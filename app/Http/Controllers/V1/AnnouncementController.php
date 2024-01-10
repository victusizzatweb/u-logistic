<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcements;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function all(){
        $all = AnnouncementResource::collection(Announcements::where('status',1)->get());
        return $all;
    }
}
