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
 * path="/api/v1/publications",
 * summary="Retrieve publications used in the database",
 * description="A list of publications used in the database, with pagination.",
 * tags={"Publications"},
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 *    description="Query",
 *    in="query",
 *    name="query",
 *    required=false,
 *    example="Darmstadt",
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
 * path="/api/v1/publications/{publication}",
 * summary="Retrieve a publication",
 * description="An publications's details.",
 * tags={"Publications"},
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 *    description="Query",
 *    in="path",
 *    name="publication",
 *    required=true,
 *    example="publication-112039",
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
class PublicationsController extends ApiController
{
    /**
     * @var array
     */
    private array $_listParams = array('query', 'page', 'size','sort', 'fields','sort_field');

    /**
     * @var array
     */
    private array $_showParams = array('publications', 'fields');

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "*" => "in: " . implode(", ", $this->_listParams),
            "page" => "numeric|gt:0",
            "size" => "numeric|gte:0|lte:100",
            "query" => "string|min:3",
            'sort_field' => 'string|in:id,title,created,modified|min:2',
            'sort' => 'string|in:asc,desc|min:3',
            "fields" => ['min:5', new PublicationsFieldsAllowed()],
            'created_before' => 'date|date_format:Y-m-d|after:created_after|after:modified_after',
            'created_after' => 'date|date_format:Y-m-d|before:created_before|before:modified_before',
            'modified_before' => 'date|date_format:Y-m-d|after:modified_after|after:created_after',
            'modified_after' => 'date|date_format:Y-m-d|before:modified_before|before:created_before',
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $response = Publications::list($request);

        if(empty($response['hits']['hits'])) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $data = $this->insertType($this->enrichPublications($this->parsePublicationsData($response)), 'publications');
            $paginator = new LengthAwarePaginator($data,$response['hits']['total']['value'],$this->getSize($request), LengthAwarePaginator::resolveCurrentPage());
            $paginator->setPath(route('api.publications.index'));
            return $this->jsonGenerate($request, $paginator, $paginator->total());
        }
    }

    /**
     * @param Request $request
     * @param string $publication
     * @return JsonResponse
     */
    public function show(Request $request, string $publication): JsonResponse
    {
        $validator = Validator::make(array_merge($request->all(), array('publication' => $publication)), [
            '*' => 'in:' . implode(",", $this->_showParams),
            'publication' => "string|min:6|regex:'^publication-\d+$'",
            "fields" => ['min:5', new PublicationsFieldsAllowed()],
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $response = Publications::show($request, $publication);

        if (!empty($response)) {
            return $this->jsonSingle($this->insertSingleType($this->enrichPublication($response),'publications'));
        } else {
            return $this->jsonError(404, $this->_notFound);
        }
    }

}
