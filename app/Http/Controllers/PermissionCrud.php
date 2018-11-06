<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Spatie\Permission\Models\Permission;

class PermissionCrud extends Controller
{
    public function __construct()
    {
    }

    public function all() {
        return Permission::all();
    }

    public function add() {
        $name = Input::get('name');
        $role = Permission::create(['name' => $name]);
        return $role;
    }

    public function delete() {
        $name = Input::get('name');
        $role = Permission::findByName($name);
        $role->delete();
        return $role;
    }

}
