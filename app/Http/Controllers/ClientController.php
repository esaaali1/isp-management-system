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

    /**
     * إضافة مشترك جديد (مع حفظ في قاعدة البيانات بشكل مضمون)
     */
    public function store(Request $request, MikrotikService $mikrotik)
    {
        // 1. التحقق من صحة البيانات
        $validated = $request->validate([
            'agent_id' => 'required|exists:agents,id',
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:clients',
            'password' => 'required|string|min:4',
            'package' => 'required|in:Economy,Standard,Business',
        ]);

        // 2. جلب بيانات الوكيل
        $agent = Agent::findOrFail($request->agent_id);

        // 3. التأكد من أن الوكيل لديه بيانات مايكروتيك
        if (empty($agent->mikrotik_host) || empty($agent->mikrotik_user) || empty($agent->mikrotik_pass)) {
            return redirect()->back()->with('error', 'هذا الوكيل ليس لديه بيانات مايكروتيك مكتملة.')->withInput();
        }

        // 4. إنشاء المستخدم في المايكروتيك
        try {
            $mikrotik->createPppoeUser(
                $request->username,
                $request->password,
                $request->package,
                $agent
            );
        } catch (\Exception $e) {
            Log::error('خطأ في المايكروتيك:', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'فشل إنشاء المستخدم في المايكروتيك: ' . $e->getMessage())->withInput();
        }

        // 5. حفظ المشترك في قاعدة البيانات (الجزء الأهم)
        try {
            $client = Client::create([
                'agent_id' => $request->agent_id,
                'fullname' => $request->fullname,
                'username' => $request->username,
                'password' => $request->password,
                'package' => $request->package,
                'start_date' => Carbon::now()->toDateString(),
                'end_date' => Carbon::now()->addDays(30)->toDateString(),
            ]);

            Log::info('تم حفظ المشترك في قاعدة البيانات:', ['id' => $client->id, 'username' => $client->username]);

        } catch (\Exception $e) {
            Log::error('فشل حفظ المشترك في قاعدة البيانات:', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'فشل حفظ المشترك في قاعدة البيانات: ' . $e->getMessage())->withInput();
        }

        return redirect()->back()->with('success', 'تم إضافة المشترك بنجاح على المايكروتيك وقاعدة البيانات');
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
        $agent = Agent::findOrFail($client->agent_id);
        
        if ($request->has('password') && $request->password != $client->password) {
            try {
                $mikrotik->changePppoePassword($client->username, $request->password, $agent);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'فشل تغيير كلمة المرور في المايكروتيك');
            }
        }
        
        if ($request->has('package') && $request->package != $client->package) {
            try {
                $mikrotik->changePppoeProfile($client->username, $request->package, $agent);
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
    /**
 * عرض المشتركين النشطين (لم تنتهِ اشتراكاتهم)
 */
public function active($agentId)
{
    $agent = Agent::findOrFail($agentId);
    $clients = Client::where('agent_id', $agentId)
                     ->where('end_date', '>=', Carbon::now())
                     ->get();
    return view('agent-clients-active', compact('clients', 'agent'));
}

/**
 * عرض المشتركين المتصلين حالياً (من المايكروتيك)
 */
public function online($agentId, MikrotikService $mikrotik)
{
    $agent = Agent::findOrFail($agentId);
    $onlineUsernames = [];
    
    try {
        $activeUsers = $mikrotik->getActiveUsers($agent);
        foreach ($activeUsers as $word) {
            if (strpos($word, '=name=') === 0) {
                $onlineUsernames[] = substr($word, 6);
            }
        }
    } catch (\Exception $e) {
        // إذا فشل الاتصال بالمايكروتيك، نترك القائمة فارغة
    }

    $clients = Client::where('agent_id', $agentId)
                     ->whereIn('username', $onlineUsernames)
                     ->get();
    
    return view('agent-clients-online', compact('clients', 'agent'));
}
}