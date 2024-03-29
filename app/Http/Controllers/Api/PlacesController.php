<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\Places;
use App\Rules\PlacesFieldsAllowed;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 * path="/api/v1/places",
 * summary="Retrieve places used in the database",
 * description="A list of places used in the database, with pagination.",
 * tags={"Geography"},
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 *    description="Query",
 *    in="query",
 *    name="query",
 *    required=false,
 *    example="Painting",
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
 * path="/api/v1/places/{place}",
 * summary="Retrieve a term",
 * description="A term's details.",
 * tags={"Geography"},
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 *    description="Query",
 *    in="path",
 *    name="place",
 *    required=true,
 *    example="term-112039",
 *    @OA\Schema(
 *       type="string",
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
class PlacesController extends ApiController
{
    /**
     * @var array
     */
    private array $_params = array(
        'query', 'page', 'size',
        'sort','fields', 'created_after',
        'created_before', 'modified_after', 'modified_before'
    );

    /**
     * @var array|string[]
     */
    private array $_showParams = array('place');

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
            'created_before' => 'date|date_format:Y-m-d|after:created_after|after:modified_after',
            'created_after' => 'date|date_format:Y-m-d|before:created_before|before:modified_before',
            'modified_before' => 'date|date_format:Y-m-d|after:modified_after|after:created_after',
            'modified_after' => 'date|date_format:Y-m-d|before:modified_before|before:created_before',
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $response = Places::list($request);
        if (empty($response['hits']['hits'])) {
            return $this->jsonError('404', $this->_notFound);
        } else {
            $data = $this->insertType($this->parseTerminologyAggPlace($response), 'places');
            $items = $this->paginate($data, $this->getSize($request), LengthAwarePaginator::resolveCurrentPage());
            $items->setPath(route('api.places.index'));
            return $this->jsonAggGenerate($request, $items, $items->values(), count($response['aggregations']['places']['buckets']));
        }
    }

    /**
     * @param Request $request
     * @param string $place
     * @return JsonResponse
     */
    public function show(Request $request, string $place): JsonResponse
    {
        $validator = Validator::make(array_merge($request->all(), array('place' => $place)), [
            '*' => 'in:' . implode(",", $this->_showParams),
            'place' => "string|min:6|regex:'^term-\d+$'",
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $data = Places::show($request, $place);
        if (!empty($data)) {
            return $this->jsonSingle($this->insertSingleType($this->enrichPlace($data),'places'));
        } else {
            return $this->jsonError(400, $this->_notFound);
        }
    }

}
