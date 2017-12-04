<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\APIController;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends ApiController
{
    public function authenticate()
    {
        return JWTAuth::authenticate();
    }

    public function index()
    {
        return User::all();
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['Not found'], Response::HTTP_NOT_FOUND);
        }

        return $user;
    }

    public function create(Request $request)
    {
        $user = User::create($request->all());

        return response()->json($user, Response::HTTP_CREATED);
    }

    public function update(Request $request, User $user)
    {
        $user->update($request->all());

        return response()->json($user, Response::HTTP_OK);
    }

    public function delete(User $user)
    {
        $user->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
