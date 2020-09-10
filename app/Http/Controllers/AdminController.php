<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function login(Request $request) {
        $data = $request->only(['userName','password']);

        if (Auth::attempt($data)) {
            $admin = Auth::user();
            return response([
                'currentAuthority' => empty($admin->is_admin) ? 'user' : 'admin',
                'type' => 'account',
                'status' => 'ok',
            ]);
        }

        return response([
            'status' => 'fail'
        ]);
    }

    public function getAdminInfo(){
        $admin = Auth::user();
        if (empty($admin)) {
            return response([],401);
        }
        $data  = ['username' => $admin->username,
            'name' => $admin->name,
            'avatar' => $admin->avatar,
            'email' => $admin->email,
            'phone' => $admin->phone,
            'userid' => $admin->id
        ];
        return response($data);
    }
}
