<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\Objects;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;
use App\LinkedArt\ObjectOrArtwork;

/**
 * @OA\Get(
 * path="/api/v1/linked-art",
 * summary="Retrieve objects and artworks recorded in the database",
 * description="A list of objects and artworks recorded in the database, with pagination.",
 * tags={"Objects and artworks"},
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
 *      default=20,
 *     minimum=1,
 *     maximum=100,
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
 *    description="Random object parameter",
 *    in="query",
 *    name="random",
 *    required=false,
 *    @OA\Schema(
*       type="enum",
 *       enum={"1","0"},
 *
 *    )
 * ),
 * @OA\Parameter(
 *    description="Request 3D linked objects",
 *    in="query",
 *    name="has3D",
 *    required=false,
 *    @OA\Schema(
 *       type="enum",
 *       enum={"1","0"},
 *
 *    )
 * ),
 * @OA\Parameter(
 *    description="Request coins with RIC numbers assigned",
 *    in="query",
 *    name="hasRIC",
 *    required=false,
 *    @OA\Schema(
 *       type="enum",
 *       enum={"1","0"},
 *
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
 *    description="Earliest creation year (production)",
 *    in="query",
 *    name="created_start",
 *    required=false,
 *    @OA\Schema(
 *       type="integer",
 *       nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="Latest creation year (production)",
 *    in="query",
 *    name="created_end",
 *    required=false,
 *    @OA\Schema(
 *       type="integer",
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
 * @OA\Get(
 * path="/api/v1/linked-art/{object}",
 * summary="Retrieve an object",
 * description="An object's details.",
 * tags={"Objects and artworks"},
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 *    description="ObjectOrArtwork number",
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
class LinkedArtController extends ApiController
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
        'random','hasGeo', 'place', 'created_before',
        'created_after', 'modified_before', 'modified_after',
        'image_id', 'has3D', 'hasRIC'
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
            "has3D" => "boolean",
            "hasRIC" => "boolean",
            "query" => "string|min:3",
            "fields" => "string|min:4",
            'sort_field' => 'string|in:id,title,created,updated|min:2',
            'sort' => 'string|in:asc,desc|min:3',
            'random' => 'boolean',
//            'random' => 'boolean|prohibited_if:sort,asc|prohibited_if:sort,asc,desc|prohibited_if:sort_field,id,name,summary_title,created,updated|prohibited:created_after|prohibited:created_before|prohibited:modified_after|prohibited:modified_before',
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
            'image_id' => "string|min:7|regex:'^media-\d+$'",
            'school_or_style' => "string|min:7|regex:'^term-\d+$'",
            'acquired_date_start' => 'numeric',
            'acquired_date_end' => 'numeric',
            'created_start' => 'numeric',
            'created_end' => 'numeric',
            'hasGeo' => 'boolean',
            'created_before' => 'date|date_format:Y-m-d|after:created_after|after:modified_after',
            'created_after' => 'date|date_format:Y-m-d|before:created_before|before:modified_before',
            'modified_before' => 'date|date_format:Y-m-d|after:modified_after|after:created_after',
            'modified_after' => 'date|date_format:Y-m-d|before:modified_before|before:created_before',
        ],
        [
            'random.prohibited_if' => 'You cannot use the random parameter with sort or sort_field parameters',
            'random.prohibited' => 'You cannot use the random parameter with date searches in this API',
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $response = Objects::list($request);
        $data = $this->insertType($this->create3D($this->parseData($response)), 'objects');
        if (!empty($data)) {
            $paginator = new LengthAwarePaginator(
                $data,
                $response['hits']['total']['value'],
                $request->query('size', 20),
                LengthAwarePaginator::resolveCurrentPage()
            );
            $paginator->setPath(route('api.objects.index'));
            $paginator->appends(request()->except('page'));
            return $this->jsonGenerate($request, $paginator, $paginator->total());
        } else {
            return $this->jsonError(404, $this->_notFound);
        }
    }


    public function show(Request $request, string $object): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "*" => "in:" . implode(",", $this->_showParams),
            'name' => "string|min:9|regex:'^object-\d+$'",
            "fields" => "string|min:4",
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $response = Objects::show($request, $object);
        if (!empty($response)) {
            $enriched = $this->insertSingleType($response, 'objects');
            return response()->json(
                ObjectOrArtwork::createLinkedArt(Collect($enriched)->recursive(), str_replace('object-', 'artwork-', $object)),
                200,
                $this->getHeaders(),
                JSON_PRETTY_PRINT
            );
        } else {
            return $this->jsonError(404, $this->_notFound);
        }
    }

    /**
     * @param array $data
     * @return array
     */
    public function create3D(array $data ): array
    {
        foreach($data as &$datum){
            $ric = [];
            $threeDimensions = [];
            foreach($datum['identifier'] as $datum2) {
                if(array_key_exists('source', $datum2)){
                    if($datum2['source'] === 'Sketchfab'){
                        $threeDimensions[] = 'https://sketchfab.com/3d-models/' . $datum2['value'];
                    }
                }
                if(!empty($threeDimensions)){
                    $datum['3dModels'] = $threeDimensions;
                }
                if(array_key_exists('type', $datum2)){
                    if($datum2['type'] === 'RIC'){
                        $ric[] =  $datum2['value'];
                    }
                }
                if(!empty($ric)){
                    $datum['RIC'] = $ric;
                }
            }
        }

        return $data;
    }
}
