<?php

namespace App\Models\Validators;

class UserValidator extends BaseValidator
{
    protected $rules = [
        'email' => 'required|email|unique:users',
        'name' => 'required',
        'password' => 'required',
    ];

    protected function getUpdateRules()
    {
        $rules = parent::getUpdateRules();

        // ignore unique if current user
        $rules['email'] = 'required|email|unique:users,email,' . $this->model->getKey();
        // do not require password
        unset($rules['password']);

        return $rules;
    }
}
