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

class exhibitionsController extends Controller
{

  public function exhibition(Request $request) {
    $id = urlencode($request->segment(3));
    $paramsTerm = [
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
                "term"=> [ "type.base" => 'exhibition']
              ]
            ]
          ]
        ]
      ],
    ];
    $exhibition = $this->getElastic()->setParams($paramsTerm)->getSearch();
    $exhibition = $exhibition['hits']['hits'][0]['_source'];
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
                      "term" => [ "type.base" => 'object']
                 ],
                 [
                      "exists" => ['field' => 'multimedia']
                 ],
              ]
           ]
        ]
      ],
    ];
    $response2 = $this->getElastic()->setParams($paramsTerm)->getSearch();
    $number = $response2['hits']['total']['value'];
    $records = $response2['hits']['hits'];
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $paginate = new LengthAwarePaginator($records, $number, $perPage, $currentPage);
    $paginate->setPath($request->getBaseUrl());
    return view('exhibitions.exhibition', compact('exhibition', 'records', 'paginate'));
  }
}
