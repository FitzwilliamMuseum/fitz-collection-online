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
 * path="/api/v1/exhibitions",
 * summary="Retrieve exhibitions used in the database",
 * description="A list of exhibitions used in the database, with pagination.",
 * tags={"Exhibitions"},
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 *    description="Query",
 *    in="query",
 *    name="query",
 *    required=false,
 *    example="Hockney",
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
 *    description="Digital data created after",
 *    in="query",
 *    name="created_after",
 *    required=false,
 *    @OA\Schema(
 *      type="string",
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
 *      type="string",
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
 *      type="string",
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
 *      type="string",
 *     nullable=true,
 *     format="Y-m-d",
 *     description="Format: YYYY-MM-DD",
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
 *
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
 * path="/api/v1/exhibitions/{exhibition}",
 * summary="Retrieve a term",
 * description="An exhibitions's details.",
 * tags={"Exhibitions"},
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 *    description="Query",
 *    in="path",
 *    name="exhibition",
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

class ExhibitionsController extends ApiController
{
    /**
     * @var array
     */
    public array $_allowed = [
        'query','page','size',
        'fields', 'sort', 'sort_field',
        'created_before', 'created_after', 'modified_before',
        'modified_after'
    ];

    /**
     * @var array
     */
    public array $_showAllowed = ['exhibition','fields'];

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            '*' => 'in:'.implode(',', $this->_allowed),
            'page' => 'numeric|gt:0',
            'size' => 'numeric|gte:0|lte:100',
            'query' => 'string|min:3',
            "sort_field" => "in:" . implode(",", $this->_sortFields),
            'sort' => 'string|in:asc,desc',
            'created_before' => 'date|date_format:Y-m-d|after:created_after|after:modified_after',
            'created_after' => 'date|date_format:Y-m-d|before:created_before|before:modified_before',
            'modified_before' => 'date|date_format:Y-m-d|after:modified_after|after:created_after',
            'modified_after' => 'date|date_format:Y-m-d|before:modified_before|before:created_before',
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $response = Exhibitions::list($request);

        $data = $this->parseData($response);

        if(empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $data = $this->insertType($this->parseExhibitions($data),'exhibitions');
            $paginator = new LengthAwarePaginator($data,$response['hits']['total']['value'],$this->getSize($request), LengthAwarePaginator::resolveCurrentPage());
            $paginator->setPath(route('api.exhibitions.index'));
            return $this->jsonGenerate($request, $paginator, $paginator->total());
        }
    }

    /**
     * @param Request $request
     * @param string $exhibition
     * @return JsonResponse
     */
    public function show(Request $request, string $exhibition): JsonResponse
    {
        $validator = Validator::make(array('exhibition' => $exhibition), [
            '*' => 'in:' . implode(',', $this->_showAllowed),
            'exhibition' => 'string|min:12|regex:"^exhibition-\d+$"',
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $data = Exhibitions::show($request, $exhibition);

        if(empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            return $this->jsonSingle($this->insertSingleType($this->enrichExhibition($data),'exhibitions'));
        }
    }
}
