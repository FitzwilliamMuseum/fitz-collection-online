<?php
namespace App\Models\Api;

use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;


class Institutions extends Model
{
    private static array $_fields = array(
        'admin.id','admin.created','admin.modified','name','summary_title'
    );
    private static array $_mandatory  = array(
        'admin.id','admin.created','admin.modified','name','summary_title'
    );

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
                    'institutions' => [
                        'terms' => [
                            'field' => 'institutions.admin.id',
                            'size' => 10,
                            'order' => [
                                '_count' => self::getSortParam($request),
                            ],
                        ],
                        'aggs' => [
                            'inst' => [
                                'top_hits' => [
                                    'size' => 1,
                                    '_source' => [
                                        'include' => [
                                            0 => 'institutions.summary_title',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        return self::searchAndCache($params);
    }

    /**
     * @param Request $request
     * @param string $institution
     * @return array
     */
    public static function show(Request $request, string $institution) : array
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "match" => [
                                    "admin.id" => Purifier::clean($institution, array('HTML.Allowed' => '')),
                                ]
                            ],
                            [
                                "term" => [
                                    "type.base" => 'agent'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            '_source' => [
                self::getSourceFields($request, self::$_fields, self::$_mandatory),
            ],
        ];
        return  Collect(self::parse(self::searchAndCache($params)))->first();
    }
}
