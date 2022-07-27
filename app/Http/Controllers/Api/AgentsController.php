<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;
use App\Models\Api\Agents;
use App\Rules\AgentFieldsAllowed;

/**
 * @OA\Get(
 * path="/api/v1/agents",
 * summary="Retrieve agents used in the database",
 * description="A list of agents used in the database, with pagination.",
 * tags={"People"},
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 *    description="Query",
 *    in="query",
 *    name="query",
 *    required=false,
 *    example="Aurelius",
 *    @OA\Schema(
 *      type="string",
 *      format="string",
 *      maxLength=255,
 *      minLength=1,
 *      example="Aurelius"
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
 *       minimum=1,
 *       example="1",
 *      format="integer"
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
 *       nullable=true,
 *     minimum=1,
 *     example="20",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Fields available to return",
 *    in="query",
 *    name="fields",
 *    required=false,
 *    example="name,summary_title",
 *    @OA\Schema(
 *       type="string",
 *       default="name,summary_title",
 *       nullable=true,
 *     format="string",
 *     maxLength=255,
 *     minLength=4,
 *     example="name,summary_title"
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
 *  @OA\Parameter(
 *    description="Sort field",
 *    in="query",
 *    name="sort_field",
 *    required=false,
 *    example="id",
 *    @OA\Schema(
 *       type="enum",
 *       enum={"created","updated","id","name","summary_title"},
 *       default="admin.id",
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
 * path="/api/v1/agents/{agent}",
 * summary="Retrieve an agent",
 * description="An agent's details",
 * tags={"People"},
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 *    description="Agent term id number",
 *    in="path",
 *    name="agent",
 *    required=true,
 *    example="agent-28",
 *    @OA\Schema(
 *       type="string",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Comma separated fields available to return, eg name,summary_title",
 *    in="query",
 *    name="fields",
 *    required=false,
 *    @OA\Schema(
 *       type="string",
 *       default="admin.id",
 *       nullable=true
 *    )
 * ),
 * @OA\Response(
 *    response=200,
 *    description="The request completed successfully."
 *     ),
 * @OA\Response(
 *    response=400,
 *    description="The request cannot be processed"
 *     ),
 * @OA\Response(
 *    response=404,
 *    description="Not found"
 *     ),
 * )
 */

class AgentsController extends ApiController
{
    /**
     * @var array
     */
    private array $_params = array(
        'query', 'page', 'size', 'sort', 'sort_field', 'fields',
        'created_before', 'created_after', 'modified_before',
        'modified_after'
    );
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
            "size" => "numeric|gte:0",
            "query" => "string|min:3",
            "sort" => "string|min:3|in:asc,desc",
            "sort_field" => "in:" . implode(",", $this->_sortFields),
            "fields" => ['min:5', new AgentFieldsAllowed],
            'created_before' => 'date|date_format:Y-m-d|after:created_after|after:modified_after',
            'created_after' => 'date|date_format:Y-m-d|before:created_before|before:modified_before',
            'modified_before' => 'date|date_format:Y-m-d|after:modified_after|after:created_after',
            'modified_after' => 'date|date_format:Y-m-d|before:modified_before|before:created_before',
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }
        $response = Agents::list($request);

        $data = $this->parseData($response);
        if (empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $data = $this->insertType($this->parseAgents($data), 'agents');
            $paginator = new LengthAwarePaginator($data, $response['hits']['total']['value'],$request->query('size',10), LengthAwarePaginator::resolveCurrentPage());
            $paginator->setPath(route('api.agents.index'));
            $paginator->appends(request()->except('page'));
            return $this->jsonGenerate($request, $paginator, $paginator->total());
        }
    }


    /**
     * @param Request $request
     * @param string $agent
     * @return JsonResponse
     */
    public function show(Request $request, string $agent): JsonResponse
    {
        $validator = Validator::make(array_merge($request->all(),array('agent' => $agent)), [
            '*' => 'in:' . implode(",", $this->_showParams),
            'agent' => "string|min:7|regex:'^agent-\d+$'",
            "fields" => ['min:5', new AgentFieldsAllowed],
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }
        $data = Agents::show($request, $agent);

        if (empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            return $this->jsonSingle($this->insertSingleType($this->enrichAgent($data),'agents'));
        }
    }
}
