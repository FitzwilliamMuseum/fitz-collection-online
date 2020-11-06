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
  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index(Request $request)
  {
  }

  public function exhibition($id) {

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

    $paramsTerm = [
      'index' => 'ciim',
      'size' => 100,
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
    $records = $response2['hits']['hits'];
    return view('exhibitions.exhibition', compact('exhibition', 'records'));
  }
}
