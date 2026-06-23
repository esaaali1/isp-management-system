<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agent;
use App\Models\Client;
use Carbon\Carbon;
use App\Services\MikrotikService;

class AgentController extends Controller
{
    // عرض جميع الوكلاء (للأدمن)
    public function index()
    {
        $agents = Agent::all();
        return view('admin', compact('agents'));
    }

    // إضافة وكيل جديد
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:agents',
            'password' => 'required',
        ]);

        Agent::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => $request->password,
            'start_date' => Carbon::now()->toDateString(),
            'end_date' => Carbon::now()->addDays(30)->toDateString(),
        ]);

        return redirect()->back()->with('success', 'تم إضافة الوكيل بنجاح');
    }

    // عرض تفاصيل وكيل معين
    public function show($id)
    {
        $agent = Agent::findOrFail($id);
        return view('agent-details', compact('agent'));
    }

    // تحديث بيانات الوكيل (الاسم أو كلمة المرور)
    public function update(Request $request, $id)
    {
        $agent = Agent::findOrFail($id);
        $agent->update($request->all());
        return redirect()->back()->with('success', 'تم التحديث');
    }

    // حذف وكيل
    public function destroy($id)
    {
        Agent::destroy($id);
        return redirect()->back()->with('success', 'تم حذف الوكيل');
    }

    // تجديد اشتراك الوكيل
    public function renew($id)
    {
        $agent = Agent::findOrFail($id);
        $agent->end_date = Carbon::parse($agent->end_date)->addDays(30);
        $agent->save();
        return redirect()->back()->with('success', 'تم تجديد الاشتراك');
    }

    // ===================== API: إحصائيات الوكيل =====================
    public function stats($id, MikrotikService $mikrotik)
    {
        $agent = Agent::findOrFail($id);
        $total = Client::where('agent_id', $id)->count();
        $active = Client::where('agent_id', $id)->where('end_date', '>=', Carbon::now())->count();
        $expired = Client::where('agent_id', $id)->where('end_date', '<', Carbon::now())->count();
        
        // جلب عدد المتصلين من المايكروتيك
        $online = 0;
        try {
            $activeUsers = $mikrotik->getActiveUsers();
            $clientUsernames = Client::where('agent_id', $id)->pluck('username')->toArray();
            foreach ($activeUsers as $word) {
                if (strpos($word, '=name=') === 0) {
                    $username = substr($word, 6);
                    if (in_array($username, $clientUsernames)) {
                        $online++;
                    }
                }
            }
        } catch (\Exception $e) {
            // إذا فشل الاتصال، نترك القيمة 0
        }
        
        return response()->json([
            'total' => $total,
            'active' => $active,
            'expired' => $expired,
            'online' => $online
        ]);
    }
}