<?php

namespace App\Http\Controllers;

use App\Models\APIModel;
use App\Models\Transformers\APISerializer;
use App\Models\Transformers\BaseTransformer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use League\Fractal\Manager as FractalManager;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Resource\Item as FractalItem;
use Tymon\JWTAuth\JWTAuth;


class ApiController extends Controller
{
    protected $request;
    protected $user;
    protected $modelClass;
    protected $transformerClass;
    protected $validatorClass;
    protected $requestData;

    public function __construct(Request $request, JWTAuth $auth)
    {
        $this->request = $request;
        $this->user = $auth->authenticate();
        $this->requestData = $request->all();
    }

    public function index()
    {
        $query = $this->createQuery();

        /** @var Collection $collection */
        $collection = $query->get();

        $response = $this->createResponse($collection);

        return $response;
    }

    public function show($id)
    {
        /** @var Builder $query */
        $query = $this->createQuery();

        if (strpos($id, ',')) { // list of ids
            $ids = explode(',', $id);
            $query->whereIn('id', $ids);
        } else {
            $query->where('id', $id);
        }

        $data = $query->get();
        if ($data->count() <= 1) {
            $data = $data->first();
        } else { // reorder collection by list of ids order
            $byId = [];

            foreach ($data as $item) {
                $byId[$item->id] = $item;
            }

            $newData = new Collection();
            foreach ($ids as $id) {
                $newData->add($byId[$id]);
            }

            $data = $newData;
        }

        if (!is_null($data)) {
            $response = $this->createResponse($data);

            //            $result['_queries'] = DB::getQueryLog();

            return $response;
        } else {
            return response('', 404);
        }
    }

    public function create()
    {
        $data = $this->requestData;

        $modelClass = $this->modelClass;

        /** @var APIModel $item */
        $item = $modelClass::create($data);

        if (!$item->isValid()) {
            return response(['errors' => $item->getValidationErrors()], 422);
        }

        $id = $item->getKey();

        $query = $this->createQuery();
        $item = $query->where('id', $id)->first();

        $response = $this->createResponse($item);

        return $response;
    }

    public function update($id)
    {
        $data = $this->requestData;

        $modelClass = $this->modelClass;

        /** @var Builder $query */
        $query = $modelClass::where('id', $id);

        /** @var APIModel $item */
        $item = $query->first();

        if (is_null($item)) {
            return response('', 404);
        }

        $item->update($data);

        if (!$item->isValid()) {
            return response(['errors' => $item->getValidationErrors()], 422);
        }

        $query = $this->createQuery();

        $item = $query->where($item->getKeyName(), $item->getKey())->first();

        $response = $this->createResponse($item);

        return $response;
    }

    public function delete($id)
    {

        $modelClass = $this->modelClass;

        /** @var Builder $query */
        $query = $modelClass::where('id', $id);

        /** @var APIModel $item */
        $item = $query->first();

        if (!is_null($item)) {
            $item->delete();
        }
    }

    protected function createFractal()
    {
        $fractal = new FractalManager;

        $fractal->setSerializer(new APISerializer);

        return $fractal;
    }

    protected function applyIncludes(FractalManager $fractal)
    {
        $includes = $this->request->get('_includes');

        if (!is_null($includes)) {
            $includes = explode(',', $includes);
            $includes = array_map(function ($include) {
                $parts = explode('.', $include);
                $parts = array_map(function ($part) {
                    return 'relation_' . $part;
                }, $parts);

                return implode('.', $parts);
            }, $includes);
            $fractal->parseIncludes($includes);
        }
    }

    protected function createQuery()
    {
        $modelClass = $this->modelClass;

        $query = $modelClass::with($modelClass::getAPIWith());

        return $query;
    }

    protected function createResponse($data)
    {
        $fractal = $this->createFractal();

        $this->applyIncludes($fractal);

        /** @var BaseTransformer $transformer */
        $transformer = new $this->transformerClass;

        if ($data instanceof Model) {
            return $fractal->createData(new FractalItem($data, $transformer))->toArray();
        } elseif ($data instanceof Collection) {
            return $fractal->createData(new FractalCollection($data, $transformer))->toArray();
        }

        // should not get here
        throw new \Exception('Data should be either a model or a collection.');
    }
}
