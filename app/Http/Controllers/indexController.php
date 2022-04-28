<?php

namespace App\Http\Controllers;

use App\Models\FindMoreLikeThis;
use ColorThief\ColorThief;
use DOMException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\Writer;
use PHPExif\Reader\Reader;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\Facades\Redirect;
use Psr\SimpleCache\InvalidArgumentException;
use Spatie\ArrayToXml\ArrayToXml;
use stdClass;
use App\Models\CIIM;
use App\Models\SpoliationClaims;

class indexController extends Controller
{
    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $perPage = 24;
        $total = 8000;
        $page = $request['page'];
        if (!is_null($page)) {
            $offset = ($page - 1) * $perPage;
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
     * @param int $priref
     * @return View
     * @throws InvalidArgumentException
     */
    public function record(int $priref): View
    {
        $params = [
            'index' => 'ciim',
            'size' => 1,
            'body' => [
                'query' => [
                    'match' => [
                        'identifier.priref' => $priref
                    ]
                ]
            ]
        ];

        $response = $this->getElastic()->setParams($params)->getSearch();
        $data = $response['hits']['hits'];
        if (empty($data)) {
            abort('404');
        }
        if (array_key_exists('summary_title', $data[0]['_source'])) {
            $query    = $data[0]['_source']['summary_title'];
            $shopify  = FindMoreLikeThis::find($data[0]['_source']['title'][0]['value'] ?? $query, 'shopify');
            $research = FindMoreLikeThis::find($data[0]['_source']['title'][0]['value'] ?? $query, '*');
        } else {
            $shopify = NULL;
            $research = NULL;
        }
        $query = '';
        if (array_key_exists('title', $data[0]['_source'])) {
            $query .= $data[0]['_source']['title'][0]['value'];
        }
        $id = $data[0]['_id'];
        $string = '{ "_id" : "' . $id . '"},"' . urlencode($query) . '"';
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
            'body' => $json
        ];
        $response2 = $this->getElastic()->setParams($paramsMLT)->getSearch();
        $mlt = $response2['hits']['hits'];
        $palette = '';
        if (array_key_exists('multimedia', $data['0']['_source'])) {
            if (array_key_exists('large', $data[0]['_source']['multimedia'][0]['processed'])) {
                $image = $data[0]['_source']['multimedia'][0]['processed']['large']['location'];
                $path = env('CIIM_IMAGE_URL') . $image;
                $palette = ColorThief::getPalette($path, 12);
                $reader = Reader::factory(Reader::TYPE_NATIVE);
                $exif = $reader->read($path);
            } else {
                $exif = NULL;
            }
        } else {
            $exif = NULL;
        }
        $spoliation = SpoliationClaims::find($priref)['data'];
        return view('record.index', compact('data', 'mlt', 'exif', 'shopify', 'research', 'palette', 'spoliation'));
    }

    /**
     * @param string $priref
     * @param string $format
     * @return Application|ResponseFactory|Response|void
     * @throws DOMException
     * @throws CannotInsertRecord
     * @throws Exception
     */
    public function recordSwitch(string $priref, string $format)
    {
        $params = [
            'index' => 'ciim',
            'size' => 1,
            'body' => [
                'query' => [
                    'match' => [
                        'identifier.priref' => $priref
                    ]
                ]
            ]
        ];
        $response = $this->getElastic()->setParams($params)->getSearch();
        $data = $response['hits']['hits'];
        if ($format === 'json') {
            return response(view('record.json', array('data' => $data[0]['_source'])), 200, ['Content-Type' => 'application/json']);
        } elseif ($format === 'txt') {
            return response(view('record.txt', array('data' => $data[0]['_source'])), 200, ['Content-Type' => 'text/plain']);
        } elseif ($format === 'qr') {
            return response(view('record.qr', array('data' => $data[0]['_source'])), 200);
        } elseif ($format === 'csv') {
            $header = array_keys($data[0]['_source']);
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
        } elseif ($format === 'xml') {
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
     * @param $array
     * @return array
     */
    public function utf8_converter($array): array
    {
        array_walk_recursive($array, function (&$item, $key) {
            if (!mb_detect_encoding($item, 'utf-8', true)) {
                $item = utf8_encode($item);
                $item = str_replace('\u', 'u', $item);
                $item = preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', $item);

            }
        });
        return $array;
    }

    /**
     * @param $oldKey
     * @param $newKey
     * @param array $input
     * @return array
     */
    public function replaceKeys($oldKey, $newKey, array $input): array
    {
        $return = array();
        foreach ($input as $key => $value) {
            if ($key === $oldKey)
                $key = $newKey;
            if (is_array($value))
                $value = $this->replaceKeys($oldKey, $newKey, $value);
            $return[$key] = $value;
        }
        return $return;
    }

    /**
     * @return RedirectResponse
     */
    public function search(): RedirectResponse
    {
        return Redirect::to(env('MAIN_URL') . '/objects-and-artworks');
    }

    /**
     * @param Request $request
     * @return View|Response
     * @throws ValidationException
     */
    public function results(Request $request): View|Response
    {
        $this->validate($request, [
            'query' => 'required|max:200|min:3',
        ]);
        $queryString = Purifier::clean($request->get('query'), array('HTML.Allowed' => ''));
        $response = CIIM::getSearchResults( $request);
        $records = $response['records'];
        $facets = $response['aggregations'];
        if (is_null($request->get('format'))) {
            return view('record.results', compact('records', 'queryString', 'facets'));
        } else {
            return response(view('record.searchJson', array('data' => array('results' => $records->items(), 'total' => $records->items()['hits']['total']))), 200, ['Content-Type' => 'application/json']);
        }
    }


    /**
     * @param array|Collection $items
     * @param int $perPage
     * @param int|null $page
     * @param array $options
     * @return LengthAwarePaginator
     */
    public function paginate(array|Collection $items, int $perPage = 24, int|null $page = NULL, array $options = []): LengthAwarePaginator
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }


    /**
     * @return array
     */
    public function randomsearch(): array
    {
        $random = new stdClass();
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
                "term" => ["type.base" => 'object']
            ],
            [
                "exists" => ['field' => 'multimedia']
            ],
        ];

        $response = $this->getElastic()->setParams($params)->getSearch();
        return $response['hits']['hits'][0]["_source"];
    }

    /**
     * @return array
     */
    public function randomsearchapp(): array
    {
        $random = new stdClass();
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
                "term" => ["type.base" => 'object']
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
