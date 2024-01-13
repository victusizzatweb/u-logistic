<?php

namespace App\Http\Controllers;

use App\Models\AImage;
use App\Models\Announcements;
use App\Models\Role;
use App\Models\User;
use Auth;
use App\Http\Resources\AnnouncementResource;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class AnnouncementsController extends Controller
{
    public function  __construct(){

        $this->middleware("auth:sanctum");

    }
    public function search(Request $request)
{
        $translitMap = [
                'a' => 'а', 'b' => 'б', 'v' => 'в', 'g' => 'г', 'd' => 'д',
                'e' => 'е', 'yo' => 'ё', 'zh' => 'ж', 'z' => 'з', 'i' => 'и',
                'y' => 'й', 'k' => 'к', 'l' => 'л', 'm' => 'м', 'n' => 'н',
                'o' => 'о', 'p' => 'п', 'r' => 'р', 's' => 'с', 't' => 'т',
                'u' => 'у', 'f' => 'ф', 'h' => 'х', 'ts' => 'ц', 'ch' => 'ч',
                'sh' => 'ш', 'shch' => 'щ', 'yu' => 'ю', 'ya' => 'я',
                'A' => 'А', 'B' => 'Б', 'V' => 'В', 'G' => 'Г', 'D' => 'Д',
                'E' => 'Е', 'Yo' => 'Ё', 'Zh' => 'Ж', 'Z' => 'З', 'I' => 'И',
                'Y' => 'Й', 'K' => 'К', 'L' => 'Л', 'M' => 'М', 'N' => 'Н',
                'O' => 'О', 'P' => 'П', 'R' => 'Р', 'S' => 'С', 'T' => 'Т',
                'U' => 'У', 'F' => 'Ф', 'H' => 'Х', 'Ts' => 'Ц', 'Ch' => 'Ч',
                'Sh' => 'Ш', 'Shch' => 'Щ', 'Yu' => 'Ю', 'Ya' => 'Я',
            ];
            $translitMap1 = [
                'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
                'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
                'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
                'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
                'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch',
                'ш' => 'sh', 'щ' => 'shch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
                'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
                'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
                'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I',
                'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
                'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
                'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'Ts', 'Ч' => 'Ch',
                'Ш' => 'Sh', 'Щ' => 'Shch', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '',
                'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
            ];

    $query = Announcements::query()->where('status', 1);
    $searchTerm = $request->input('search');

    $cyrillicPattern = '/\p{Cyrillic}+/u';
    $latinPattern = '/\p{Latin}+/u';

    if ($searchTerm) {
        if (preg_match($cyrillicPattern, $searchTerm)) {
            $lotin = strtr($searchTerm, $translitMap1);
            $this->applySearchTerms($query, $lotin, $searchTerm);
        } elseif (preg_match($latinPattern, $searchTerm)) {
            $ciril = strtr($searchTerm, $translitMap);
            $this->applySearchTerms($query, $ciril, $searchTerm);
        } else {
            $this->applySearchTerms($query, $searchTerm);
        }
    } else {
        $all = AnnouncementResource::collection(Announcements::where('status', 1)->get());
        return response()->json($all);
    }

    $results = $query->get();
    return response()->json(AnnouncementResource::collection($results));
}

private function applySearchTerms($query, $term1, $term2 = null)
{
    $query->where(function ($q) use ($term1, $term2) {
        $q->where('id', 'like', '%' . $term1 . '%')
            ->orWhere('name', 'like', '%' . $term1 . '%')
            ->orWhere('weight', 'like', '%' . $term1 . '%')
            ->orWhere('pick_up_address', 'like', '%' . $term1 . '%')
            ->orWhere('shipping_address', 'like', '%' . $term1 . '%')
            ->orWhere('price', 'like', '%' . $term1 . '%');

        if ($term2) {
            $q->orWhere('name', 'like', '%' . $term2 . '%')
                ->orWhere('weight', 'like', '%' . $term2 . '%')
                ->orWhere('pick_up_address', 'like', '%' . $term2 . '%')
                ->orWhere('shipping_address', 'like', '%' . $term2 . '%')
                ->orWhere('price', 'like', '%' . $term2 . '%');
        }
    });
}

   
       
    
    public function store(Request $request)
{
    $date = now();
    $time = date_format($date,'Y-m-d H:i:s');

    $request->validate([
        'name' => 'required',
        'weight' => 'required',
        'pick_up_address' => 'required',
        'shipping_address' => 'required',
    ]);

    $user_id = Auth::id();
    $role = Auth::user()->role_id;

    // Create announcement
    $announcements = Announcements::create([
        'name' => $request->name,
        'weight' => $request->weight,
        'pick_up_address' => $request->pick_up_address,
        'shipping_address' => $request->shipping_address,
        'get_latitude' => $request->get_latitude,
        'get_longitude' => $request->get_longitude,
        'to_go_latitude' => $request->to_go_latitude,
        'to_go_longitude' => $request->to_go_longitude,
        'description' => $request->description,
        'price' => $request->price,
        'time' => $time,
        'user_id' => $user_id,
        'role_id' => $role,
        'status' => 1,
    ]);

    // Check if images are present
    if ($request->hasFile('images')) {
        $imageRules = [
            'images.*' => 'image',
        ];

        $imageValidator = Validator::make($request->all(), $imageRules);

        if ($imageValidator->fails()) {
            return response()->json([
                'messages' => $imageValidator->messages(),
                'error' => 403,
            ]);
        }
        foreach ($request->file('images') as $image) {
            $path = md5(rand(1111, 9999) . microtime()) . "." . $image->extension();
            $image->storeAs('public/announcements/', $path);
            if ($path) {
                AImage::create([
                    'a_id' => $announcements->id,
                    'path' => $path,
                ]);
            } else {
                return response()->json([
                    'error' => 'File upload failed',
                ]);
            }
        }
    }
    $images = AImage::where('a_id', $announcements->id)->pluck('path')->toArray();

    if ($announcements) {
        $combinedData = $announcements->toArray() + ['images' => $images];
        return response()->json([
            'message' => 'Create successfully',
            'data' => $combinedData,
        ]);
    } else {
        return response()->json([
            'error' => 'Announcement not found',
        ], 403);
    }
}
public function show(Announcements $announcements, $id)
{
    $announcement = Announcements::where('id',$id)->with('user')->first();
    if (!$announcement) {
        return response()->json([
            'error' => 'Not found'
        ], 404);
    }
    $responseData = [
        "announcement" => new AnnouncementResource($announcement)
    ];

    return response()->json($responseData);
}


    public function update(Request $request, Announcements $announcements, $id)
    {
    $announcements = Announcements::find($id);
    $date = now();
    $user_id = Auth::id();
    $role = Auth::user()->role_id;

    $request->validate([
        'name' => 'required',
        'weight' => 'required',
        'pick_up_address' => 'required',
        'shipping_address' => 'required',
        'description' => 'required',
        'get_latitude' => 'required',
        'get_longitude' => 'required',
        'to_go_latitude' => 'required',
        'to_go_longitude' => 'required',
    ]);

    if ($request->has('images')) {
        $imageRules = [
            'images.*' => 'image|max:2048',
        ];

        $imageValidator = Validator::make($request->all(), $imageRules);

        if ($imageValidator->fails()) {
            return response()->json([
                'error' => 403,
                'messages' => $imageValidator->messages(),
            ]);
        }

        $images = AImage::where('a_id', $id)->get();

        foreach ($images as $Aimage) {
            File::delete(public_path('storage/announcements/'.$Aimage->path));
        }

        AImage::where('a_id', $id)->delete();

        foreach ($request->file('images') as $image) {
            $path = md5(rand(1111, 9999) . microtime()) . "." . $image->extension();
            $image->storeAs('public/announcements/', $path);

            AImage::create([
                'a_id' => $id,
                'path' => $path,
            ]);
        }
    }
    // dd($request);
    $announcements->update([
        'name' => $request->name,
        'weight' => $request->weight,
        'pick_up_address' => $request->pick_up_address,
        'shipping_address' => $request->shipping_address,
        'get_latitude' => $request->get_latitude,
        'get_longitude' => $request->get_longitude,
        'to_go_latitude' => $request->to_go_latitude,
        'to_go_longitude' => $request->to_go_longitude,
        'description' => $request->description,
        'price'=>$request->price,
        'date' => $date,
        'user_id' => $user_id,
        'role_id' => $role,
        'status' => '1',
    ]);
    $images = AImage::where('a_id', $announcements->id)->pluck('path')->toArray();
        if ($announcements && $images) {
            $combinedData = $announcements->toArray() + ['images' => $images];
            
            return response()->json([ 
                'data' => $combinedData,
            ]);
        } else {
            return response()->json([
                'error' => 'Announcement or images not found',
            ],403);
        }
}


    public function customer_announcements(Announcements $announcements ,$id)
    { 
       $user = Auth::id();
      
       if( $user && $id == 2){
        $result =  AnnouncementResource::collection(Announcements::where('user_id',$user)->where('status',2)->get());
        return response()->json([
            "data"=>$result
        ],200);
    }
        elseif( $user && $id == 3){
        $result = AnnouncementResource::collection(Announcements::where('user_id',$user)->where('status',4)->get());
        return response()->json([
            "data"=>$result
        ],200);
       }elseif($user && $id == 1){
        $result = AnnouncementResource::collection(Announcements::where('user_id',$user)->get());
        return response()->json([
            "data"=>$result
        ],200);
       }
    }
    public function destroy($id)
{
    // Find the announcement by ID
    $announcement = Announcements::find($id);

    // Check if the announcement exists
    if (!$announcement) {
        return response()->json([
            'error' => 'Announcement not found',
        ], 404);
    }
    $userRole = Auth::user()->role_id;
    if ($userRole != '2') {
        AImage::where('a_id', $announcement->id)->delete();
        $announcement->delete();

        return response()->json([
            'message' => 'Announcement and associated images deleted successfully',
        ]);
    } else {
        return response()->json([
            'error' => 'You do not have permission to delete announcements',
        ], 403);
    }
}
   
    
}
