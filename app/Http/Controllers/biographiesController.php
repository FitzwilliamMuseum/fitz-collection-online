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
  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index(Request $request)
  {
    return view('biographies.index', compact('data', 'paginator'));
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

    $paramsTerm = [
      'index' => 'ciim',
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
        ]
      ],
    ];
    $response2 = $this->getElastic()->setParams($paramsTerm)->getSearch();
    $use = $response2['hits']['hits'];

    return view('biographies.record', compact('data', 'use'));
  }
}
