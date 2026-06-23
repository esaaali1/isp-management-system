<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'fullname',
        'username',
        'password',
        'package',
        'start_date',
        'end_date'
    ];

    // علاقة: المشترك تابع لوكيل واحد
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}