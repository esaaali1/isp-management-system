<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'username',
        'password',
        'mikrotik_host',
        'mikrotik_user',
        'mikrotik_pass',
        'mikrotik_port',
        'start_date',
        'end_date',
    ];

    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}