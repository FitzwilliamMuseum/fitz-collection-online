<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

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
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                'size' => 0,
                'aggregations' => [
                    'department' => [
                        'terms' => [
                            'field' => 'department.value.keyword',
                            'size' => 10,
                        ],
                    ],
                ],
            ]
        ];
        $response = $this->searchAndCache($params);
        $data = array();
        foreach ($response['aggregations']['department']['buckets'] as $department) {
            $data[] = array('department' => $department['key'], 'records' => $department['doc_count']);
        }
        $items = $this->paginate($data, 20, LengthAwarePaginator::resolveCurrentPage());
        $items->setPath(route('api.departments.index'));
        return $this->jsonAggGenerate($request, $items, $items->values(), count($response['aggregations']['department']['buckets']));
    }

}
