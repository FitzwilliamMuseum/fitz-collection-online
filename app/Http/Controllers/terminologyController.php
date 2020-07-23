<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Elasticsearch\ClientBuilder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;


class terminologyController extends Controller
{
  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index(Request $request)
  {
    return view('terminology.index', compact('data', 'paginator'));
  }

  public function record( $id) {

    $params = [
      'index' => 'ciim',
      'body' => [
        "query" => [
          "bool" => [
              "must" => [
                 [
                      "match" => [
                        "admin.id" => urlencode($id)
                      ]
                 ],
                 [
                      "term"=> [ "type.base" => 'term']
                 ]
              ]
           ]
        ]
      ],
    ];
    $response = $this->getElastic()->setParams($params)->getSearch();
    $data = $response['hits']['hits'];
    //$count  = $this->getElastic()->setParams($params)->getCount();

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
    $paramsTerm = [
      'index' => 'ciim',
      'size' => 12,
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
    // dd($paramsTerm);
    $response2 = $this->getElastic()->setParams($paramsTerm)->getSearch();
    $use = $response2['hits'];
    // dd($use);
    return view('terminology.term', compact('data', 'use', 'count'));
  }
}
