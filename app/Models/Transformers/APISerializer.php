<?php namespace App\Models\Transformers;

use Illuminate\Support\Str;
use League\Fractal\Serializer\DataArraySerializer;

class APISerializer extends DataArraySerializer
{
    public function item($resourceKey, array $data)
    {
        $data = $this->transformItem($data);

        return ['data' => $data];
    }

    public function collection($resourceKey, array $data)
    {
        $data = array_map(function($item) {
            return $this->transformItem($item);
        }, $data);

        return ['data' => $data];
    }

    protected function transformItem($item)
    {
        $includes = [];

        foreach ($item as $key => $value) {
            if (Str::startsWith($key, 'relation_')) {
                $includes[substr($key, 9)] = $value;
                unset($item[$key]);
            }
        }

        if (!empty($includes)) {
            $item['_includes'] = $includes;
        }

        return $item;
    }
}
