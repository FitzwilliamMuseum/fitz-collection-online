<?php

namespace App\Models\Api;

use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;

class Publications extends Model
{
    /**
     * @var array
     */
    private static array $_fields = array('admin.id','summary_title','lifecycle','title','type.base');

    /**
     * @var array
     */
    private static array $_mandatory = array('admin.id','summary_title');

    /**
     * @param Request $request
     * @return array
     */
    public static function list(Request $request): array
    {
        $params = [
            'index' => 'ciim',
            'size' => self::getSize($request),
            'track_total_hits' => true,
            'from' => self::getFrom($request),
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'term' => [
                                    'type.base' => 'publication'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            '_source' => [
                self::getSourceFields($request, self::$_fields, self::$_mandatory)
            ],
        ];
        $params['body']['sort'] = parent::getSort($request);
        $createdBefore = self::createdBeforeParam($request);
        $createdAfter = self::createdAfterParam($request);
        $modifiedBefore = self::modifiedBeforeParam($request);
        $modifiedAfter = self::modifiedAfterParam($request);
        $combined = array_merge_recursive(
            $params, self::createQueryPublications($request), $createdBefore,
            $createdAfter, $modifiedBefore, $modifiedAfter
        );
        return self::searchAndCache($combined);
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
            'track_total_hits' => true,
            'from' => self::getFromID($request),
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'term' => [
                                    'type.base' => 'publication'
                                ]
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
        $combined = array_merge_recursive(
            $params, self::createQueryPublications($request), $createdBefore,
            $createdAfter, $modifiedBefore, $modifiedAfter
        );
        return self::searchAndCache($combined);
    }
    /**
     * @param Request $request
     * @param string $publication
     * @return array|NULL
     */
    public static function show(Request $request, string $publication): ?array
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                'query' => [
                    'match' => [
                        'admin.id' => Purifier::clean($publication, array('HTML.Allowed' => ''))
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
