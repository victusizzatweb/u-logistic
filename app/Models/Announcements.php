<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcements extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $timestamps = false;
    public function images(){
        return $this->hasMany(Apimage::class,'p_id');
    }
}
