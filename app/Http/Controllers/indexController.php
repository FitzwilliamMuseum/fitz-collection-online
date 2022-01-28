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
use App\Models\FindMoreLikeThis;
use League\Csv\Writer;

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

  /**
   * [record description]
   * @param  [type] $priref [description]
   * @return [type]         [description]
   */
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
    if(empty($data)){
      abort('404');
    }
    // $data = $this->replaceKeys('@link', 'link', $data);
    if(array_key_exists('summary_title',$data[0]['_source'])) {
      $query = $data[0]['_source']['summary_title'];
      $shopify = FindMoreLikeThis::find($data[0]['_source']['title'][0]['value'] ?? $query, 'shopify');
      $research = FindMoreLikeThis::find($data[0]['_source']['title'][0]['value'] ?? $query, '*');
    } else {
      $shopify = NULL;
      $research = NULL;
    }
    if(array_key_exists('title',$data[0]['_source'] )){
      $query .= ' ';
      $query .=  $data[0]['_source']['title'][0]['value'];
    }
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
      'size' => 4,
      'body'  => $json
    ];
    $response2 = $this->getElastic()->setParams($paramsMLT)->getSearch();
    $mlt = $response2['hits']['hits'];
    $palette = '';
    if(array_key_exists('multimedia', $data['0']['_source'])){
      if(array_key_exists('large', $data[0]['_source']['multimedia'][0]['processed'])){
        $image = $data[0]['_source']['multimedia'][0]['processed']['large']['location'];
        $path = env('CIIM_IMAGE_URL') . $image;
        $palette = ColorThief::getPalette( $path, 12);
        $reader = Reader::factory(Reader::TYPE_NATIVE);
        $exif = $reader->read($path);
      } else {
        $exif = NULL;
      }
    } else {
      $exif = NULL;
    }
    return view('record.index', compact('data', 'mlt', 'exif', 'shopify', 'research', 'palette'));
  }
  /**
   * [recordSwitch description]
   * @param  string $priref [description]
   * @param  string $format [description]
   * @return [type]         [description]
   */
  public function recordSwitch(string $priref, string $format) {

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
    if($format === 'json'){
      return response(view('record.json',array('data' => $data[0]['_source'])),200, ['Content-Type' => 'application/json']);
    } elseif ($format === 'txt') {
      return response(view('record.txt',array('data' => $data[0]['_source'])),200, ['Content-Type' => 'text/plain']);
    } elseif ($format === 'qr'){
      return response(view('record.qr', array('data' => $data[0]['_source'])),200);
    } elseif ($format === 'csv'){
      $header = array_keys($data[0]['_source']);
      dd($header);
      $records = array_values($data[0]['_source']);
      $csv = Writer::createFromString();
      $csv->insertOne($header);
      $csv->insertAll($records);
      return response($csv->toString(), 200, [
          'Content-Encoding' => 'none',
          'Content-Type' => 'text/csv; charset=UTF-8',
          'Content-Disposition' => 'attachment; filename="name-for-your-file.csv"',
          'Content-Description' => 'File Transfer',
      ]);
    } elseif($format === 'xml'){
      $data = $this->utf8_converter($data[0]['_source']);
      $data = $this->replaceKeys('@link', 'link', $data);
      $arrayToXml = new ArrayToXml($data);
      $xml = $arrayToXml->prettify()->toXml();
      return response($xml, 200)->header('Content-Type', 'application/xml');
    } else {
      abort('404');
    }
  }

  /**
   * [replaceKeys description]
   * @param  [type] $oldKey [description]
   * @param  [type] $newKey [description]
   * @param  array  $input  [description]
   * @return [type]         [description]
   */
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

  /**
   * [utf8_converter description]
   * @param  [type] $array [description]
   * @return [type]        [description]
   */
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
    return \Redirect::to(env('MAIN_URL') . '/objects-and-artworks');

    $latest = '[
      "query" => [
        "bool" => [
            "must" => [
               [
                    "match" => [
                      "reference_links" => $id
                    ]
               ],
               [
                    "term" => [ "type.base" => "object"]
               ],
               [
                    "exists" => ["field" => "multimedia"]
               ],
            ]
         ]
      ]
    ],';
    $lParams = [
      'index' => 'ciim',
      'size' => 9,
      'body' => [
        "query" => [
          "bool" => [
              "must" => [

                 [
                      "term" => [ "type.base" => 'object']
                 ],
                 [
                      "exists" => ['field' => 'multimedia']
                 ],
              ]
           ]
        ],
        'sort' => [
          "admin.modified" =>  [
            "order" =>  'DESC'
            ]
        ]
      ],
    ];
    $filter  =  array("exists" => [
      "field" => "multimedia"]
    );
    $response = $this->getElastic()->setParams($lParams)->getSearch();
    $records = $response['hits']['hits'];
    return view('record.search', compact('records'));
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
                  "fields" => ["_generic_all_std","accession_number^3"],
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
  'material' => [
    'terms' =>
    [
      "field" => 'materials.reference.summary_title.keyword',
      "size" => 10
    ]
  ],
  'period' => [
    'terms' =>
      [
       "field" => 'lifecycle.creation.periods.summary_title.keyword',
       "size" => 10
       ]
  ],
  'object_type' => [
      'terms' =>
      [
        "field" => 'name.reference.summary_title.keyword',
        "size" => 10
      ]
  ],
  'maker' => [
    'terms' =>
        [
        "field" => 'lifecycle.creation.maker.summary_title.keyword',
        "size" => 10
        ]
  ],
  'department' => [
      'terms' =>
        [
          "field" => 'department.value.keyword',
          "size" => 10
          ]
        ]
    );
    $params['body']['aggs'] = $facets;

    // Add images filter
    if(!is_null($request->get('images'))){
      $filter  =  array("exists" => [
        "field" => "multimedia"
        ]
      );
      array_push($params['body']['query']['bool']['must'], [$filter]);
    }
    // Maker filter
    if(!is_null($request->get('maker'))){
      $filter  =  array("term" => [
        "lifecycle.creation.maker.summary_title.keyword" => $request->get('maker')]
      );
      array_push($params['body']['query']['bool']['must'], [$filter]);
    }
    // Add iiif filter
    if(!is_null($request->get('iiif'))){
      $filter  =  array("exists" => [
        "field" => "multimedia.processed.zoom"
        ]
      );
      array_push($params['body']['query']['bool']['must'], [$filter]);
    }
    //  Material filter
    if(!is_null($request->get('material'))){
      $filter  =  array("term" => [
        "materials.reference.summary_title.keyword" => $request->get('material')]
      );
      array_push($params['body']['query']['bool']['must'], [$filter]);
    }
    // Period filter
    if(!is_null($request->get('period'))){
      $filter  =  array("term" => [
        "lifecycle.creation.periods.summary_title.keyword" => $request->get('period')]
      );
      array_push($params['body']['query']['bool']['must'], [$filter]);
    }

    // Period filter
    if(!is_null($request->get('department'))){
      $filter  =  array("term" => [
        "department.value.keyword" => $request->get('department')]
      );
      array_push($params['body']['query']['bool']['must'], [$filter]);
    }

    // Accession filter
    if(!is_null($request->get('accession_number'))){
      $filter  =  array("term" => [
        "identifier.accession_number" => $request->get('accession_number')]
      );
      array_push($params['body']['query']['bool']['must'], [$filter]);
    }

    // Period filter
    if(!is_null($request->get('object_type'))){
      $filter  =  array("term" => [
        "name.reference.summary_title.keyword" => $request->get('object_type')]
      );
      array_push($params['body']['query']['bool']['must'], [$filter]);
    }

    // Add sort filter
    // if(!is_null($request->get('sort'))){
      $order = $request->get('sort');
      $sort = array(
        "multimedia.admin.id" => [
          "order" =>  'ASC',
          "missing" => '_last'
          ]
        ,
        // "admin.modified" =>  [
        //   "order" =>  'ASC'
        //   ]
        );
        $params['body']['sort'] = $sort;
      // }
      // Get response
      $response = $this->getElastic()->setParams($params)->getSearch();

      $number = $response['hits']['total']['value'];
      $records = $response['hits']['hits'];
      $facets = $response['aggregations'];
      $currentPage = LengthAwarePaginator::resolveCurrentPage();
      $paginate = new LengthAwarePaginator($records, $number, $perPage, $currentPage);
      $paginate->setPath($request->getBaseUrl());
      if(is_null($request->get('format'))){
        return view('record.results', compact('records', 'number', 'paginate', 'queryString', 'facets'));
      } else {
        return response(view('record.searchJson',array('data' => array('results' => $paginate->items(),'total' => $paginate->total()))),200, ['Content-Type' => 'application/json']);      }
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
      $filtered = filter_array($data, $id);
      $image = $data[0]['processed']['large']['location'];
      $path = env('CIIM_IMAGE_URL') . $image;
      $palette = ColorThief::getPalette( $path, 12);
      $reader = Reader::factory(Reader::TYPE_NATIVE);
      $exif = $reader->read($path);

      $paramsTerm = [
        'index' => 'ciim',
        'size' => 1,
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
      $object = $response2['hits']['hits'][0]['_source'];
      return view('record.image', compact('filtered', 'object','palette', 'exif'));
    }

    public function slowiiif(Request $request){
      $params = [
        'index' => 'ciim',
        'body'  => [
          'query' => [
            'match' => [
              'multimedia.admin.id' => $request->get('image')
            ]
          ]
        ]
      ];

      $response = $this->getElastic()->setParams($params)->getSearch();
      $object = $response['hits']['hits'][0]['_source'];
      return view('record.slow', compact('object'));
    }

    public function iiif($id){

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
      $filtered = filter_array($data, $id);
      $image = $data[0]['processed']['large']['location'];
      $path = env('CIIM_IMAGE_URL') . $image;
      $palette = ColorThief::getPalette( $path, 12);
      $reader = Reader::factory(Reader::TYPE_NATIVE);
      $exif = $reader->read($path);

      $paramsTerm = [
        'index' => 'ciim',
        'size' => 1,
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
      $object = $response2['hits']['hits'][0]['_source'];
      return view('record.iiif', compact('filtered', 'object','palette', 'exif'));
    }

    public function sketchfab($id){

      $paramsTerm = [
        'index' => 'ciim',
        'size' => 1,
        'body' => [
          "query" => [
            "bool" => [
                "must" => [
                   [
                        "match" => [
                          "identifier.priref" => $id
                        ]
                   ],
                   [
                        "term" => [ "type.base" => 'object']
                   ],

                ]
             ]
          ]
        ],
      ];
      $response2 = $this->getElastic()->setParams($paramsTerm)->getSearch();
      $data = $response2['hits']['hits'][0];
      return view('record.3d', compact('data'));
    }

    public function mirador(string $id)
    {
      $paramsTerm = [
        'index' => 'ciim',
        'size' => 1,
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
      $object = $response2['hits']['hits'][0]['_source'];
      return view('record.mirador', compact('object'));
    }

    public function randomsearch()
    {
      $random = new \stdClass();
      $random->seed = time();
      $params = [
        'index' => 'ciim',
        'size' => 1,
        'body' => []
      ];
      $params['body']['query']['function_score']['functions'][]['random_score'] = $random;
      $params['body']['query']['function_score']['query']['bool']['must'] = [
         [
              "match" => [
                "department.value" => "Paintings, Drawings and Prints"
              ]
         ],
         [
              "term" => [ "type.base" => 'object']
         ],
         [
              "exists" => ['field' => 'multimedia']
         ],
      ];

      $response = $this->getElastic()->setParams($params)->getSearch();
      return $response['hits']['hits'][0]["_source"];
    }


    public function randomsearchapp()
    {
      $random = new \stdClass();
      $random->seed = time();
      $params = [
        'index' => 'ciim',
        'size' => 1,
        'body' => []
      ];
      $params['body']['query']['function_score']['functions'][]['random_score'] = $random;
      $params['body']['query']['function_score']['query']['bool']['must'] = [
         [
              "match" => [
                "department.value" => "Paintings, Drawings and Prints"
              ]
         ],
         [
              "term" => [ "type.base" => 'object']
         ],
         [
              "exists" => ['field' => 'multimedia']
         ],
      ];

      $response = $this->getElastic()->setParams($params)->getSearch();
      $data = array();
      $data['data'][] = $response['hits']['hits'][0]["_source"];
      return $data;
    }
  }
