<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * Get user
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \App\Models\User
     */
    public function get(Request $request, $id = null) {
        if (!$id) {
            return response(
                User::whereId($request->user()->id)
                    ->first(['name', 'email'])
            , 200);
        }

        $user = User::whereId($id)
            ->first(['name', 'email']);

        if (!$user) {
            return response([], 404);
        }

        return response($user, 200);
    }

}
