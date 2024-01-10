<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyAuto extends Model
{
    use HasFactory;
    protected $guarded = ["id"];
    protected $fillable = ['user_id','image', 'transport_number', 'transport_model', 'transport_capacity'];
}
