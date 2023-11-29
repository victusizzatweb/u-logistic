<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index():Collection
    {
       return Role ::all();
    }

    public function store(StoreRoleRequest $request)
    {
        
            $request->validate([
                'name' => 'required|string|unique:roles'
            ]);
    
            $role = Role::create([
                'name' => $request->name
            ]);
    
            return response()->json([
                'message' => 'Role created successfully',
                'data' => $role
            ], 201);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return $role;
    }

    /**
     * Show the form for editing the specified resource.
     */
    
     public function update(Request $request,$id)
     {
       
         $request->validate([
             'name' => 'required'
         ]);
        //  dd($request);
     
         $role = Role::findOrFail($request->id);
         $role->name = $request->name;
         $role->save();
     
         return response()->json([
             'message' => 'Role updated successfully',
             'data' => $role
         ], 200);
     }
     public function destroy($id)
{
    $role = Role::findOrFail($id);
    $role->delete();

    return response()->json([
        'message' => 'Role deleted successfully',
    ], 200);
}

}
