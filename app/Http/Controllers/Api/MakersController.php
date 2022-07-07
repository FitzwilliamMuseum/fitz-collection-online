<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\Makers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 * path="/api/v1/makers",
 * summary="Retrieve agents used in the database",
 * description="A list of makers used in the database, with pagination.",
 * tags={"People"},
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 *    description="Query",
 *    in="query",
 *    name="query",
 *    required=false,
 *    example="Roman",
 *    @OA\Schema(
 *       type="string",
 *     format="string"
 *    )
 * ),
 * @OA\Parameter(
 *    description="Page number",
 *    in="query",
 *    name="page",
 *    required=false,
 *    example="1",
 *    @OA\Schema(
 *       type="integer",
 *       format="int64"
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
 *       format="int64"
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
 * ),
 * @OA\Get(
 * path="/api/v1/makers/{maker}",
 * summary="Retrieve a maker",
 * description="A maker's representation as used in the database.",
 * tags={"People"},
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 *    description="Query",
 *    in="path",
 *    name="maker",
 *    required=true,
 *    example="agent-173723",
 *    @OA\Schema(
 *      type="string",
 *     format="string"
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
 * ),
 * )
 */
class MakersController extends ApiController
{
    /**
     * @var array|string[]
     */
    private array $_params = array('query', 'page', 'size', 'sort');

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "*" => "in:" . implode(",", $this->_params),
            "page" => "numeric|gt:0",
            "size" => "numeric|gte:0|lte:100",
            "query" => "string|min:3",
            'sort' => 'string|in:asc,desc|min:3',
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }
        $response = Makers::list($request);

        $data = $this->insertType($this->parseTerminologyAggMakers($response),'makers');
        if (empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $items = $this->paginate($data, $this->getSize($request), LengthAwarePaginator::resolveCurrentPage());
            $items->setPath(route('api.makers.index'));
            if ($items->items()) {
                return $this->jsonAggGenerate($request, $items, $items->values(), count($response['aggregations']['records']['buckets']));
            } else {
                return $this->jsonError(404, $this->_notFound);
            }
        }

    }

    /**
     * @param Request $request
     * @param string $maker
     * @return JsonResponse
     */
    public function show(Request $request, string $maker): JsonResponse
    {
        $validator = Validator::make(array_merge($request->all(), array('maker' => $maker)), [
            '*' => 'in:maker',
            'maker' => "string|min:6|regex:'^agent-\d+$'",
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $data = Makers::show($request, $maker);

        if (empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $data = $this->enrichMaker($data);
            return $this->jsonSingle($data);
        }

    }

}
