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
 * path="/api/v1/ids/terminology",
 * summary="Retrieve terminology IDs used in the database",
 * description="A list of terminology IDs used in the database, with pagination.",
 * tags={"ID Numbers"},
 * security={{"bearerAuth": {}}},
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
 *     nullable=true,
 *     minimum=1,
 *     example="20",
 *     format="integer",
 *     default="500",
 *     maximum="500"
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
 */
class TerminologyNumbersController extends ApiController
{
    private array $_listParams = array('page', 'size');


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
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $response = Terminology::listNumbers($request);
        $data = $this->parseIdData($response);
        if (empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $paginator = new LengthAwarePaginator($data, $response['hits']['total']['value'], $request->query('size',500), LengthAwarePaginator::resolveCurrentPage());
            $paginator->setPath(route('api.ids.terminology.index'));
            return $this->jsonGenerate($request, $paginator, $response['hits']['total']['value']);
        }
    }

}
