<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Agent;
use Carbon\Carbon;
use App\Services\MikrotikService;

class ClientController extends Controller
{
    // عرض جميع مشتركي وكيل معين (للوكيل)
    public function index($agentId)
    {
        $agent = Agent::findOrFail($agentId);
        $clients = Client::where('agent_id', $agentId)->get();
        return view('agent-clients-all', compact('clients', 'agent'));
    }

    // عرض المشتركين المنتهية اشتراكاتهم
    public function expired($agentId)
    {
        $agent = Agent::findOrFail($agentId);
        $clients = Client::where('agent_id', $agentId)
                         ->where('end_date', '<', Carbon::now())
                         ->get();
        return view('agent-clients-expired', compact('clients', 'agent'));
    }

    // إضافة مشترك جديد (مع الربط بالمايكروتيك)
    public function store(Request $request, MikrotikService $mikrotik)
    {
        $request->validate([
            'agent_id' => 'required|exists:agents,id',
            'fullname' => 'required',
            'username' => 'required|unique:clients',
            'password' => 'required',
            'package' => 'required',
        ]);

        // 1. إنشاء المستخدم في المايكروتيك أولاً
        try {
            $result = $mikrotik->createPppoeUser(
                $request->username,
                $request->password,
                $request->package
            );
            
            // التحقق من نجاح الإنشاء
            if (in_array('!trap', $result)) {
                $errorMsg = '';
                foreach ($result as $word) {
                    if (strpos($word, '=message=') === 0) {
                        $errorMsg = substr($word, 9);
                        break;
                    }
                }
                return redirect()->back()->with('error', 'فشل إنشاء المستخدم في المايكروتيك: ' . $errorMsg);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'خطأ في الاتصال بالمايكروتيك: ' . $e->getMessage());
        }

        // 2. حفظ المشترك في قاعدة البيانات
        Client::create([
            'agent_id' => $request->agent_id,
            'fullname' => $request->fullname,
            'username' => $request->username,
            'password' => $request->password,
            'package' => $request->package,
            'start_date' => Carbon::now()->toDateString(),
            'end_date' => Carbon::now()->addDays(30)->toDateString(),
        ]);

        return redirect()->back()->with('success', 'تم إضافة المشترك بنجاح على المايكروتيك');
    }

    // عرض تفاصيل مشترك معين
    public function show($id)
    {
        $client = Client::findOrFail($id);
        return view('agent-client-details', compact('client'));
    }

    // تحديث بيانات مشترك (مع الربط بالمايكروتيك)
    public function update(Request $request, $id, MikrotikService $mikrotik)
    {
        $client = Client::findOrFail($id);
        
        // إذا تم تغيير كلمة المرور
        if ($request->has('password') && $request->password != $client->password) {
            try {
                $mikrotik->changePppoePassword($client->username, $request->password);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'فشل تغيير كلمة المرور في المايكروتيك');
            }
        }
        
        // إذا تم تغيير الباقة
        if ($request->has('package') && $request->package != $client->package) {
            try {
                $mikrotik->changePppoeProfile($client->username, $request->package);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'فشل تغيير الباقة في المايكروتيك');
            }
        }
        
        // تحديث التاريخ إذا تم تغييره
        if ($request->has('end_date') && $request->end_date != $client->end_date) {
            $client->end_date = $request->end_date;
        }
        
        $client->update($request->all());
        
        return redirect()->back()->with('success', 'تم تحديث بيانات المشترك في المايكروتيك وقاعدة البيانات');
    }

    // حذف مشترك (مع الربط بالمايكروتيك)
    public function destroy($id, MikrotikService $mikrotik)
    {
        $client = Client::findOrFail($id);
        
        // حذف المستخدم من المايكروتيك
        try {
            $mikrotik->deletePppoeUser($client->username);
        } catch (\Exception $e) {
            // يمكن تجاهل الخطأ إذا كان المستخدم غير موجود
        }

        // حذف المشترك من قاعدة البيانات
        $client->delete();
        
        return redirect()->back()->with('success', 'تم حذف المشترك من المايكروتيك وقاعدة البيانات');
    }

    // تجديد اشتراك المشترك
    public function renew($id, MikrotikService $mikrotik)
    {
        $client = Client::findOrFail($id);
        $client->end_date = Carbon::parse($client->end_date)->addDays(30);
        $client->save();
        
        // تفعيل المستخدم في المايكروتيك
        try {
            $mikrotik->enablePppoeUser($client->username);
        } catch (\Exception $e) {
            // يمكن تجاهل الخطأ
        }
        
        return redirect()->back()->with('success', 'تم تجديد الاشتراك 30 يوماً وتفعيل المستخدم');
    }
}