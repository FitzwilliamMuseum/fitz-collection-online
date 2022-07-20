<?php
namespace App\Models\Api;

use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;

class Agents extends Model
{
    /**
     * @var array|string[]
     */
    public static array $_mandatory = array(
        'admin.id', 'admin.created', 'admin.modified', 'name', 'summary_title');
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
        $params['body']['sort'] = parent::getSort($request);
        $createdBefore = self::createdBeforeParam($request);
        $createdAfter = self::createdAfterParam($request);
        $modifiedBefore = self::modifiedBeforeParam($request);
        $modifiedAfter = self::modifiedAfterParam($request);
        $combined = array_merge_recursive($params, self::createQueryAgents($request), $createdBefore, $createdAfter, $modifiedBefore, $modifiedAfter);
        return parent::searchAndCache($combined);
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

    /**
     * @param Request $request
     * @return array
     */
    public static function listNumbers(Request $request): array
    {
        $params = [
            'index' => 'ciim',
            'size' => self::getSizeID($request),
            'from' => self::getFromID($request),
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
                'admin.id'
            ],
        ];
        $params['body']['sort'] = parent::getSort($request);
        $createdBefore = self::createdBeforeParam($request);
        $createdAfter = self::createdAfterParam($request);
        $modifiedBefore = self::modifiedBeforeParam($request);
        $modifiedAfter = self::modifiedAfterParam($request);
        $combined = array_merge_recursive($params, self::createQueryAgents($request), $createdBefore, $createdAfter, $modifiedBefore, $modifiedAfter);
        return parent::searchAndCache($combined);
    }
}
