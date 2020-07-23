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
  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index(Request $request)
  {
    return view('agents.index', compact('data', 'paginator'));
  }

  public function record($id) {

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

    $params = [
      'index' => 'ciim',
      'size' => 12,
      'body' => [
        "query" => [
          "bool" => [
            "must" => [
              [
                "match" => [
                  "department.value" => urlencode($id)
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
    $data = $response['hits']['hits'];
    return view('departments.record', compact('data', 'use', 'name'));
  }
}
