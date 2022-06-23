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
 * tags={"Terminology"},
 * @OA\Parameter(
 *    description="Query",
 *    in="query",
 *    name="q",
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
 * tags={"Terminology"},
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
     * @var array|string[] $fields
     */
    private array $_params = array('q', 'page', 'size', 'sort', 'fields', 'sort_field');

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
        ]);
        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $response = Periods::list($request);


        if (empty($response)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $data = $this->parseTerminologyAggPeriods($response);
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
            return $this->jsonSingle($this->enrichPeriod($data));
        } else {
            return $this->jsonError(400, $this->_notFound);
        }
    }
}
