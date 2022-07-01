<?php

namespace App\Models\Api;

use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;

class Makers extends Model
{
    private static array $_fields = array(
        'admin.id', 'admin.created', 'admin.modified',
        'name', 'summary_title', 'description',
        'equivalent', 'parent', 'related', 'identifier'
    );

    private static array $_mandatory = array('admin.id', 'admin.created', 'admin.modified', 'summary_title', 'name');

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
                    'records' => [
                        'terms' => [
                            'field' => 'lifecycle.creation.maker.admin.id',
                            'size' => 6000,
                            'order' => [
                                '_count' => self::getSortParam($request),
                            ],
                        ],
                        'aggs' => [
                            'maker' => [
                                'top_hits' => [
                                    'size' => 1,
                                    '_source' => [
                                        'include' => [
                                            'lifecycle.creation.maker.summary_title',
                                            'lifecycle.creation.maker.admin.id'
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $params = self::createQuery($request, $params);

        return self::searchAndCache($params);
    }

    /**
     * @param Request $request
     * @param string $maker
     * @return array
     */
    public static function show(Request $request, string $maker) : array
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                'query' => [
                    'match' => [
                        'admin.id' => Purifier::clean($maker, array('HTML.Allowed' => ''))
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
