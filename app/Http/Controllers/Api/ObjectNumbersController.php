<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\Objects;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 * path="/api/v1/ids",
 * summary="Retrieve objects and artworks ID numbers",
 * description="A list of objects and artworks ID numbers recorded in the database",
 * tags={"ID Numbers"},
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 *    description="Query the system for objects and artworks (defaults to AND)",
 *    in="query",
 *    name="query",
 *    required=false,
 *    example="Dendera",
 *    @OA\Schema(
 *       type="string",
 *       format="string"
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
 *       nullable=true,
 *       default="1"
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
 *      nullable=true,
 *      default=500,
 *      minimum=1,
 *     maximum=500
 *    )
 * ),
 * @OA\Parameter(
 *    description="Accession number",
 *    in="query",
 *    name="accession_number",
 *    required=false,
 *    @OA\Schema(
 *       type="string",
 *       nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="Maker",
 *    in="query",
 *    name="maker",
 *    required=false,
 *    @OA\Schema(
 *       type="string",
 *       nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="Place of collection",
 *    in="query",
 *    name="place",
 *    required=false,
 *    @OA\Schema(
 *       type="string",
 *       nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="Category",
 *    in="query",
 *    name="category",
 *    required=false,
 *    @OA\Schema(
 *       type="string",
 *       nullable=true
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
 *    description="Determine whether an object has images available",
 *    in="query",
 *    name="hasImage",
 *    required=false,
 *    example="1",
 *    @OA\Schema(
 *       type="enum",
 *       enum={"1","0"},
 *       nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="Choose fields to return. Use a comma separated string. These can be any of the following without quotes: 'categories','description','component','department.value','identifier','inscription','lifecycle','institutions','multimedia','name','note','owners','publications','school_or_style','summary','techniques','measurements','title'. By default we return all fields.",
 *    in="query",
 *    name="fields",
 *    required=false,
 *    @OA\Schema(
 *     type="string",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Determine whether an object has geographic data available",
 *    in="query",
 *    name="hasGeo",
 *    required=false,
 *    @OA\Schema(
 *       type="enum",
 *       enum={"1","0"},
 *       nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="Determine whether an object has IIIF images available",
 *    in="query",
 *    name="hasIIIF",
 *    required=false,
 *    @OA\Schema(
 *       type="enum",
 *       enum={"1","0"},
 *       nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="Sort field",
 *    in="query",
 *    name="sort_field",
 *    required=false,
 *    @OA\Schema(
 *       type="enum",
 *       enum={"id","created","updated","name","summary_title"},
 *       nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="Period associated",
 *    in="query",
 *    name="period",
 *    required=false,
 *    @OA\Schema(
 *       type="string",
 *       nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="Acquired date greater than",
 *    in="query",
 *    name="acquired_date_start",
 *    required=false,
 *    @OA\Schema(
 *       type="int",
 *     nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="Acquired date less than",
 *    in="query",
 *    name="acquired_date_end",
 *    required=false,
 *    @OA\Schema(
 *       type="int",
 *     nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="Publication associated",
 *    in="query",
 *    name="publication",
 *    required=false,
 *    @OA\Schema(
 *       type="string",
 *       nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="Materials used for component",
 *    in="query",
 *    name="component",
 *    required=false,
 *    @OA\Schema(
 *       type="string",
 *       nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="Person associated with acquisition",
 *    in="query",
 *    name="acquired_from",
 *    required=false,
 *    @OA\Schema(
 *       type="string",
 *       nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="Findspot or location associated with acquisition",
 *    in="query",
 *    name="collected_place",
 *    required=false,
 *    @OA\Schema(
 *       type="string",
 *       nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="School or style associated with object",
 *    in="query",
 *    name="school_or_style",
 *    required=false,
 *    @OA\Schema(
 *       type="string",
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
 * @OA\Parameter(
 *    description="Earliest year of acquisition",
 *    in="query",
 *    name="acquired_date_start",
 *    required=false,
 *    @OA\Schema(
 *       type="integer",
 *       nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="Latest year of acquisition",
 *    in="query",
 *    name="acquired_date_end",
 *    required=false,
 *    @OA\Schema(
 *       type="integer",
 *       nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="Earliest creation year",
 *    in="query",
 *    name="created_start",
 *    required=false,
 *    @OA\Schema(
 *       type="integer",
 *       nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="Latest creation year",
 *    in="query",
 *    name="created_end",
 *    required=false,
 *    @OA\Schema(
 *       type="integer",
 *       nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="Technique used in production",
 *    in="query",
 *    name="technique",
 *    required=false,
 *    @OA\Schema(
 *       type="string",
 *       nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="Components of the work",
 *    in="query",
 *    name="component",
 *    required=false,
 *    @OA\Schema(
 *       type="string",
 *       nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="Assigned department for the object",
 *    in="query",
 *    name="department",
 *    required=false,
 *    @OA\Schema(
 *       type="string",
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
class ObjectNumbersController extends ApiController
{
    public array $_params = array(
        'sort', 'size', 'hasImage',
        'hasIIIF', 'page', 'query',
        'fields', 'sort_field', 'department',
        'category', 'period', 'publication',
        'name','acquired_from', 'collected_place',
        'accession_number','maker','school_or_style',
        'acquired_date_start','acquired_date_end','technique',
        'component', 'created_start', 'created_end',
        'hasGeo', 'place', 'has3D'
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
            "hasIIIF" => "boolean",
            "hasImage" => "boolean",
            "query" => "string|min:3",
            "fields" => "string|min:4",
            'sort_field' => 'string|in:id,title,created,updated|min:2',
            'sort' => 'string|in:asc,desc|min:3',
            'period' => "string|min:7|regex:'^term-\d+$'",
            'category' => "string|min:7|regex:'^term-\d+$'",
            'publication' => "string|min:10|regex:'^publication-\d+$'",
            'department' => "string|min:7",
            'name' => "string|min:6|regex:'^term-\d+$'",
            'acquired_from' => "string|min:7|regex:'^agent-\d+$'",
            'collected_place' => "string|min:7|regex:'^term-\d+$'",
            'technique' => "string|min:7|regex:'^term-\d+$'",
            'component' => "string|min:7|regex:'^term-\d+$'",
            'place' => "string|min:7|regex:'^term-\d+$'",
            'accession_number' => "string",
            'maker' => "string|min:7|regex:'^agent-\d+$'",
            'school_or_style' => "string|min:7|regex:'^term-\d+$'",
            'acquired_date_start' => 'numeric',
            'acquired_date_end' => 'numeric',
            'created_start' => 'numeric',
            'created_end' => 'numeric',
            'hasGeo' => 'boolean',
            'has3D' => 'boolean',
            'created_before' => 'date|date_format:Y-m-d|after:created_after|after:modified_after',
            'created_after' => 'date|date_format:Y-m-d|before:created_before|before:modified_before',
            'modified_before' => 'date|date_format:Y-m-d|after:modified_after|after:created_after',
            'modified_after' => 'date|date_format:Y-m-d|before:modified_before|before:created_before',
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $response = Objects::listNumbers($request);
        $data = $this->parseIdData($response);
        if (!empty($data)) {

            $paginator = new LengthAwarePaginator(
                $data,
                $response['hits']['total']['value'],
                $request->query('size', 500),
                LengthAwarePaginator::resolveCurrentPage()
            );
            $paginator->setPath(route('api.ids.ids.index'));
            $paginator->appends(request()->except('page'));

            return $this->jsonGenerate($request, $paginator, $paginator->total());
        } else {
            return $this->jsonError(404, $this->_notFound);
        }
    }
}
