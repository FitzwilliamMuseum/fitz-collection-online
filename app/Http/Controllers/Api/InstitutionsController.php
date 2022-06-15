<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstitutionsController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                'aggregations' => [
                    'institutions' => [
                        'terms' => [
                            'field' => 'institutions.admin.id',
                            'size' => 10,
                            'order' => [
                                '_count' => 'desc',
                            ],
                        ],
                        'aggs' => [
                            'inst' => [
                                'top_hits' => [
                                    'size' => 1,
                                    '_source' => [
                                        'include' => [
                                            0 => 'institutions.summary_title',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

        ];
        $response = $this->searchAndCache($params);
        $data = array();
        foreach ($response['aggregations']['institutions']['buckets'] as $department) {
            $data[] = array(
                'institutions' => $department['key'],
                'records' => $department['doc_count'],
                'summary_title' => $department['inst']['hits']['hits'][0]['_source']['institutions'][0]['summary_title'],
                'URI'  => $this->getWebURI('terminology', $department['key']),
                'apiURI' => $this->getTermURI('api.institutions.show', $department['key']),
            );
        }
        $paginator = $this->paginate($data)->setPath(route('api.institutions.index'));
        return $this->jsonGenerate($request, $paginator, count($response['aggregations']['institutions']['buckets']));
    }

    /**
     * @param string $institution
     * @return JsonResponse
     */
    public function show(string $institution): JsonResponse
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "match" => [
                                    "admin.id" => urlencode($institution)
                                ]
                            ],
                            [
                                "term" => [
                                    "type.base" => 'agent'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            '_source' => [
                'admin.id,admin.created,admin.modified,name,summary_title'
            ],
        ];
        $data = Collect($this->parseData($this->searchAndCache($params)))->first();
        if($data) {
            $data['URI'] = $this->getWebURI('agent', $data['admin']['id']);
            $data['apiURI'] = $this->getTermURI('api.institutions.show', $data['admin']['id']);
            return $this->jsonSingle($data);
        } else {
            return $this->jsonError('Not Found', 404);
        }
    }

}
