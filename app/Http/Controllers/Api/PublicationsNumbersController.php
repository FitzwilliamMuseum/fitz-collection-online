<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\Publications;
use App\Rules\PublicationsFieldsAllowed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 * path="/api/v1/ids/publications",
 * summary="Retrieve publication IDs used in the database",
 * description="A list of publication IDs used in the database, with pagination.",
 * tags={"ID Numbers"},
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 *    description="Page number",
 *    in="query",
 *    name="page",
 *    required=false,
 *    example="1",
 *    @OA\Schema(
 *     type="integer",
 *     format="int64",
 *     default="1",
 *     minimum="1",
 *    )
 * ),
 *  @OA\Parameter(
 *    description="Size of the page response",
 *    in="query",
 *    name="size",
 *    required=false,
 *    example="20",
 *    @OA\Schema(
 *     type="integer",
 *     format="int64",
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
 * )
 */
class PublicationsNumbersController extends ApiController
{
    /**
     * @var array
     */
    private array $_listParams = array('page', 'size');


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "*" => "in: " . implode(", ", $this->_listParams),
            "page" => "numeric|gt:0",
            "size" => "numeric|gte:0|lte:500"
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $response = Publications::listNumbers($request);

        if(empty($response['hits']['hits'])) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $data = $this->parseIdData($response);
            $paginator = new LengthAwarePaginator($data,$response['hits']['total']['value'], $request->query('size',500), LengthAwarePaginator::resolveCurrentPage());
            $paginator->setPath(route('api.ids.publications.index'));
            return $this->jsonGenerate($request, $paginator, $paginator->total());
        }
    }
}
