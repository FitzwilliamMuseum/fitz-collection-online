<?php

namespace App\Models\Api;

use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;

class Places extends Model
{

    /**
     * @var array
     */
    public static array $_fields = [
        'admin.id', 'admin.created', 'admin.modified',
        'name', 'summary_title', 'description',
        'equivalent', 'parent', 'related',
        'identifier'
    ];
    /**
     * @var array|string[]
     */
    public static array $_mandatory = [
        'admin.id', 'admin.created', 'admin.modified',
        'name', 'summary_title', 'description',
        'equivalent', 'parent', 'related',
        'identifier'
    ];

    /**
     * @param Request $request
     * @return array
     */
    public static function list(Request $request): array
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                'aggregations' => [
                    'places' => [
                        'terms' => [
                            'field' => 'lifecycle.creation.places.admin.id',
                            'size' => 3800,
                            'order' => [
                                '_count' => self::getSortParam($request),
                            ],
                        ],
                        'aggs' => [
                            'place' => [
                                'top_hits' => [
                                    'size' => 1,
                                    '_source' => [
                                        'include' =>
                                            [
                                                'lifecycle.creation.places.summary_title',
                                                'lifecycle.creation.places.admin.id'
                                            ]
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ];
        $combined = array_merge_recursive($params, self::createQueryPlaces($request));
        return self::searchAndCache($combined);
    }

    /**
     * @param Request $request
     * @param string $place
     * @return array|NULL
     */
    public static function show(Request $request, string $place): array
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "match" => [
                                    "admin.id" => Purifier::clean($place, array('HTML.Allowed' => ''))
                                ]
                            ],
                            [
                                "term" => ["type.base" => 'term']
                            ]
                        ]
                    ]
                ]
            ],
            '_source' => [
                self::getSourceFields($request, self::$_fields, self::$_mandatory)
            ],
        ];
        return Collect(self::parse(self::searchAndCache($params)))->first();
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function listNumbers(Request $request): array
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                'aggregations' => [
                    'places' => [
                        'terms' => [
                            'field' => 'lifecycle.creation.places.admin.id',
                            'size' => 3800,
                            'order' => [
                                '_count' => self::getSortParam($request),
                            ],
                        ],
                        'aggs' => [
                            'place' => [
                                'top_hits' => [
                                    'size' => 1,
                                    '_source' => [
                                        'include' =>
                                            [
                                                'lifecycle.creation.places.summary_title',
                                                'lifecycle.creation.places.admin.id'
                                            ]
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ];
        $combined = array_merge_recursive($params, self::createQueryPlaces($request));
        return self::searchAndCache($combined);
    }
}
