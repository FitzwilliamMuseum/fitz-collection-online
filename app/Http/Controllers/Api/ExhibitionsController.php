<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;


class ExhibitionsController extends ApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $params = [
            'index' => 'ciim',
            'size' => $this->getSize($request),
            'from' => $this->getFrom($request),
            'track_total_hits' => true,
            'body' => [
                'sort' => $this->getSort($request),
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "term" => ["type.base" => 'exhibition']
                            ]
                        ]
                    ]
                ],
            ],
            '_source' => [
                'admin.id,admin.created,admin.modified,venues,summary_title,name.value,title.value'
            ],
        ];

        if(!is_null($request->query('q'))) {
            $params['body']['query']['bool']['must'][] = ["multi_match" => [
                "fields" => "_generic_all_std",
                "query" => $request->query('q'),
                "operator" => "AND",
            ]
            ];
        }
        $response = $this->searchAndCache($params);
        $data = $this->parseData($response);
        if(empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $data = $this->parseExhibitions($data);
        }
        $paginator = new LengthAwarePaginator(
            $data,
            $response['hits']['total']['value'],
//            $this->getClient()->count(Arr::except($params, ['_source', 'from', 'size','track_total_hits','sort']))['count'],
            $this->getSize($request),
            LengthAwarePaginator::resolveCurrentPage()
        );
        $paginator->setPath(route('api.exhibitions.index'));
        return $this->jsonGenerate($request, $paginator, $paginator->total());
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "match" => [
                                    "admin.id" => $id
                                ]
                            ],
                            [
                                "term" => ["type.base" => 'exhibition']
                            ]
                        ]
                    ]
                ]
            ],
            '_source' => [
                'admin.id,admin.created,admin.modified,name,summary_title,venues'
            ],
        ];
        $data = Collect($this->parseData($this->searchAndCache($params)))->first();
        if(empty($data)) {
            return $this->jsonError(404, 'No exhibitions found.');
        } else {
            $data = $this->enrichExhibition($data);
            return $this->jsonSingle($data);
        }
    }
}
