<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Announcements extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $timestamps = false;
    public function images()
    {
        return $this->hasMany(AImage::class, 'a_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
   
    
    public function driverRequest()
    {
        return $this->hasOne(DriverRequest::class, 'driver_id', 'driver_id');
    }
}
