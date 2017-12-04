<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transformers\APISerializer;
use App\Models\Transformers\BaseTransformer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Routing\Controller;
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
    protected $siteId;

    public function __construct(Request $request, JWTAuth $auth)
    {
        $this->request = $request;
        $this->user = $auth->authenticate();
        $this->requestData = $request->all();
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
