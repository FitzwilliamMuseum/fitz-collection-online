<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\Periods;
use App\Rules\PeriodFieldsAllowed;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 * path="/api/v1/ids/periods",
 * summary="Retrieve agent IDs used in the database",
 * description="A list of period IDs used in the database, with pagination.",
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
 * @OA\Parameter(
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
 *     )
 */
class PeriodsNumbersController extends ApiController
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
            "page" => "numeric|gt:0",
            "size" => "numeric|gte:0|lte:500",
        ]);
        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $response = Periods::list($request);
        if (empty($response)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $data = $this->parseTerminologyAggPeriodsID($response);
            $items = $this->paginate($data, $request->query('size',500), LengthAwarePaginator::resolveCurrentPage());
            $items->setPath(route('api.ids.periods.index'));
            if ($items->items()) {
                return $this->jsonAggGenerate($request, $items, $items->values(), count($response['aggregations']['records']['buckets']));
            } else {
                return $this->jsonError(404, $this->_notFound);
            }
        }
    }
}
