<?php

namespace App\Models\Api;

use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;

class Places extends Model
{

    public static array $_fields = ['admin.id', 'admin.created', 'admin.modified', 'name', 'summary_title', 'description', 'equivalent', 'parent', 'related', 'identifier'];
    public static array $_mandatory =  ['admin.id', 'admin.created', 'admin.modified', 'name', 'summary_title', 'description', 'equivalent', 'parent', 'related', 'identifier'];

    public static function list(Request $request)
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
                                                'lifecycle.creation.places.admin.id',
                                            ]
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ];

        $params = self::createQuery($request, $params);
        return self::searchAndCache($params);
    }

    public static function show(Request $request, string $place)
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
}
