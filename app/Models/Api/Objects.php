<?php

namespace App\Models\Api;

use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;

class Objects extends Model
{

    /**
     * @var array
     */
    private static array $_allowed = array(
        'admin.id',
        'admin.created',
        'admin.modified',
        'categories',
        'description',
        'component',
        'department.value',
        'identifier',
        'inscription',
        'lifecycle',
        'institutions',
        'multimedia',
        'name',
        'note',
        'owners',
        'publications',
        'school_or_style',
        'summary',
        'techniques',
        'measurements',
        'title',
        'materials'
    );

    /**
     * @var array
     */
    private static array $_mandatory = array(
        'admin.id',
        'admin.created',
        'admin.modified',
        'summary_title'
    );

    /**
     * @param Request $request
     * @return array
     */
    public static function list(Request $request): array
    {
        if (!$request->has('random')) {
            $params = [
                'index' => 'ciim',
                'size' => self::getSize($request),
                'from' => self::getFrom($request),
                'track_total_hits' => true,
                'body' => [
                    "query" => [
                        "bool" => [
                            "must" => [

                            ],
                            "filter" =>
                                [
                                    "term" => ["type.base" => 'object'],
                                ],
                        ]
                    ],
                ],
                '_source' => [
                    self::getSourceFields($request, self::$_allowed, self::$_mandatory),
                ],
            ];
            $params['body']['sort'] = self::getSort($request);
            $query = self::createQueryObjects($request);
        } else {
            $params = [
                'index' => 'ciim',
                'size' => self::getSize($request),
                'from' => self::getFrom($request),
                'body' => [
                    "query" => [
                        "function_score" => [
                            "random_score" => [],
                            "query" => [
                                "bool" => [
                                    "must" => [
                                        [
                                            "term" => ["type.base" => 'object'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                '_source' => [
                    self::getSourceFields($request, self::$_allowed, self::$_mandatory),
                ],
            ];
            $query = self::createRandomQueryObjects($request);
        }
        $image = self::getImageParam($request);
        $iiif = self::getIiifParam($request);
        $department = self::getDepartmentParam($request);
        $publications = self::getPublicationParam($request);
        $categories = self::getCategoriesParam($request);
        $periods = self::getPeriodsParam($request);
        $names = self::getNamesParam($request);
        $acquiredFrom = self::getAcquiredFrom($request);
        $collected = self::getCollectedFrom($request);
        $accession = self::getAccessionParam($request);
        $maker = self::getMaker($request);
        $school = self::getSchool($request);
        $start_date = self::getAcquisitionDate($request);
        $end_date = self::getAcquisitionEndDate($request);
        $techniques = self::getTechniquesParam($request);
        $components = self::getComponentsParam($request);
        $firstCreated = self::getFirstCreationDate($request);
        $secondCreated = self::getSecondCreationDate($request);
        $random = self::getRandom($request);
        $geo = self::getGeoParam($request);
        $place = self::getPlaceParam($request);
        $createdBefore = self::createdBeforeParam($request);
        $createdAfter = self::createdAfterParam($request);
        $modifiedBefore = self::modifiedBeforeParam($request);
        $modifiedAfter = self::modifiedAfterParam($request);
        $media = self::getMediaParam($request);
        $combined = array_merge_recursive(
            $params, $image, $iiif, $query, $department,
            $publications, $categories, $periods, $names,
            $acquiredFrom, $collected, $accession, $maker,
            $school, $start_date, $end_date, $techniques,
            $components, $firstCreated, $secondCreated,
            $random, $geo, $place, $createdBefore,
            $createdAfter, $modifiedBefore, $modifiedAfter,
            $media
        );
//        dd($combined);
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
            'from' => self::getFromID($request),
            'track_total_hits' => true,
            'body' => [
                "query" => [
                    "bool" => [
                        "must" => [

                        ],
                        "filter" =>
                            [
                                "term" => ["type.base" => 'object'],
                            ],
                    ]
                ],
            ],
            '_source' => [
                'admin.id',
            ],
        ];
        $query = self::createQueryObjects($request);
        $image = self::getImageParam($request);
        $iiif = self::getIiifParam($request);
        $department = self::getDepartmentParam($request);
        $publications = self::getPublicationParam($request);
        $categories = self::getCategoriesParam($request);
        $periods = self::getPeriodsParam($request);
        $names = self::getNamesParam($request);
        $acquiredFrom = self::getAcquiredFrom($request);
        $collected = self::getCollectedFrom($request);
        $accession = self::getAccessionParam($request);
        $maker = self::getMaker($request);
        $school = self::getSchool($request);
        $start_date = self::getAcquisitionDate($request);
        $end_date = self::getAcquisitionEndDate($request);
        $techniques = self::getTechniquesParam($request);
        $components = self::getComponentsParam($request);
        $firstCreated = self::getFirstCreationDate($request);
        $secondCreated = self::getSecondCreationDate($request);
        $random = self::getRandom($request);
        $geo = self::getGeoParam($request);
        $place = self::getPlaceParam($request);
        $createdBefore = self::createdBeforeParam($request);
        $createdAfter = self::createdAfterParam($request);
        $modifiedBefore = self::modifiedBeforeParam($request);
        $modifiedAfter = self::modifiedAfterParam($request);
        $combined = array_merge_recursive(
            $params, $image, $iiif, $query, $department,
            $publications, $categories, $periods, $names,
            $acquiredFrom, $collected, $accession, $maker,
            $school, $start_date, $end_date, $techniques,
            $components, $firstCreated, $secondCreated,
            $random, $geo, $place, $createdBefore,
            $createdAfter, $modifiedBefore, $modifiedAfter
        );
        return self::searchAndCache($combined);
    }

    /**
     * @param Request $request
     * @param string $object
     * @return array
     */
    public static function show(Request $request, string $object): array
    {
        $params = [
            'index' => 'ciim',
            'size' => 1,
            'body' => [
                'query' => [
                    'match' => [
                        'admin.id' => Purifier::clean($object, array('HTML.Allowed' => '')),
                    ]
                ]
            ],
            '_source' => [
                self::getSourceFields($request, self::$_allowed, self::$_mandatory),
            ],
        ];
        return Collect(self::parse(self::searchAndCache($params)))->first();
    }
}
