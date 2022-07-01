<?php
namespace App\Models\Api;

use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;


class Agents extends Model
{
    /**
     * @var array|string[]
     */
    public static array $_mandatory = array('admin.id', 'admin.created', 'admin.modified', 'name', 'summary_title');
    /**
     * @var array
     */
    public static array $_fields = array('admin.id', 'admin.created', 'admin.modified', 'name', 'summary_title', 'type.base', 'related');

    /**
     * @param Request $request
     * @return array
     */
    public static function list(Request $request): array
    {
        $params = [
            'index' => 'ciim',
            'size' => parent::getSize($request),
            'from' => parent::getFrom($request),
            'track_total_hits' => true,
            'body' => [
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "term" => ["type.base" => 'agent']
                            ]
                        ]
                    ]
                ]
            ],
            '_source' => [
                parent::getSourceFields($request, self::$_fields, self::$_mandatory)
            ],
        ];
        $params = parent::createQuery($request, $params);
        $params['body']['sort'] = parent::getSort($request);
        return parent::searchAndCache($params);
    }

    /**
     * @param Request $request
     * @param string $term
     * @return array|NULL
     */
    public static function show(Request $request, string $term): ?array
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "match" => [
                                    "admin.id" => Purifier::clean($term, array('HTML.Allowed' => ''))
                                ]
                            ],
                            [
                                "term" => ["type.base" => 'agent']
                            ]
                        ]
                    ]
                ]
            ],
            '_source' => [
                parent::getSourceFields($request, self::$_fields, self::$_mandatory)
            ],
        ];
        return Collect(self::parse(self::searchAndCache($params)))->first();
    }

}
