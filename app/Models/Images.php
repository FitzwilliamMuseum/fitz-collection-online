<?php

namespace App\Models;
use App\FitzElastic\Elastic;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\Pure;
use PhpParser\Node\Expr\Array_;

class Images extends Model
{

    /**
     * @return Elastic
     */
    #[Pure] public static function getElastic(): Elastic
    {
        return new Elastic();
    }

    /**
     * @param string $id
     * @return mixed
     */
    public static function getMirador(string $id): Array
    {
        return self::getElastic()->setParams([
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
                                "term" => ["type.base" => 'object']
                            ],
                            [
                                "exists" => ['field' => 'multimedia']
                            ],
                        ]
                    ]
                ]
            ],
        ])->getSearch()['hits']['hits'][0]['_source'];
    }

    /**
     * @param string $id
     * @return mixed
     */
    public static function getSketchFab(string $id): Array
    {
        return self::getElastic()->setParams([
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
                                "term" => ["type.base" => 'object']
                            ],

                        ]
                    ]
                ]
            ],
        ])->getSearch()['hits']['hits'][0];
    }

    public static function getIIIFData(Request $request)
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                'query' => [
                    'match' => [
                        'multimedia.admin.id' => $request->get('image')
                    ]
                ]
            ]
        ];
        $response = self::getElastic()->setParams($params)->getSearch();
        return $response['hits']['hits'][0]['_source'];
    }

    public static function getImageData(string $id)
    {
        return self::getElastic()->setParams([
            'index' => 'ciim',
            'body' => [
                'query' => [
                    'match' => [
                        'multimedia.admin.id' => $id
                    ]
                ]
            ]
        ])->getSearch()['hits']['hits'][0]['_source']['multimedia'];
    }

    public static function getObject(string $id)
    {
        return self::getElastic()->setParams([
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
                                "term" => ["type.base" => 'object']
                            ],
                            [
                                "exists" => ['field' => 'multimedia']
                            ],
                        ]
                    ]
                ]
            ],
        ])->getSearch()['hits']['hits'][0]['_source'];
    }

    public static function getIIIF(string $id)
    {
        return self::getElastic()->setParams([
            'index' => 'ciim',
            'body' => [
                'query' => [
                    'match' => [
                        'multimedia.admin.id' => $id
                    ]
                ]
            ]
        ])->getSearch()['hits']['hits'][0]['_source']['multimedia'];
    }
}
