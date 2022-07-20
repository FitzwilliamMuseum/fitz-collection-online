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
 * path="/api/v1/periods",
 * summary="Retrieve agents used in the database",
 * description="A list of periods used in the database, with pagination.",
 * tags={"Chronology"},
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
 * @OA\Parameter(
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
 * @OA\Parameter(
 *    description="Sort direction",
 *    in="query",
 *    name="sort",
 *    required=false,
 *    example="asc",
 *    @OA\Schema(
*       type="enum",
 *       enum={"asc","desc"},
 *       nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="Digital data created after",
 *    in="query",
 *    name="created_after",
 *    required=false,
 *    @OA\Schema(
 *       type="string",
 *     nullable=true,
 *     format="Y-m-d",
 *     description="Format: YYYY-MM-DD",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Digital data created before",
 *    in="query",
 *    name="created_before",
 *    required=false,
 *    @OA\Schema(
 *       type="string",
 *     nullable=true,
 *     format="Y-m-d",
 *     description="Format: YYYY-MM-DD",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Digital data modified after",
 *    in="query",
 *    name="modified_after",
 *    required=false,
 *    @OA\Schema(
 *       type="string",
 *     nullable=true,
 *     format="Y-m-d",
 *     description="Format: YYYY-MM-DD",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Digital data modified before",
 *    in="query",
 *    name="modified_before",
 *    required=false,
 *    @OA\Schema(
 *       type="string",
 *     nullable=true,
 *     format="Y-m-d",
 *     description="Format: YYYY-MM-DD",
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
 * path="/api/v1/periods/{period}",
 * summary="Retrieve a period",
 * description="A period's representation as used in the database.",
 * tags={"Chronology"},
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 *    description="Query",
 *    in="path",
 *    name="period",
 *    required=true,
 *    example="term-111193",
 *    @OA\Schema(
 *      type="string",
 *     format="string"
 *    )
 * ),
 * @OA\Parameter(
 *    description="Fields available to return",
 *    in="query",
 *    name="fields",
 *    required=false,
 *    example="admin.id,admin.created,admin.modified,name,summary_title,description,equivalent,parent,related,identifier",
 *    @OA\Schema(
 *       type="string",
 *       default="admin.created,admin.modified,admin.id,name,summary_title",
 *      nullable=true
 *    )
 * ),
 * @OA\Response(
 *    response=200,
 *    description="Success"
 *     ),
 * @OA\Response(
 *    response=400,
 *    description="Success"
 *     ),
 * @OA\Response(
 *    response=404,
 *    description="Success"
 *     ),
 * )
 */
class PeriodsController extends ApiController
{
    /**
     * @var array
     */
    private array $_params = array('query', 'page', 'size', 'sort', 'fields', 'sort_field');

    /**
     * @var array
     */
    private array $_showParams = array('period', 'fields');

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
            "fields" => ['min:5', new PeriodFieldsAllowed],
            'created_before' => 'date|date_format:Y-m-d|after:created_after|after:modified_after',
            'created_after' => 'date|date_format:Y-m-d|before:created_before|before:modified_before',
            'modified_before' => 'date|date_format:Y-m-d|after:modified_after|after:created_after',
            'modified_after' => 'date|date_format:Y-m-d|before:modified_before|before:created_before',
        ]);
        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $response = Periods::listNumbers($request);
        if (empty($response)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $data = $this->insertType($this->parseTerminologyAggPeriods($response), 'periods');
            $items = $this->paginate($data, $this->getSize($request), LengthAwarePaginator::resolveCurrentPage());
            $items->setPath(route('api.periods.index'));
            if ($items->items()) {
                return $this->jsonAggGenerate($request, $items, $items->values(), count($response['aggregations']['records']['buckets']));
            } else {
                return $this->jsonError(404, $this->_notFound);
            }
        }
    }

    /**
     * @param Request $request
     * @param string $period
     * @return JsonResponse
     */
    public function show(Request $request, string $period): JsonResponse
    {
        $validator = Validator::make(array_merge($request->all(), array('period' => $period)), [
            '*' => 'in:' . implode(",", $this->_showParams),
            'period' => "string|min:7|regex:'^term-\d+$'",
            'fields' => ['min:5', new PeriodFieldsAllowed],
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $data = Periods::show($request, $period);

        if (!empty($data)) {
            return $this->jsonSingle($this->insertSingleType($this->enrichPeriod($data),'periods'));
        } else {
            return $this->jsonError(400, $this->_notFound);
        }
    }
}
