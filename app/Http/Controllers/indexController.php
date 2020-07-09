<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Elasticsearch\ClientBuilder;

use Cache;

class indexController extends Controller
{
  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index()
  {
    $hosts = [
      'http://api.fitz.ms:9200',        // SSL to localhost
    ];
    $client = ClientBuilder::create()->setHosts($hosts)->build();
    $params = [
      'index' => 'ciim',
      'size' => 24,
      
    ];

    $response = $client->search($params);
    $data = $response['hits']['hits'];
    return view('index', compact('data'));
  }

  public function record($priref)
  {
    $hosts = [
      'http://api.fitz.ms:9200',        // SSL to localhost
    ];
    $client = ClientBuilder::create()->setHosts($hosts)->build();
    $params = [
      'index' => 'ciim',
      'size' => 1,
      'body'  => [
        'query' => [
          'match' => [
            'identifier.priref' => $priref
          ]
        ]
      ]
    ];
    $response = $client->search($params);
    $data = $response['hits']['hits'];
    return view('record.index', compact('data'));
  }
}
