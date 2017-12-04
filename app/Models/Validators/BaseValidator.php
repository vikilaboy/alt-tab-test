<?php

namespace App\Models\Validators;

use App\Models\APIModel;
use Illuminate\Validation\Validator;
use Validator as ValidatorFacade;

class BaseValidator
{
    protected $rules = [];
    protected $validator;
    protected $data;
    /** @var  APIModel */
    protected $model;

    public function validate($data, APIModel $model)
    {
        $this->data = $data;
        $this->model = $model;

        $rules = $model->exists ? $this->getUpdateRules() : $this->getCreateRules();

        $this->validator = ValidatorFacade::make($data, $rules);

        $this->validator->after([$this, 'customValidate']);

        return $this->validator->passes();
    }

    public function errors()
    {
        return $this->validator->errors();
    }

    protected function getCreateRules()
    {
        return $this->rules;
    }

    protected function getUpdateRules()
    {
        return $this->getCreateRules();
    }

    public function customValidate(Validator $validator)
    {
    }
}
