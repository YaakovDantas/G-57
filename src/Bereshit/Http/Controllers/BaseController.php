<?php

namespace Bereshit\Http\Controllers;

use Illuminate\Routing\Controller;
use Core\Generics\Services\BaseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class BaseController extends Controller
{    /**
     * The service instance.
     *  @var BaseService $service
     */
    protected $service;

    /**
     * Instantiate a new controller instance.
     *
     * @param BaseService $service
     * @return void
     */
    public function __construct(BaseService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        return response()->json([
            "dados" => $this->service->all($request),
            "erro" => null,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        return response()->json(
            [
                "erro" => null,
                "dados" => $this->service->save($request->all()),
            ],
            Response::HTTP_CREATED
        );
    }

    /**
     * @param int $id
     * @param mixed $info
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, $info = null)
    {
        $response = $this->service->find($id);
        return response()->json($response);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        return response()->json([
            "erro" => null,
            "dados" => $this->service->update($request->all(), $id),
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $this->service->delete($id);
        return response()->json(
            [
                "erro" => null,
                "dados" => null,
            ],
            Response::HTTP_NO_CONTENT
        );
    }

    public function setService(BaseService $service)
    {
        $this->service = $service;
    }

    public function getService()
    {
        return $this->service;
    }
}