<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Elasticsearch\ClientBuilder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;


class indexController extends Controller
{
  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index(Request $request)
  {
    $hosts = [
      'http://api.fitz.ms:9200',        // SSL to localhost
    ];
    $perPage = 12;
    $total = 8000;
    $page = $request->page;
    if(!is_null($page)){
      $offset = ($page -1) * $perPage ;
    } else {
      $offset = 0;
    }


    $client = ClientBuilder::create()->setHosts($hosts)->build();
    $params = [
      'index' => 'ciim',
      'size' => $perPage,
      'from' => $offset

    ];
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $response = $client->search($params);
    $data = $response['hits']['hits'];
    $paginator = new LengthAwarePaginator($data, $total, $perPage, $currentPage);
    $paginator->setPath('/');
    return view('index', compact('data', 'paginator'));
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

  public function landing()
  {
    return view('record.landing');
  }

  /** Get results for search
  *
  * @param Request $request
  * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
  */
  public function results(Request $request)
  {
    $hosts = [
      'http://api.fitz.ms:9200',        // SSL to localhost
    ];
    $this->validate($request, [
      'query' => 'required|max:200|min:3',
    ]);

    $queryString = \Purifier::clean($request->get('query'), array('HTML.Allowed' => ''));
    $perPage = 20;
    $from = ($request->get('page', 1) - 1) * $perPage;
    $client = ClientBuilder::create()->setHosts($hosts)->build();
    $params = [
      'index' => 'ciim',
      'body' => [
        'query' => [
          "bool" => [
            "must" => [
              [
                "match" => [
                  "_generic_all_std" => $queryString
                ]
              ],
              [
                "match" => [
                  "type.base" => 'object'
                ]
              ]
            ],
          ],
        ],
      ],
    ];
    $response = $client->search($params);
    $number = $response['hits']['total']['value'];
    $records = $response['hits']['hits'];
    $paginate = new LengthAwarePaginator($records, $number, $perPage);
    $paginate->setPath($request->getBaseUrl() . '?query='. $queryString);
    return view('record.results', compact('records', 'number', 'paginate', 'queryString'));
  }
}
