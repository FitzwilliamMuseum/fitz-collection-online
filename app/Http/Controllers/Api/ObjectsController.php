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
 * path="/api/v1/objects",
 * summary="Retrieve objects and artworks recorded in the database",
 * description="A list of objects and artworks recorded in the database, with pagination.",
 * tags={"Objects and artworks"},
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
 *    )
 * ),
 * @OA\Parameter(
 *    description="Accession number",
 *    in="query",
 *    name="accession_number",
 *    required=false,
 *    example="1",
 *    @OA\Schema(
 *       type="integer",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Maker",
 *    in="query",
 *    name="maker",
 *    required=false,
 *    example="agent-149652",
 *    @OA\Schema(
 *       type="string",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Category",
 *    in="query",
 *    name="category",
 *    required=false,
 *    example="agent-149652",
 *    @OA\Schema(
 *       type="string",
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
 *     enum={"asc","desc"}
 *    )
 * ),
 * @OA\Parameter(
 *    description="Random object parameter",
 *    in="query",
 *    name="random",
 *    required=false,
 *    example="1",
 *    @OA\Schema(
 *       type="enum",
 *     enum={"1","0"}
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
 *     enum={"1","0"}
 *    )
 * ),
 * @OA\Parameter(
 *    description="Determine whether an object has geographic data available",
 *    in="query",
 *    name="hasGeo",
 *    required=false,
 *    example="1",
 *    @OA\Schema(
 *       type="enum",
 *     enum={"1","0"}
 *    )
 * ),
 * @OA\Parameter(
 *    description="Determine whether an object has IIIF images available",
 *    in="query",
 *    name="hasIIIF",
 *    required=false,
 *    example="1",
 *    @OA\Schema(
 *       type="enum",
 *     enum={"1","0"}
 *    )
 * ),
 * @OA\Parameter(
 *    description="Sort field",
 *    in="query",
 *    name="sort_field",
 *    required=false,
 *    example="id",
 *    @OA\Schema(
 *       type="enum",
 *       enum={"id","created","updated","name","summary_title"}
 *    )
 * ),
 * @OA\Parameter(
 *    description="Period associated",
 *    in="query",
 *    name="period",
 *    required=false,
 *    example="term-12502",
 *    @OA\Schema(
 *       type="string",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Acquired date greater than",
 *    in="query",
 *    name="acquired_date_start",
 *    required=false,
 *    example="1920",
 *    @OA\Schema(
 *       type="int",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Acquired date less than",
 *    in="query",
 *    name="acquired_date_end",
 *    required=false,
 *    example="1921",
 *    @OA\Schema(
 *       type="int",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Publication associated",
 *    in="query",
 *    name="publication",
 *    required=false,
 *    example="publication-2797",
 *    @OA\Schema(
 *       type="string",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Materials used for component",
 *    in="query",
 *    name="component",
 *    required=false,
 *    example="term-107563",
 *    @OA\Schema(
 *       type="string",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Person associated with acquisition",
 *    in="query",
 *    name="acquired_from",
 *    required=false,
 *    example="agent-195177",
 *    @OA\Schema(
 *       type="string",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Findspot or location associated with acquisition",
 *    in="query",
 *    name="collected_place",
 *    required=false,
 *    example="term-107870",
 *    @OA\Schema(
 *       type="string",
 *    )
 * ),
 * @OA\Parameter(
 *    description="School or style associated with object",
 *    in="query",
 *    name="school_or_style",
 *    required=false,
 *    example="term-9010",
 *    @OA\Schema(
 *       type="string",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Earliest year of acquisition",
 *    in="query",
 *    name="acquired_date_start",
 *    required=false,
 *    example="1970",
 *    @OA\Schema(
 *       type="integer",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Latest year of acquisition",
 *    in="query",
 *    name="acquired_date_end",
 *    required=false,
 *    example="1975",
 *    @OA\Schema(
 *       type="integer",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Earliest creation year",
 *    in="query",
 *    name="created_start",
 *    required=false,
 *    example="1975",
 *    @OA\Schema(
 *       type="integer",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Latest creation year",
 *    in="query",
 *    name="created_end",
 *    required=false,
 *    example="1975",
 *    @OA\Schema(
 *       type="integer",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Technique used in production",
 *    in="query",
 *    name="technique",
 *    required=false,
 *    example="term-27007",
 *    @OA\Schema(
 *       type="string",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Components of the work",
 *    in="query",
 *    name="component",
 *    required=false,
 *    example="term-106206",
 *    @OA\Schema(
 *       type="string",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Assigned department for the object",
 *    in="query",
 *    name="department",
 *    required=false,
 *    example="Antiquities",
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
 * @OA\Get(
 * path="/api/v1/objects/{object}",
 * summary="Retrieve an object",
 * description="An object's details.",
 * tags={"Objects and artworks"},
 * @OA\Parameter(
 *    description="Object number",
 *    in="path",
 *    name="object",
 *    required=true,
 *    example="object-2910",
 *    @OA\Schema(
 *       type="string",
 *       format="string"
 *    )
 * ),
 * @OA\Response(
 *    response=200,
 *    description="Success"
 *     ),
 * )
 */
class ObjectsController extends ApiController
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
        'random','hasGeo'
    );
    public array $_showParams = array(
        'period', 'fields'
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
            "size" => "numeric|gt:0|lte:100",
            "hasIIIF" => "boolean",
            "hasImage" => "boolean",
            "query" => "string|min:3",
            "fields" => "string|min:4",
            'sort_field' => 'string|in:id,title,created,updated|min:2',
            'sort' => 'string|in:asc,desc|min:3',
            'random' => 'boolean|prohibited_if:sort,asc|prohibited_if:sort,asc,desc|prohibited_if:sort_field,id,name,summary_title,created,updated',
            'period' => "string|min:7|regex:'^term-\d+$'",
            'category' => "string|min:7|regex:'^term-\d+$'",
            'publication' => "string|min:10|regex:'^publication-\d+$'",
            'department' => "string|min:7",
            'name' => "string|min:6|regex:'^term-\d+$'",
            'acquired_from' => "string|min:7|regex:'^agent-\d+$'",
            'collected_place' => "string|min:7|regex:'^term-\d+$'",
            'technique' => "string|min:7|regex:'^term-\d+$'",
            'component' => "string|min:7|regex:'^term-\d+$'",
            'accession_number' => "string",
            'maker' => "string|min:7|regex:'^agent-\d+$'",
            'school_or_style' => "string|min:7|regex:'^term-\d+$'",
            'acquired_date_start' => 'numeric',
            'acquired_date_end' => 'numeric',
            'created_start' => 'numeric',
            'created_end' => 'numeric',
            'hasGeo' => 'boolean',
        ],
        [
            'random.prohibited_if' => 'You cannot use the random parameter with sort or sort_field parameters',
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $response = Objects::list($request);
        $data = $this->insertType($this->parseData($response), 'objects');
        if (!empty($data)) {

            $paginator = new LengthAwarePaginator(
                $data,
                $response['hits']['total']['value'],
                LengthAwarePaginator::resolveCurrentPage()
            );
            $paginator->setPath(route('api.objects.index'));
            $paginator->appends(request()->except('page'));

            return $this->jsonGenerate($request, $paginator, $paginator->total());
        } else {
            return $this->jsonError(404, $this->_notFound);
        }
    }


    /**
     * @param Request $request
     * @param string $object
     * @return JsonResponse
     */
    public function show(Request $request, string $object): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "*" => "in:" . implode(",", $this->_showParams),
            'name' => "string|min:9|regex:'^object-\d+$'",
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $response = Objects::show($request, $object);
        if (!empty($response)) {
            $enriched = $this->insertSingleType($response, 'objects');
            return $this->jsonSingle($this->enrich('http:', 'https:', $enriched));
        } else {
            return $this->jsonError(404, $this->_notFound);
        }
    }
}
