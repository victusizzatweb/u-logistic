<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DriverRequest extends Model
{
    use HasFactory;
    protected $guarded=['id'];
    public function user()
    {
        return $this->belongsTo(User::class, 'driver_id', 'id');
    }
    
   
    public function announcement()
    {
        return $this->belongsTo(Announcements::class, 'driver_id','driver_id');
    }
    public function myAuto()
    {
        return $this->belongsTo(
            MyAuto::class, 'driver_id', 'user_id'
        );
    }
   
}
