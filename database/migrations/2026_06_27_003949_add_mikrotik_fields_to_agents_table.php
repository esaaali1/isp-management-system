<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->string('mikrotik_host')->nullable()->after('password');
            $table->string('mikrotik_user')->nullable()->after('mikrotik_host');
            $table->string('mikrotik_pass')->nullable()->after('mikrotik_user');
            $table->integer('mikrotik_port')->default(8728)->after('mikrotik_pass');
        });
    }

    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn(['mikrotik_host', 'mikrotik_user', 'mikrotik_pass', 'mikrotik_port']);
        });
    }
};