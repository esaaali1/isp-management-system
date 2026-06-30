<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agent;
use App\Models\Client;
use Carbon\Carbon;
use App\Services\MikrotikService;

class AgentController extends Controller
{
    public function index()
    {
        $agents = Agent::all();
        return view('admin', compact('agents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:agents',
            'password' => 'required',
            'mikrotik_host' => 'nullable|string',
            'mikrotik_user' => 'nullable|string',
            'mikrotik_pass' => 'nullable|string',
            'mikrotik_port' => 'nullable|integer',
        ]);

        Agent::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => $request->password,
            'mikrotik_host' => $request->mikrotik_host,
            'mikrotik_user' => $request->mikrotik_user,
            'mikrotik_pass' => $request->mikrotik_pass,
            'mikrotik_port' => $request->mikrotik_port ?? 8728,
            'start_date' => Carbon::now()->toDateString(),
            'end_date' => Carbon::now()->addDays(30)->toDateString(),
        ]);

        return redirect()->back()->with('success', 'تم إضافة الوكيل بنجاح');
    }

    public function show($id)
    {
        $agent = Agent::findOrFail($id);
        return view('agent-details', compact('agent'));
    }

    public function update(Request $request, $id)
    {
        $agent = Agent::findOrFail($id);
        $agent->update($request->all());
        return redirect()->back()->with('success', 'تم التحديث');
    }

    public function destroy($id)
    {
        Agent::destroy($id);
        return redirect()->back()->with('success', 'تم حذف الوكيل');
    }

    public function renew($id)
    {
        $agent = Agent::findOrFail($id);
        $agent->end_date = Carbon::parse($agent->end_date)->addDays(30);
        $agent->save();
        return redirect()->back()->with('success', 'تم تجديد الاشتراك');
    }

    public function stats($id, MikrotikService $mikrotik)
    {
        $agent = Agent::findOrFail($id);
        $total = Client::where('agent_id', $id)->count();
        $active = Client::where('agent_id', $id)->where('end_date', '>=', Carbon::now())->count();
        $expired = Client::where('agent_id', $id)->where('end_date', '<', Carbon::now())->count();
        
        $online = 0;
        try {
            // ✅ تمرير $agent إلى getActiveUsers
            $activeUsers = $mikrotik->getActiveUsers($agent);
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
            // تجاهل
        }
        
        return response()->json([
            'total' => $total,
            'active' => $active,
            'expired' => $expired,
            'online' => $online
        ]);
    }
}