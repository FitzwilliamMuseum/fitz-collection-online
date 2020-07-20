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
    $perPage = 24;
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
    $paginator->setPath('/spelunker');
    return view('index', compact('data', 'paginator'));
  }

  public function record($priref) {
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

    $query = $data[0]['_source']['summary_title'];
    $id = $data[0]['_id'];
    $string = '{ "_id" : "' . $id . '"},"' . urlencode($query) .'"';
    $json = '{
      "query": {
        "bool": {
          "must": [
            {
              "more_like_this": {
                "fields": [
                  "_generic_all_std"
                ],
                "like": [

                  ' . $string . '

                ],
                "min_term_freq": 1,
                "min_doc_freq": 1,
                "max_query_terms": 15,
                "stop_words": [],
                "boost": 2,
                "include": false
              }
            }
          ],
          "filter": [
            {
              "exists": {
                "field": "multimedia"
              }
            },
            {
              "term": {
                "type.base": "object"
              }
            }
          ]
        }
      }
    }';

    $paramsMLT = [
      'index' => 'ciim',
      'size' => 3,
      'body'  => $json
    ];
    $response2 = $client->search($paramsMLT);
    $mlt = $response2['hits']['hits'];
    // dd($data);
    return view('record.index', compact('data', 'mlt'));
  }

  public function recordSwitch($priref,$format) {
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
    return response(view('record.json',array('data'=>$data)),200, ['Content-Type' => 'application/json']);
  }

  public function search() {
    return view('record.search');
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
    $perPage = 24;
    $from = ($request->get('page', 1) - 1) * $perPage;
    $client = ClientBuilder::create()->setHosts($hosts)->build();
    if(!is_null($request->get('operator'))){
      $operator  =  $request->get('operator');
    } else {
      $operator = 'AND';
    }
    $params = [
      'index' => 'ciim',
      'size' => $perPage,
      'from' => $from,
      'body' => [
        "query" => [
          "bool" => [
            "must" => [
              [
                "multi_match" => [
                  "fields" => "_generic_all_std",
                  "query" => $queryString,
                  "operator" =>  $operator
                ],

              ],

            ],
            "filter" =>
            [
              "term"=> [ "type.base" => 'object'],
              // "term" => ["lifecycle.creation.periods.admin.id" => "term-111888"],
            ],

          ]
        ],
      ],
    ];

    $facets = array(
      'institutions' => [
        'terms' =>
          ["field" => 'institutions.admin.id',"size" => 10]
        ],
      'materials' => [
        'terms' =>
          ["field" => 'materials.reference.admin.id',"size" => 10]
         ],
      'periods' => [
        'terms' =>
          ["field" => 'lifecycle.creation.periods.admin.id',"size" => 10]
         ],
      'object-name' => [
           'terms' =>
             ["field" => 'name.reference.admin.id',"size" => 10]
            ],
      'maker' => [
           'terms' =>
             ["field" => 'lifecycle.creation.periods.admin.id',"size" => 10]
            ],
      'agents' => [
           'terms' =>
             ["field" => 'content.agents.admin.id',"size" => 10]
            ],
    );
    $params['body']['aggs'] = $facets;
    // Add images filter
    if(!is_null($request->get('images'))){
      $filter  =  array("exists" => [
        "field" => "multimedia"]
      );
      array_push($params['body']['query']['bool']['must'], [$filter]);
    }


    // Add sort filter
    if(!is_null($request->get('sort'))){
      $order = $request->get('sort');
      $sort = array(

            "admin.modified" =>  [
              "order" =>  $order
            ]
      );
      $params['body']['sort'] = $sort;
    }
    // dd($params);
    // Get response
    $response = $client->search($params);
    // dd($response);

    $number = $response['hits']['total']['value'];
    $records = $response['hits']['hits'];
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $paginate = new LengthAwarePaginator($records, $number, $perPage, $currentPage);
    // dd(\Request::getRequestUri());
    $paginate->setPath($request->getBaseUrl() . \Request::getRequestUri());
    return view('record.results', compact('records', 'number', 'paginate', 'queryString'));
  }



  public function image($id){
    $hosts = [
      'http://api.fitz.ms:9200',        // SSL to localhost
    ];
    $client = ClientBuilder::create()->setHosts($hosts)->build();
    $params = [
      'index' => 'ciim',
      'body'  => [
        'query' => [
          'match' => [
            'multimedia.admin.id' => $id
          ]
        ]
      ]
    ];

    $response = $client->search($params);
    $data = $response['hits']['hits'][0]['_source']['multimedia'];

    function filter_array($array, $term){
      $matches = array();
      foreach($array as $a){
        if($a['admin']['id'] == $term)
        $matches[] = $a;
      }
      return $matches;
    }
    $filtered = filter_array($data,$id);
    return view('record.image', compact('filtered'));
  }
}
