<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver_license extends Model
{
    use HasFactory;
    protected $guarded=['id'];
    protected $fillable = [
        'user_id',
        'categories',
        'certificate_number',
        'image',
    ];
}
