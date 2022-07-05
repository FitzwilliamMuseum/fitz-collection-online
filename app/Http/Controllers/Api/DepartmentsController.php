<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Models\Api\Departments;
use OpenApi\Annotations as OA;
use Illuminate\Http\Request;


/**
 * @OA\Get(
 * path="/api/v1/departments",
 * summary="Departments of the museum",
 * description="Retrieve string literals of departments in the museum and object counts",
 * tags={"Terminology"},
 * security={{"bearerAuth": {}}},
 * @OA\Response(
 *    response=200,
 *    description="Success"
 *   ),
 * ),
 * @OA\Get(
 *     path="/api/v1/departments/{department}",
 *     summary="Departments of the museum",
 *     description="Retrieve string literals of a specific department in the museum and object counts",
 *     tags={"Terminology"},
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 *     description="Department",
 *     *    in="path",
 *    name="department",
 *    required=true,
 *    example="Antiquities",
 *    @OA\Schema(
 *       type="string",
 *    )
 * ),
 * @OA\Response(
 *     response=200,
 *    description="Success"
 *     ),
 *  @OA\Response(
 *    response=404,
 *    description="Not found"
 *     ),
 * )
 */
class DepartmentsController extends ApiController
{

    /**
     * List Museum Departments
     *
     * Display a listing of Museum departments.
     * This endpoint will list all departments in the Museum collection. It will
     * return an array with counts, a label. At the moment, department entries are
     * string literals.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $response = Departments::list();
        $data = array();
        if(array_key_exists('aggregations', $response)) {
            foreach ($response['aggregations']['department']['buckets'] as $department) {
                $data[] = array(
                    'department' => $department['key'],
                    'records' => $department['doc_count'],
                    'type' => 'departments',
                    'apiURI' => route('api.departments.show', $department['key']),
                );
            }
            return $this->jsonSingle($data);
        } else {
            return $this->jsonError(404, $this->_notFound);
        }
    }

    /**
     * @param Request $request
     * @param string $department
     * @return JsonResponse
     */
    public function show(Request $request, string $department): JsonResponse
    {
        $response = Departments::show($request, $department);
        if(array_key_exists('aggregations', $response)) {
            $data = array();
            foreach ($response['aggregations']['department']['buckets'] as $department) {
                $data[] = array(
                    'department' => $department['key'],
                    'records' => $department['doc_count'],
                    'type' => 'departments',
                    'apiURI' => route('api.departments.show', $department['key']),
                );
            }
            return $this->jsonSingle($data);
        } else {
            return $this->jsonError(404, $this->_notFound);
        }
    }

}
