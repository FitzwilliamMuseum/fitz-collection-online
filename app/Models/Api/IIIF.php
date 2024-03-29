<?php

namespace App\Models\Api;

use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;

class IIIF extends Model
{
    /**
     * @var array
     */
    private static array $objects = array(
        'objects.admin.id',
        'objects.admin.source',
        'objects.summary_title'
    );

    /**
     * @var array
     */
    private static array $admin = array(
        'admin.id',
        'admin.created',
        'admin.modified'
    );
    /**
     * @var array
     */
    private static array $exif = array(
        'source.attributes.EXIF_-_ISO_Speed',
        'source.attributes.Cataloged',
        'source.attributes.Copyright',
        'source.attributes.EXIF_-_Camera_Make',
        'source.attributes.EXIF_-_Camera_Model',
        'source.attributes.EXIF_-_Exposure_Time',
        'source.attributes.EXIF_-_Focal_Length',
        'source.attributes.EXIF_-_ISO_Speed',
        'source.attributes.EXIF_-_Shutter_Speed',
        'source.attributes.File_Description',
        'source.attributes.IPTC_-_Rights_Usage_Terms',
        'source.attributes.Keywords'
    );

    /**
     * @var array
     */
    private static array $images = array(
        'processed.large',
        'processed.medium',
        'processed.preview',
        'processed.zoom',
        'processed.original',
    );

    /**
     * @param Request $request
     * @return array
     */
    public static function list(Request $request):array
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
                                "term" => ["type.base" => 'media']
                            ]
                        ]
                    ]
                ],

            ],
            '_source' => [
                implode(',', array_merge(self::$objects, self::$images, self::$admin))
            ],

        ];
        $filter = array(
            "exists" => [
                "field" => "processed.zoom"
            ]
        );
        $params['body']['query']['bool']['must'][] = [$filter];
        $createdBefore = self::createdBeforeParam($request);
        $createdAfter = self::createdAfterParam($request);
        $modifiedBefore = self::modifiedBeforeParam($request);
        $modifiedAfter = self::modifiedAfterParam($request);
        $combined = array_merge_recursive($params, $createdBefore, $createdAfter, $modifiedBefore, $modifiedAfter);
        return self::searchAndCache($combined);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return array|NULL
     */
    public static function show(Request $request, string $id) : ?array
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'match' => [
                                    'admin.id' => $id
                                ]
                            ],
                            [
                                'term' => [
                                    'type.base' => 'media'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            '_source' => [
//                implode(',', array_merge(self::$objects, self::$images, self::$admin))
            'admin,objects,multimedia'
            ],
        ];
        return Collect(self::parse(self::searchAndCache($params)))->first();
    }


    /**
     * @param Request $request
     * @return array
     */
    public static function listNumbers(Request $request):array
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
                                "term" => ["type.base" => 'media']
                            ]
                        ]
                    ]
                ],

            ],
            '_source' => [
                'admin.id'
            ],

        ];
        $filter = array(
            "exists" => [
                "field" => "processed.zoom"
            ]
        );
        $params['body']['query']['bool']['must'][] = [$filter];
        $createdBefore = self::createdBeforeParam($request);
        $createdAfter = self::createdAfterParam($request);
        $modifiedBefore = self::modifiedBeforeParam($request);
        $modifiedAfter = self::modifiedAfterParam($request);
        $combined = array_merge_recursive($params, $createdBefore, $createdAfter, $modifiedBefore, $modifiedAfter);
        return self::searchAndCache($combined);
    }
}

