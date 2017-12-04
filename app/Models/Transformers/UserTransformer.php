<?php namespace App\Models\Transformers;

use App\Models\User;
use League\Fractal;

class UserTransformer extends Fractal\TransformerAbstract
{
    public function transform(User $item)
    {
        return [
            'id' => $item->id,
            'email' => $item->email,
            'name' => $item->name,
            'created_at' => $item->created_at->toIso8601String(),
            'updated_at' => $item->updated_at->toIso8601String(),
        ];
    }
}
