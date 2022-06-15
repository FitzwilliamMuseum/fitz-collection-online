<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PublicationsController extends ApiController
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
            'track_total_hits' => true,
            'from' => $this->getFrom($request),
            'body' => [
               'query' => [
                   'bool' => [
                       'must' => [
                            [
                                'term' => [
                                    'type.base' => 'publication'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            '_source' => [
                'admin.id,summary_title,lifecycle,title,type.base'
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
        $data = $this->parsePublicationsData($response);
        if(empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $data = $this->parsePublications($data);
        }
        $paginator = new LengthAwarePaginator(
            $data,
            $response['hits']['total']['value'],
            $this->getSize($request),
            LengthAwarePaginator::resolveCurrentPage()
        );
        $paginator->setPath(route('api.publications.index'));
        return $this->jsonGenerate($request, $paginator, $paginator->total());
    }

    /**
     * @param string $publication
     * @return JsonResponse
     */
    public function show(string $publication): JsonResponse
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                'query' => [
                    'match' => [
                        'admin.id' => urlencode($publication)
                    ]
                ]
            ],
            '_source' => [
                'admin.id,summary_title,lifecycle,title,type.base'
            ],
        ];
        $data = Collect($this->parsePublicationsData($this->searchAndCache($params)))->first();
        if (!empty($data)) {
            $data = $this->enrichPublication($data);
            return $this->jsonSingle($data);
        } else {
            return $this->jsonError(404, $this->_notFound);
        }
    }

}
