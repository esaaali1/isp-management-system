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
        'start_date',
        'end_date'
    ];

    // علاقة: الوكيل لديه العديد من المشتركين
    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}