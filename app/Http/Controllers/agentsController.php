<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Elasticsearch\ClientBuilder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;


class agentsController extends Controller
{
  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index(Request $request)
  {
    return view('agents.index', compact('data', 'paginator'));
  }

  public function record( $id) {
    $hosts = [
      'http://api.fitz.ms:9200',        // SSL to localhost
    ];
    $client = ClientBuilder::create()->setHosts($hosts)->build();
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
                      "term"=> [ "type.base" => 'agent']
                 ]
              ]
           ]
        ]
      ],
    ];
    $response = $client->search($params);
    $data = $response['hits']['hits'];
    return view('agents.record', compact('data'));
  }
}