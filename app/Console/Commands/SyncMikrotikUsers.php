<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Services\MikrotikService;
use Carbon\Carbon;

class SyncMikrotikUsers extends Command
{
    protected $signature = 'mikrotik:sync';
    protected $description = 'مزامنة حالة المستخدمين مع المايكروتيك (تفعيل/تعطيل)';

    public function handle(MikrotikService $mikrotik)
    {
        $clients = Client::all();
        $now = Carbon::now();
        $updated = 0;

        $this->info("🔄 بدء مزامنة المستخدمين مع المايكروتيك...");

        foreach ($clients as $client) {
            try {
                if ($client->end_date < $now) {
                    $mikrotik->disablePppoeUser($client->username);
                    $this->line("🔴 تم تعطيل: {$client->username}");
                    $updated++;
                } else {
                    $mikrotik->enablePppoeUser($client->username);
                    $this->line("🟢 تم تفعيل: {$client->username}");
                }
            } catch (\Exception $e) {
                $this->error("❌ فشل تحديث: {$client->username} - " . $e->getMessage());
            }
        }

        $this->info("✅ تم تحديث {$updated} مستخدم منتهي الاشتراك");
    }
}
