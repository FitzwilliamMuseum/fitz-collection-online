<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\Images;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 * path="/api/v1/ids/images",
 * summary="Retrieve images IDs used in the database",
 * description="A list of image IDs used in the database, with pagination.",
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
 *     format="int64",
 *     minimum="1",
 *    )
 * ),
 *  @OA\Parameter(
 *    description="Number of items returned per page",
 *    in="query",
 *    name="size",
 *    required=false,
 *    example="20",
 *    @OA\Schema(
 *       type="integer",
 *     format="int64",
 *     minimum="1",
 *     maximum="500",
 *     default="500"
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
class ImagesNumbersController extends ApiController
{

    public array $_params = array(
        'size', 'page',

    );


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "*" => "in:" . implode(",", $this->_params),
            "page" => "numeric|gt:0",
            "size" => "numeric|gt:0|lte:500",
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }
        $response = Images::listNumbers($request);
        $data = $this->parseIdData($response);
        if (empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $paginator = new LengthAwarePaginator(
                $data,
                $response['hits']['total']['value'],
                $request->query('size', 500),
                LengthAwarePaginator::resolveCurrentPage()
            );
            $paginator->setPath(route('api.ids.images.index'));
            return $this->jsonGenerate($request, $paginator, $paginator->total());
        }
    }
}
