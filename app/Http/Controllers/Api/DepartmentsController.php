<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Models\Api\Departments;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 * path="/api/v1/departments",
 * summary="Departments of the museum",
 * description="Retrieve string literals of departments in the museum and object counts",
 * tags={"Terminology"},
 * @OA\Response(
 *    response=200,
 *    description="Success"
 *   ),
 * )
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
                $data[] = array('department' => $department['key'], 'records' => $department['doc_count']);
            }
            return $this->jsonSingle($data);
        } else {
            return $this->jsonError(404, $this->_notFound);
        }
    }

}
