<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Elasticsearch\ClientBuilder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Spatie\ArrayToXml\ArrayToXml;
use ColorThief\ColorThief;
use PHPExif\Reader\Reader;

class indexController extends Controller
{
  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index(Request $request)
  {

    $perPage = 24;
    $total = 8000;
    $page = $request->page;
    if(!is_null($page)){
      $offset = ($page -1) * $perPage ;
    } else {
      $offset = 0;
    }
    $params = [
      'index' => 'ciim',
      'size' => $perPage,
      'from' => $offset

    ];
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $response = $this->getElastic()->setParams($params)->getSearch();
    $data = $response['hits']['hits'];
    $paginator = new LengthAwarePaginator($data, $total, $perPage, $currentPage);
    $paginator->setPath('/spelunker');
    return view('index', compact('data', 'paginator'));
  }

  public function record($priref) {

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
    $response = $this->getElastic()->setParams($params)->getSearch();
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
    $response2 = $this->getElastic()->setParams($paramsMLT)->getSearch();
    $mlt = $response2['hits']['hits'];
    return view('record.index', compact('data', 'mlt'));
  }

  public function recordSwitch($priref,$format) {

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
    $response = $this->getElastic()->setParams($params)->getSearch();
    $data = $response['hits']['hits'];
    if($format == 'json'){
      return response(view('record.json',array('data' => $data[0]['_source'])),200, ['Content-Type' => 'application/json']);
    } elseif($format == 'xml'){
      $data = $this->utf8_converter($data[0]['_source']);
      $data = $this->replaceKeys('@link', 'link', $data);
      $arrayToXml = new ArrayToXml($data);
      $xml = $arrayToXml->prettify()->toXml();
      return response($xml, 200)->header('Content-Type', 'application/xml');
    } else {
      abort('404');
    }
  }


  public function replaceKeys($oldKey, $newKey, array $input){
    $return = array();
    foreach ($input as $key => $value) {
      if ($key===$oldKey)
      $key = $newKey;

      if (is_array($value))
      $value = $this->replaceKeys( $oldKey, $newKey, $value);

      $return[$key] = $value;
    }
    return $return;
  }

  public function utf8_converter($array){
    array_walk_recursive($array, function(&$item, $key){
      if(!mb_detect_encoding($item, 'utf-8', true)){
        $item = utf8_encode($item);
        $item = str_replace('\u','u',$item);
        $item = preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', $item);

      }
    });
    return $array;
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
      env('ELASTIC_API'),        // SSL to localhost
    ];
    $this->validate($request, [
      'query' => 'required|max:200|min:3',
    ]);

    $queryString = \Purifier::clean($request->get('query'), array('HTML.Allowed' => ''));
    $perPage = 24;
    $from = ($request->get('page', 1) - 1) * $perPage;
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
                  "operator" =>  $operator,
                ],

              ],

            ],
            "filter" =>
            [
              "term"=> [ "type.base" => 'object'],
            ],

          ]
        ],
      ],
    ];

    $facets = array(
      'institutions' => [
        'terms' =>
        [
          "field" => 'institutions.admin.id',
          "size" => 10,
          ]
      ],
      'materials' => [
        'terms' =>
        ["field" => 'materials.reference.summary_title.exact',"size" => 10]
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
              ["field" => 'lifecycle.creation.maker.admin.id',"size" => 10]
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
    // Maker filter
    if(!is_null($request->get('maker'))){
      $filter  =  array("term" => [
        "lifecycle.creation.maker.admin.id" => $request->get('maker')]
      );
      array_push($params['body']['query']['bool']['must'], [$filter]);
    }
    // Material filter
    if(!is_null($request->get('material'))){
      $filter  =  array("term" => [
        "materials.reference.admin.id" => $request->get('material')]
      );
      array_push($params['body']['query']['bool']['must'], [$filter]);
    }
    // Period filter
    if(!is_null($request->get('period'))){
      $filter  =  array("term" => [
        "lifecycle.creation.periods.admin.id" => $request->get('period')]
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
      // Get response
      $response = $this->getElastic()->setParams($params)->getSearch();

      $number = $response['hits']['total']['value'];
      $records = $response['hits']['hits'];
      $facets = $response['aggregations'];
      $currentPage = LengthAwarePaginator::resolveCurrentPage();
      $paginate = new LengthAwarePaginator($records, $number, $perPage, $currentPage);
      $paginate->setPath($request->getBaseUrl());
      return view('record.results', compact('records', 'number', 'paginate', 'queryString', 'facets'));
    }

    public function images(Request $request, $priref)
    {
      $perPage = 24;
      $page = $request->page;
      if(!is_null($page)){
        $offset = ($page -1) * $perPage ;
      } else {
        $offset = 0;
      }
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
      $response = $this->getElastic()->setParams($params)->getSearch();
      $data = $response['hits']['hits'];
      if(array_key_exists('multimedia', $data[0]['_source'])){
        $images = $data[0]['_source']['multimedia'];
        $total = count($images);
      } else {
        abort('404');
      }
      $paginate = $this->paginate($images);
      $paginate->setPath($request->getBaseUrl());
      return view('record.images', compact('paginate', 'data'));
    }

    public function paginate($items, $perPage = 24, $page = null, $options = [])
  {
      $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
      $items = $items instanceof Collection ? $items : Collection::make($items);
      return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
  }

    public function image($id){

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

      $response = $this->getElastic()->setParams($params)->getSearch();
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
      $image = $data[0]['processed']['large']['location'];
      $path = env('CIIM_IMAGE_URL') . $image;
      $palette = ColorThief::getPalette( $path, 12);
      $reader = Reader::factory(Reader::TYPE_NATIVE);
      $exif = $reader->read($path);
      return view('record.image', compact('filtered', 'palette', 'exif'));
    }
  }
