<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Agent;
use Carbon\Carbon;
use App\Services\MikrotikService;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    public function index($agentId)
    {
        $agent = Agent::findOrFail($agentId);
        $clients = Client::where('agent_id', $agentId)->get();
        return view('agent-clients-all', compact('clients', 'agent'));
    }

    public function expired($agentId)
    {
        $agent = Agent::findOrFail($agentId);
        $clients = Client::where('agent_id', $agentId)
                         ->where('end_date', '<', Carbon::now())
                         ->get();
        return view('agent-clients-expired', compact('clients', 'agent'));
    }

    public function store(Request $request, MikrotikService $mikrotik)
    {
        Log::info('بيانات الإضافة:', $request->all());

        try {
            $validated = $request->validate([
                'agent_id' => 'required|exists:agents,id',
                'fullname' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:clients',
                'password' => 'required|string|min:4',
                'package' => 'required|in:Economy,Standard,Business',
            ]);

            // جلب بيانات الوكيل
            $agent = Agent::findOrFail($request->agent_id);
            
            // التأكد من أن الوكيل لديه بيانات مايكروتيك
            if (empty($agent->mikrotik_host) || empty($agent->mikrotik_user) || empty($agent->mikrotik_pass)) {
                return redirect()->back()->with('error', 'هذا الوكيل ليس لديه بيانات مايكروتيك مكتملة.')->withInput();
            }

            Log::info('تم التحقق بنجاح');

            // إنشاء المستخدم في مايكروتيك الوكيل
            try {
                $result = $mikrotik->createPppoeUser(
                    $request->username,
                    $request->password,
                    $request->package,
                    $agent  // تمرير بيانات الوكيل
                );
                
                Log::info('نتيجة المايكروتيك:', $result);

                if (in_array('!trap', $result)) {
                    $errorMsg = '';
                    foreach ($result as $word) {
                        if (strpos($word, '=message=') === 0) {
                            $errorMsg = substr($word, 9);
                            break;
                        }
                    }
                    return redirect()->back()->with('error', 'فشل إنشاء المستخدم في المايكروتيك: ' . $errorMsg)->withInput();
                }
            } catch (\Exception $e) {
                Log::error('خطأ في المايكروتيك:', ['message' => $e->getMessage()]);
                return redirect()->back()->with('error', 'خطأ في الاتصال بالمايكروتيك: ' . $e->getMessage())->withInput();
            }

            // حفظ المشترك في قاعدة البيانات
            $client = Client::create([
                'agent_id' => $request->agent_id,
                'fullname' => $request->fullname,
                'username' => $request->username,
                'password' => $request->password,
                'package' => $request->package,
                'start_date' => Carbon::now()->toDateString(),
                'end_date' => Carbon::now()->addDays(30)->toDateString(),
            ]);

            Log::info('تم حفظ المشترك في قاعدة البيانات:', ['id' => $client->id]);

            return redirect()->back()->with('success', 'تم إضافة المشترك بنجاح على المايكروتيك وقاعدة البيانات');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('فشل التحقق:', $e->errors());
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('خطأ غير متوقع:', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'حدث خطأ غير متوقع: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id, MikrotikService $mikrotik)
    {
        $client = Client::findOrFail($id);
        $agent = Agent::findOrFail($client->agent_id);
        
        // جلب IP المتصل (إذا كان موجوداً)
        $clientIp = null;
        try {
            $clientIp = $mikrotik->getClientIP($client->username, $agent);
        } catch (\Exception $e) {
            // إذا فشل الاتصال بالمايكروتيك، نترك الـ IP فارغاً
        }

        return view('agent-client-details', compact('client', 'clientIp'));
    }

    public function update(Request $request, $id, MikrotikService $mikrotik)
    {
        $client = Client::findOrFail($id);
        
        if ($request->has('password') && $request->password != $client->password) {
            try {
                $mikrotik->changePppoePassword($client->username, $request->password, $client->agent);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'فشل تغيير كلمة المرور في المايكروتيك');
            }
        }
        
        if ($request->has('package') && $request->package != $client->package) {
            try {
                $mikrotik->changePppoeProfile($client->username, $request->package, $client->agent);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'فشل تغيير الباقة في المايكروتيك');
            }
        }
        
        if ($request->has('end_date') && $request->end_date != $client->end_date) {
            $client->end_date = $request->end_date;
        }
        
        $client->update($request->all());
        
        return redirect()->back()->with('success', 'تم تحديث بيانات المشترك');
    }

    public function destroy($id, MikrotikService $mikrotik)
    {
        $client = Client::findOrFail($id);
        $agent = Agent::findOrFail($client->agent_id);
        
        try {
            $mikrotik->deletePppoeUser($client->username, $agent);
        } catch (\Exception $e) {
            // تجاهل
        }

        $client->delete();
        return redirect()->back()->with('success', 'تم حذف المشترك');
    }

    public function renew($id, MikrotikService $mikrotik)
    {
        $client = Client::findOrFail($id);
        $agent = Agent::findOrFail($client->agent_id);
        
        $client->end_date = Carbon::parse($client->end_date)->addDays(30);
        $client->save();
        
        try {
            $mikrotik->enablePppoeUser($client->username, $agent);
        } catch (\Exception $e) {
            // تجاهل
        }
        
        return redirect()->back()->with('success', 'تم تجديد الاشتراك 30 يوماً');
    }
}