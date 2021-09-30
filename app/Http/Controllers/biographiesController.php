<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Elasticsearch\ClientBuilder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;


class biographiesController extends Controller
{

  public function record(Request $request) {
    $id = $request->segment(3);
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
                      "term"=> [ "type.base" => 'biography']
                 ]
              ]
           ]
        ]
      ],
    ];
    $response = $this->getElastic()->setParams($params)->getSearch();
    $data = $response['hits']['hits'];

    $json = '{
      "query": {
        "match": {
          "reference_links" : "' . $id . '"
        }
      }
    }';
    $cParams = [
      'index' => 'ciim',
      'body' => $json
    ];
    $count  = $this->getElastic()->setParams($cParams)->getCount();
    $perPage = 24;
    $from = ($request->get('page', 1) - 1) * $perPage;

    $paramsTerm = [
      'index' => 'ciim',
      'size' => $perPage,
      'from' => $from,
      'body' => [
        "query" => [
          "bool" => [
              "must" => [
                 [
                      "match" => [
                        "reference_links" => $id
                      ]
                 ],
                 [
                      "term"=> [ "type.base" => 'object']
                 ],
                 [
                      "exists" => ['field' => 'multimedia']
                 ],
              ]
           ]
        ],
        'sort' => [
          [
            "admin.modified" =>  [
              "order" =>  "desc"
            ]
          ]
        ]
      ],

    ];
    $response2 = $this->getElastic()->setParams($paramsTerm)->getSearch();
    $use = $response2['hits'];
    $number = $response2['hits']['total']['value'];
    $records = $response2['hits']['hits'];
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $paginate = new LengthAwarePaginator($records, $number, $perPage, $currentPage);
    $paginate->setPath($request->getBaseUrl());
    return view('biographies.record', compact('data', 'records', 'paginate', 'use', 'count'));
  }
}
