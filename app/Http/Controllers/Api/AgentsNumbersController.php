<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;
use App\Models\Api\Agents;
use App\Rules\AgentFieldsAllowed;

/**
 * @OA\Get(
 * path="/api/v1/ids/agents",
 * summary="Retrieve agent id numbers used in the database",
 * description="A list of agents numbers used in the database, with pagination.",
 * tags={"ID Numbers"},
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 *    description="Page number",
 *    in="query",
 *    name="page",
 *    required=false,
 *    example="1",
 *    @OA\Schema(
 *       type="integer",
 *       nullable=true,
 *       minimum=1,
 *       example="1",
 *      format="integer"
 *    )
 * ),
 *  @OA\Parameter(
 *    description="Size of the page response",
 *    in="query",
 *    name="size",
 *    required=false,
 *    example="20",
 *    @OA\Schema(
 *       type="integer",
 *       nullable=true,
 *     minimum=1,
 *     example="20",
 *     format="integer",
 *     default="500",
 *     maximum="500"
 *
 *    )
 * ),
 * @OA\Response(
 *    response=200,
 *    description="The request completed successfully."
 * ),
 * @OA\Response(
 *    response=400,
 *    description="The request cannot be processed"
 * ),
 * @OA\Response(
 *    response=404,
 *    description="Not found"
 * ),
 *     )
 */

class AgentsNumbersController extends ApiController
{
    /**
     * @var array
     */
    private array $_params = array('page', 'size');

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "*" => "in:" . implode(",", $this->_params),
            "page" => "numeric|gt:0|distinct:ignore_case",
            "size" => "numeric|gte:0|lte:500|distinct:ignore_case",
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }
        $response = Agents::listNumbers($request);
        $data = $this->parseIdData($response);
        if (empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $paginator = new LengthAwarePaginator($data, $response['hits']['total']['value'],$request->query('size',500), LengthAwarePaginator::resolveCurrentPage());
            $paginator->setPath(route('api.ids.agents.index'));
            $paginator->appends(request()->except('page'));
            return $this->jsonGenerate($request, $paginator, $paginator->total());
        }
    }

}
