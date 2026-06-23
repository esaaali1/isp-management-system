<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\ClientController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return view('login');
});

// API Routes
Route::post('/api/agent-login', function (Request $request) {
    $agent = \App\Models\Agent::where('username', $request->username)
                              ->where('password', $request->password)
                              ->first();
    
    if ($agent) {
        session(['agent_id' => $agent->id]);
        return response()->json([
            'success' => true,
            'agent' => $agent
        ]);
    } else {
        return response()->json([
            'success' => false
        ]);
    }
});

Route::get('/api/agent-stats/{id}', [AgentController::class, 'stats']);

// Routes الأدمن
Route::get('/admin', [AgentController::class, 'index'])->name('admin.index');
Route::post('/admin/agents', [AgentController::class, 'store'])->name('agents.store');
Route::get('/admin/agent/{id}', [AgentController::class, 'show'])->name('agents.show');
Route::put('/admin/agent/{id}', [AgentController::class, 'update'])->name('agents.update');
Route::get('/admin/agent/{id}/renew', [AgentController::class, 'renew'])->name('agents.renew');
Route::delete('/admin/agent/{id}', [AgentController::class, 'destroy'])->name('agents.destroy');

// Routes الوكيل
Route::get('/agent/dashboard', function () {
    return view('agent-dashboard');
})->name('agent.dashboard');

Route::get('/agent/clients/all/{agentId}', [ClientController::class, 'index'])->name('clients.all');
Route::get('/agent/clients/expired/{agentId}', [ClientController::class, 'expired'])->name('clients.expired');
Route::post('/agent/clients', [ClientController::class, 'store'])->name('clients.store');
Route::get('/agent/client/{id}', [ClientController::class, 'show'])->name('clients.show');
Route::put('/agent/client/{id}', [ClientController::class, 'update'])->name('clients.update');
Route::get('/agent/client/{id}/renew', [ClientController::class, 'renew'])->name('clients.renew');
Route::delete('/agent/client/{id}', [ClientController::class, 'destroy'])->name('clients.destroy');

// Routes الاختبار
Route::get('/test-mikrotik', function () {
    $mikrotik = new \App\Services\MikrotikService();
    return $mikrotik->testConnection();
});

Route::get('/test-create-user', function () {
    $host = '192.168.88.1';
    $port = 8728;
    $user = 'admin';
    $pass = '1998';

    $socket = @fsockopen($host, $port, $errno, $errstr, 10);
    if (!$socket) {
        return "❌ فشل الاتصال: $errstr ($errno)";
    }

    $output = "✅ تم الاتصال بالمايكروتيك<br>";

    $readWord = function($socket) {
        $c = fread($socket, 1);
        if ($c === false || strlen($c) === 0) return false;
        $c = ord($c);
        if ($c < 0x80) {
            $len = $c;
        } elseif ($c < 0xC0) {
            $c2 = fread($socket, 1);
            if ($c2 === false || strlen($c2) === 0) return false;
            $len = (($c & 0x3F) << 8) + ord($c2);
        } else {
            fread($socket, 1);
            $c2 = fread($socket, 1);
            $c3 = fread($socket, 1);
            $c4 = fread($socket, 1);
            if ($c2 === false || $c3 === false || $c4 === false) return false;
            $len = (($c & 0x3F) << 24) + (ord($c2) << 16) + (ord($c3) << 8) + ord($c4);
        }
        if ($len == 0) return '';
        $data = fread($socket, $len);
        return ($data === false) ? false : $data;
    };

    $sendWord = function($socket, $word) {
        $len = strlen($word);
        if ($len < 0x80) {
            fwrite($socket, chr($len));
        } elseif ($len < 0x4000) {
            fwrite($socket, chr(($len >> 8) | 0x80));
            fwrite($socket, chr($len & 0xFF));
        } else {
            fwrite($socket, chr(0x80));
            fwrite($socket, chr(($len >> 16) & 0xFF));
            fwrite($socket, chr(($len >> 8) & 0xFF));
            fwrite($socket, chr($len & 0xFF));
        }
        fwrite($socket, $word);
    };

    $sendSentence = function($socket, $words) use ($sendWord) {
        foreach ($words as $word) {
            $sendWord($socket, $word);
        }
        $sendWord($socket, '');
    };

    $readReply = function($socket) use ($readWord) {
        $reply = [];
        while (true) {
            $word = $readWord($socket);
            if ($word === false) {
                $reply[] = '!fatal';
                break;
            }
            if ($word === '') continue;
            $reply[] = $word;
            if ($word === '!done' || $word === '!trap') break;
        }
        return $reply;
    };

    $sendSentence($socket, ['/login', "=name=$user", "=password=$pass"]);
    $reply = $readReply($socket);
    if (!in_array('!done', $reply)) {
        return "❌ فشل تسجيل الدخول";
    }

    $output .= "✅ تم تسجيل الدخول<br>";
    $testUsername = 'test_' . time();
    $output .= "🔧 محاولة إنشاء مستخدم: $testUsername<br>";
    
    $sendSentence($socket, [
        '/ppp/secret/add',
        "=name=$testUsername",
        "=password=123456",
        "=service=pppoe",
        "=profile=default"
    ]);
    
    $reply = $readReply($socket);
    $output .= "📨 الرد من المايكروتيك: <pre>" . print_r($reply, true) . "</pre>";

    fclose($socket);
    return $output;
});