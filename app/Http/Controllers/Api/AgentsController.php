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
 * tags={"Terminology"},
 * @OA\Parameter(
 *    description="Query",
 *    in="query",
 *    name="query",
 *    required=false,
 *    example="Aurelius",
 *    @OA\Schema(
 *       type="string",
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
 *       nullable=true
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
 *       nullable=true
 *    )
 * ),
 * @OA\Parameter(
 *    description="Fields available to return",
 *    in="query",
 *    name="fields",
 *    required=false,
 *    example="admin.created,admin.modified,admin.id,name,summary_title",
 *    @OA\Schema(
 *       type="string",
 *       default="admin.created,admin.modified,admin.id,name,summary_title",
 *      nullable=true
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
 *    example="admin.id",
 *    @OA\Schema(
 *       type="enum",
 *       enum={"admin.created","admin.modified","admin.id","name.value.keyword","summary_title.keyword","description"},
 *       default="admin.id",
 *      nullable=true
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
 * tags={"Terminology"},
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
 *    description="Fields available to return",
 *    in="query",
 *    name="fields",
 *    required=false,
 *    example="admin.created,admin.modified,admin.id,name,summary_title",
 *    @OA\Schema(
 *       type="string",
 *       default="admin.id",
 *      nullable=true
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
 *    @OA\Response(
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
    private array $_params = array('query', 'page', 'size', 'sort', 'sort_field', 'fields');
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
            "page" => "numeric|gt:0|distinct:ignore_case",
            "size" => "numeric|gte:0|lte:100|distinct:ignore_case",
            "query" => "string|min:3",
            "sort" => "string|min:3|in:asc,desc",
            "sort_field" => "in:" . implode(",", $this->_sortFields),
            "fields" => ['min:5', new AgentFieldsAllowed],
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }
        $response = Agents::list($request);

        $data = $this->parseData($response);
        if (empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $data = $this->parseAgents($data);
            $paginator = new LengthAwarePaginator($data, $response['hits']['total']['value'], $this->getSize($request), LengthAwarePaginator::resolveCurrentPage());
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
            return $this->jsonSingle($this->enrichAgent($data));
        }
    }
}
