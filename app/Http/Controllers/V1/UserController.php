<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use App\Models\Transformers\UserTransformer;
use App\Models\Validators\UserValidator;
use App\Http\Controllers\ApiController;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends ApiController
{
    protected $modelClass = User::class;
    protected $transformerClass = UserTransformer::class;
    protected $validatorClass = UserValidator::class;

    public function authenticate()
    {
        return JWTAuth::authenticate();
    }

    public function show($id)
    {
        if ($id === 'me') {
            $id = JWTAuth::authenticate()->id;
        }

        return parent::show($id);
    }

    public function update($id)
    {
        if ($id === 'me') {
            $id = JWTAuth::authenticate()->id;
        }

        return parent::update($id);
    }
}
