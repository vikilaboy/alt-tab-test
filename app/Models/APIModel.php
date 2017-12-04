<?php namespace App\Models;

use App\Models\Validators\BaseValidator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class APIModel extends Model
{
    protected $isValid = true;
    protected $validationErrors;
    protected static $apiWith = [];
    protected $updatableRelations = [];
    protected $relationsAttributes = [];
    protected $dirtyRelations = [];
    protected $dirtyAttributes = [];

    public static function create(array $attributes = [])
    {
        $model = new static;

        if ($model->validate($attributes)) {
            $model->fill($attributes);

            $model->save();
        }

        return $model;
    }

    public function update(array $attributes = [])
    {
        if (!$this->validate($attributes)) {
            return false;
        }

        return parent::update($attributes);
    }

    public function save(array $options = [])
    {
        $this->dirtyAttributes = array_keys($this->getDirty());

        $saved = parent::save($options);

        if ($saved) {
            $this->updateRelations();
            $this->dirtyAttributes = array_merge($this->dirtyAttributes, $this->dirtyRelations);
        }

        if (count($this->dirtyAttributes) > 0) {
            $this->fireModelEvent('updated_with_relations');
        }

        return $saved;
    }

    protected function validate(array $attributes) {
        $validatorClass = $this->getValidatorClass();

        if (class_exists($validatorClass)) {
            /** @var BaseValidator $validator */
            $validator = new $validatorClass;

            $this->isValid = $validator->validate($attributes, $this);

            if (!$this->isValid) {
                $this->validationErrors = $validator->errors();
                return false;
            }
        }

        return true;
    }

    public function setAttribute($key, $value)
    {
        $result = parent::setAttribute($key, $value);

        if (in_array($key, $this->getUpdatableRelations())) {
            $this->relationsAttributes[$key] = $this->attributes[$key];
            unset($this->attributes[$key]);
        }

        return $result;
    }

    protected function updateRelations() {
        $this->dirtyRelations = [];

        foreach ($this->getUpdatableRelations() as $relation) {
            if (Arr::has($this->relationsAttributes, $relation)) {
                $changes = $this->$relation()->sync($this->relationsAttributes[$relation]);
                if (count($changes['attached']) > 0 ||
                    count($changes['detached']) > 0 ||
                    count($changes['updated']) > 0) {
                    array_push($this->dirtyRelations, $relation);
                }
            }
        }
    }

    protected function getUpdatableRelations()
    {
        return $this->updatableRelations;
    }

    public function getDirtyRelations()
    {
        return $this->dirtyRelations;
    }

    public function getDirtyAttributes()
    {
        return $this->dirtyAttributes;
    }

    public function isValid()
    {
        return $this->isValid;
    }

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    public static function getAPIWith()
    {
        return static::$apiWith;
    }

    protected function getValidatorClass()
    {
        $modelClass = get_short_class(get_called_class());

        return 'App\\Models\\Validators\\' . $modelClass . 'Validator';
    }
}
