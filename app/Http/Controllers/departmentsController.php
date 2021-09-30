<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Elasticsearch\ClientBuilder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

use App\FitzElastic\Elastic;

class departmentsController extends Controller
{

  public function record(Request $request) {
    $id = urlencode($request->segment(3));
    $paramsTerm = [
      'index' => 'ciim',
      'body' => [
        "query" => [
          "bool" => [
            "must" => [
              [
                "match" => [
                  "department.value" => $id
                ]
              ],
              [
                "term"=> [ "type.base" => 'object']
              ]
            ]
          ]
        ]
      ],
    ];
    $use = $this->getElastic()->setParams($paramsTerm)->getCount();

    $name = urldecode($id);
    $perPage = 24;
    $from = ($request->get('page', 1) - 1) * $perPage;

    $params = [
      'index' => 'ciim',
      'size' => $perPage,
      'from' => $from,
      'body' => [
        "query" => [
          "bool" => [
            "must" => [
              [
                "match" => [
                  "department.value" => $id
                ]
              ],
              [
                "term"=> [ "type.base" => 'object']
              ],
              [
                "exists" => ['field' => 'multimedia']
              ],
            ]
          ],
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
    $response = $this->getElastic()->setParams($params)->getSearch();
    $number = $response['hits']['total']['value'];
    $records = $response['hits']['hits'];
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $paginate = new LengthAwarePaginator($records, $number, $perPage, $currentPage);
    $paginate->setPath($request->getBaseUrl());
    return view('departments.record', compact('records', 'use', 'name', 'paginate'));
  }
}
