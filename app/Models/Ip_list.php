<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ip_list extends Model
{
    use HasFactory;
    protected $fillable = [
        'full_address',
        'mac_address',
        'phone_model',
        'last_seen',
    ];
}
