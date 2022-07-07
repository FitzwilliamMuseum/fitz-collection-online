<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\Exhibitions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 * path="/api/v1/exhibitions/ids",
 * summary="Retrieve exhibition IDs used in the database",
 * description="A list of exhibition IDs used in the database, with pagination.",
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
 *       format="int64",
 *     default="500",
 *     minimum="1",
 *     maximum="500"
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

 */

class ExhibitionsNumbersController extends ApiController
{
    /**
     * @var array
     */
    public array $_allowed = ['page','size'];


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            '*' => 'in:'.implode(',', $this->_allowed),
            "page" => "numeric|gt:0|distinct:ignore_case",
            "size" => "numeric|gte:0|lte:500|distinct:ignore_case",
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $response = Exhibitions::listNumbers($request);

        $data = $this->parseIdData($response);

        if(empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $paginator = new LengthAwarePaginator($data,$response['hits']['total']['value'],$request->query('size',500), LengthAwarePaginator::resolveCurrentPage());
            $paginator->setPath(route('api.ids.exhibitions.index'));
            return $this->jsonGenerate($request, $paginator, $paginator->total());
        }
    }
}
