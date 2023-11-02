<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Database\Eloquent\Collection;

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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return $role;
        //fjfj
    }

    /**
     * Show the form for editing the specified resource.
     */
    
    public function update(UpdateRoleRequest $request, Role $role)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        //
    }
}
