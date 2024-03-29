<?php
namespace App\Models\Api;

use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;


class Exhibitions extends Model
{
    /**
     * @var array
     */
    private static array $_fields = array(
        'admin.id','admin.created','admin.modified',
        'venues','summary_title','name.value',
        'title.value'
    );
    /**
     * @var array
     */
    private static array $_mandatory  = array(
        'admin.id','admin.created','admin.modified',
        'venues','summary_title','name.value',
        'title.value'
    );

    /**
     * @param Request $request
     * @return array
     */
    public static function list(Request $request): array
    {
        $params = [
            'index' => 'ciim',
            'size' => self::getSize($request),
            'from' => self::getFrom($request),
            'track_total_hits' => true,
            'body' => [
                'sort' => self::getSort($request),
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "term" => ["type.base" => 'exhibition']
                            ]
                        ]
                    ]
                ],
            ],
            '_source' => [
                parent::getSourceFields($request, self::$_fields, self::$_mandatory)
            ],
        ];
        $query = self::createQuery($request);
        $createdBefore = self::createdBeforeParam($request);
        $createdAfter = self::createdAfterParam($request);
        $modifiedBefore = self::modifiedBeforeParam($request);
        $modifiedAfter = self::modifiedAfterParam($request);

        $combined = array_merge_recursive(
            $params, $query, $createdBefore,
            $createdAfter, $modifiedBefore, $modifiedAfter
        );
        return self::searchAndCache($combined);
    }

    /**
     * @param Request $request
     * @param string $exhibition
     * @return array|NULL
     */
    public static function show(Request $request, string $exhibition) : ?array
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "match" => [
                                    "admin.id" =>  Purifier::clean($exhibition, array('HTML.Allowed' => ''))
                                ]
                            ],
                            [
                                "term" => ["type.base" => 'exhibition']
                            ]
                        ]
                    ]
                ]
            ],
            '_source' => [
                parent::getSourceFields($request, self::$_fields, self::$_mandatory)
            ],
        ];
        return  Collect(self::parse(self::searchAndCache($params)))->first();
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
                'sort' => self::getSort($request),
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "term" => ["type.base" => 'exhibition']
                            ]
                        ]
                    ]
                ],
            ],
            '_source' => [
                'admin.id'
            ],
        ];
        $createdBefore = self::createdBeforeParam($request);
        $createdAfter = self::createdAfterParam($request);
        $modifiedBefore = self::modifiedBeforeParam($request);
        $modifiedAfter = self::modifiedAfterParam($request);
        $combined = array_merge_recursive($params, $createdBefore, $createdAfter, $modifiedBefore, $modifiedAfter);
        return self::searchAndCache($combined);
    }
}
