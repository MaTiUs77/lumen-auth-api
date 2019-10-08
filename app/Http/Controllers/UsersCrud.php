<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Input;
use Spatie\Permission\Models\Permission;

class UsersCrud extends Controller
{
    public function all() {
        $find = Input::get('find');
        if(empty($find)) {
            $users = User::paginate();
        } else {
            $users = User::where('username',$find)
                ->orWhere('role',$find)
                ->paginate();
        }
        return $users;
    }

    public function show($id) {
        return User::findOrFail($id);
    }

    public function add() {
/*        $name = Input::get('name');
        $role = User::create(['name' => $name]);
        return $role;*/
    }

    public function delete() {
/*        $name = Input::get('name');
        $role = Permission::findByName($name);
        $role->delete();
        return $role;*/
    }

}
