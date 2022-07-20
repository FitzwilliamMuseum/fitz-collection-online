<?php

namespace App\Http\Controllers\Api;
use App\Models\Api\Terminology;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;
use App\Rules\TerminologyFieldsAllowed;

/**
 * @OA\Get(
 * path="/api/v1/terminology",
 * summary="Retrieve terminology used in the database",
 * description="A list of terminology used in the database, with pagination.",
 * tags={"Terminology"},
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
 *    )
 * ),
 *  @OA\Parameter(
 *    description="Sort direction",
 *    in="query",
 *    name="sort",
 *    required=false,
 *    example="asc",
 *    @OA\Schema(
 *       type="enum",
 *       enum={"asc","desc"}
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
 *    description="Fields available to return",
 *    in="query",
 *    name="fields",
 *    required=false,
 *    example="admin.id,admin.created,admin.modified,name,summary_title",
 *    @OA\Schema(
 *       type="string",
 *       format="string"
 *    )
 * ),
 * @OA\Response(
 *    response=200,
 *    description="Success"
 *     ),
 * @OA\Response(
 *    response=400,
 *    description="Cannot be proceessed"
 *     ),
 * @OA\Response(
 *    response=404,
 *    description="Not found"
 *     ),
 * ),
 * @OA\Get(
 * path="/api/v1/terminology/{terminology}",
 * summary="Retrieve a term",
 * description="A term's details.",
 * tags={"Terminology"},
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 *    description="Query",
 *    in="path",
 *    name="terminology",
 *    required=true,
 *    example="term-30009",
 *    @OA\Schema(
 *       type="string",
 *     format="string"
 *    )
 * ),
 * @OA\Parameter(
 *    description="Fields available to return",
 *    in="query",
 *    name="fields",
 *    required=false,
 *    example="admin.id,admin.created,admin.modified,name,summary_title",
 *    @OA\Schema(
 *       type="string",
 *       format="string"
 *    )
 * ),
 * @OA\Response(
 *    response=200,
 *    description="Success"
 * ),
 *  @OA\Response(
 *    response=404,
 *    description="Not found"
 *  ),
 * @OA\Response(
 *    response=400,
 *    description="Not processed"
 *  ),
 * )
 */
class TerminologyController extends ApiController
{
    private array $_listParams = array('page', 'size', 'query', 'fields', 'sort','sort_field');

    private array $_showParams = array('fields', 'term');

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            '*' => 'in: ' . implode(',', $this->_listParams),
            'page' => 'numeric|gt:0',
            'size' => 'numeric|gt:0',
            'query' => 'string|min:3',
            'sort_field' => 'string|in:id,title,created,modified|min:2',
            'sort' => 'string|in:asc,desc|min:3',
            'fields' => ['min:5', new TerminologyFieldsAllowed()],
            'created_before' => 'date|date_format:Y-m-d|after:created_after|after:modified_after',
            'created_after' => 'date|date_format:Y-m-d|before:created_before|before:modified_before',
            'modified_before' => 'date|date_format:Y-m-d|after:modified_after|after:created_after',
            'modified_after' => 'date|date_format:Y-m-d|before:modified_before|before:created_before',
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $response = Terminology::list($request);
        $data = $this->parseData($response);
        if (empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $enriched = $this->insertType($this->parseTerms($data), 'terminology');
            $paginator = new LengthAwarePaginator($enriched, $response['hits']['total']['value'], $this->getSize($request), LengthAwarePaginator::resolveCurrentPage());
            $paginator->setPath(route('api.terminology.index'));
            return $this->jsonGenerate($request, $paginator, $response['hits']['total']['value']);
        }
    }

    /**
     * @param Request $request
     * @param string $term
     * @return JsonResponse
     */
    public function show(Request $request, string $term): JsonResponse
    {
        $validator = Validator::make(array_merge($request->all(), array('term' => $term)), [
            "*" => "in: " . implode(", ", $this->_showParams),
            "term" => "string|min:3|regex:'^term-\d+$'",
            'fields' => ['min:5', new TerminologyFieldsAllowed()],
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }
        $data = Terminology::show($request, $term);
        if (empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            return $this->jsonSingle($this->insertSingleType($this->enrichTerms($data),'terminology'));
        }
    }
}
