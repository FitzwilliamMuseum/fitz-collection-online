<?php

namespace App\Models\Api;

use Illuminate\Http\Request;

class Images extends Model
{

    /**
     * @var array|string[]
     */
    private static array $objects = array(
        'objects.admin.id',
        'objects.admin.source',
        'objects.summary_title'
    );

    /**
     * @var array|string[]
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
    public static function list(Request $request): array
    {
       $params =  [
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
                self::getFields()
            ],
        ];
        $query = self::createQueryObjects($request);
        $image = self::getImageParam($request);
        $iiif = self::getIiifParam($request);
        $combined = array_merge_recursive(
            $params, $image, $iiif, $query,
        );
        return self::searchAndCache($combined);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return array
     */
    public static function show(Request $request, string $id): array
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                'query' => [
                    'match' => [
                        'admin.id' => $id
                    ]
                ]
            ],
            '_source' => [
                self::getFields()
            ],
        ];

        return Collect(self::parse(self::searchAndCache($params)))->first();
    }

    private static function getFields()
    {
        return implode(',', array_merge(self::$exif, self::$objects, self::$images, self::$admin));
    }
}
