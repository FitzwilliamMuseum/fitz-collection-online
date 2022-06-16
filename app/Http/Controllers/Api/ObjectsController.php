<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ObjectsController extends ApiController
{
    public array $_params = array(
        'sort',
        'size',
        'hasImage',
        'hasIIIF',
        'page',
        'q',
        'fields',
    );

    public array $_listFields = array(
        'admin.id',
        'admin.created',
        'admin.modified',
        'department.value',
        'identifier',
        'description',
        'lifecycle',
        'institutions',
        'multimedia',
        'name',
        'school_or_style',
        'title',
        'type.base'
    );

    public array $_fields = array(
        'admin.id',
        'admin.created',
        'admin.modified',
        'categories',
        'description',
        'component',
        'department.value',
        'identifier',
        'inscription',
        'lifecycle',
        'institutions',
        'multimedia',
        'name',
        'note',
        'owners',
        'publications',
        'school_or_style',
        'summary',
        'techniques',
        'measurements',
        'title',
        'type.base'
    );
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {

        if($request->query('fields')){
            $explodedFields = explode(',', $request->query('fields'));
            $errors = array();
            foreach($explodedFields as $field){
                if(!in_array($field, $this->_fields)){
                    $errors[] = $field;
                }
            }
            if(!empty($errors)){
                return $this->jsonError(400,'Invalid fields: ' . implode(',',$errors));
            }
        }

        if($request->getQueryString()){
            parse_str($request->getQueryString(), $clean);
            $errors = array();
            foreach(array_keys($clean) as $param){
                if(!in_array($param, $this->_params)){
                    $errors[] = $param;
                }
            }
            if(!empty($errors)){
                return $this->jsonError(400,'Invalid parameters: ' . implode(',',$errors));
            }
        }

        $size = $request->query('size') ?? 20;
        if(!is_numeric($size)){
            return $this->jsonError(400,'Size must be an integer');
        }
        if ($size > 100) {
            return $this->jsonError(400, 'Size must be less than 100');
        }

        if($request->query('page')){
            $from = $request->query('page') * $size;
        } else {
            $from = 0;
        }

        $params = [
            'index' => 'ciim',
            'size' => $this->getSize($request),
            'from' => $from,
            'track_total_hits' => true,
            'body' => [
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "multi_match" => [
                                    "fields" => "_generic_all_std",
                                    "query" => $request->query('q') ?? 'Fitzwilliam',
                                    "operator" => "AND",
                                ],

                            ],

                        ],
                        "filter" =>
                            [
                                "term" => ["type.base" => 'object'],
                            ],

                    ]
                ],
            ],

            '_source' => [
                $request->query('fields') ?? implode(',', $this->_listFields)
            ],
        ];
        $sortString = $request->query('sort') ?? 'asc';
        if (is_string($sortString) && in_array($sortString, array('asc', 'desc'))) {
            $sort = array(
                "admin.modified" => [
                    "order" => $sortString
                ]
            );
            $params['body']['sort'] = $sort;
        } else {
            return $this->jsonError( 400, $this->_invalidSort);
        }

        if ($request->query('hasImage') === 'true') {
            $filter = array(
                "exists" => [
                    "field" => "multimedia"
                ]
            );
            $params['body']['query']['bool']['must'][] = [$filter];
        }
        if ($request->query('hasIIIF') === 'true') {
            $filter = array(
                "exists" => [
                    "field" => "multimedia.processed.zoom"
                ]
            );
            $params['body']['query']['bool']['must'][] = [$filter];
        }
        $response = $this->searchAndCache($params);
        $data = $this->parseData($response);
        $paginator = new LengthAwarePaginator(
            $data,
            $response['hits']['total']['value'],
            $size,
            LengthAwarePaginator::resolveCurrentPage()
        );
        $paginator->setPath(route('api.objects.index'));
        return $this->jsonGenerate($request, $paginator, $paginator->total());
    }

    /**
     * @param Request $request
     * @param string $object
     * @return JsonResponse
     */
    public function show(Request $request, string $object): JsonResponse
    {
        if($request->getQueryString()){
            parse_str($request->getQueryString(), $clean);
            $errors = array();
            foreach(array_keys($clean) as $param){
                if($param != 'fields'){
                    $errors[] = $param;
                }
            }
            if(!empty($errors)){
                return $this->jsonError(400,'Invalid parameters: ' . implode(',',$errors));
            }
        }
        $params = [
            'index' => 'ciim',
            'size' => 1,
            'body' => [
                'query' => [
                    'match' => [
                        'admin.id' => urlencode($object)
                    ]
                ]
            ],
            '_source' => [
                $request->query('fields') ?? implode(',', $this->_fields)
            ],
        ];
        $response = $this->searchAndCache($params);
        $data = $this->parseData($response);

        if (!empty($data)) {
            return $this->jsonSingle($data[0]);
        } else {
            return $this->jsonError(404, $this->_notFound);
        }
    }
}
