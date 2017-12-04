<?php namespace App\Models\Transformers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BaseTransformer
{
    public function transformItem(Model $item)
    {
        return $item->toArray();
    }

    public function transformCollection(Collection $collection)
    {
        return $collection->map([$this, 'transformItem']);
    }
}
