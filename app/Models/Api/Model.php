<?php

namespace App\Models\Api;


use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Mews\Purifier\Facades\Purifier;
use stdClass;

class Model
{
    /**
     * @param array $params
     * @return array
     */
    public static function searchAndCache(array $params) : array
    {
        $key = self::getKey($params);
        $expiresAt = now()->addMinutes(60);
        if (Cache::has($key)) {
            $data = Cache::get($key);
        } else {
            $data = self::getClient()->search($params);
            Cache::put($key, $data, $expiresAt);
        }
        return $data;
    }

    /**
     * @param array $params
     * @return string
     */
    public static function getKey(array $params): string
    {
        return md5(json_encode($params));
    }

    /**
     * @return Client
     */
    public static function getClient(): Client
    {
        return ClientBuilder::create()->setHosts(self::getHosts())->build();
    }

    /**
     * @return array[]
     */
    public static function getHosts(): array
    {
        return [
            [
                'host' => env('ELASTIC_API'),
                'port' => '80',
                'path' => env('ELASTIC_PATH'),
            ]
        ];
    }

    /**
     * @param Request $request
     * @return int
     */
    public static function getSize(Request $request): int
    {
        $size = 20;
        $params = $request->query();
        if (is_array($params)) {
            if (array_key_exists('size', $params) && $params['size'] > 0) {
                $size = $params['size'];
            }
        }
        return $size;
    }

    /**
     * @param Request $request
     * @return int
     */
    public static function getSizeID(Request $request): int
    {
        $size = 500;
        $params = $request->query();
        if (is_array($params)) {
            if (array_key_exists('size', $params) && $params['size'] > 0) {
                $size = $params['size'];
            }
        }
        return $size;
    }

    /**
     * @param Request $request
     * @return array|string
     */
    public static function getSort(Request $request): array|string
    {
        $sort = '';
        $params = $request->query();
        if (array_key_exists('sort_field', $params)) {
            $sortField = match ($params['sort_field']) {
                'name' => 'name.value.keyword',
                'created' => 'admin.created',
                'updated' => 'admin.modified',
                'id' => 'admin.id',
                default => 'summary_title.keyword',
            };
        } else {
            $sortField = 'summary_title.keyword';
        }
        if (is_array($params)) {
            if (array_key_exists('sort', $params) && in_array($params['sort'], ['asc', 'desc'])) {
                $sort = array(
                    $sortField => [
                        "order" => $params['sort']
                    ]
                );
            } else  {
                $sort = array(
                    $sortField => [
                        "order" => 'asc'
                    ]
                );
            }
        }
        return $sort;
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function getRandom(Request $request): array
    {
        $params = [];
        if (!is_null($request->query('random'))) {
            $random = new stdClass();
            $random->seed = time();
            return array(
                'body' => array(
                    'query' => array(
                        'function_score' => array(
                            'random_score' => $random,
                        ),
                    ),
                ),
            );
        } else {
            return $params;
        }
    }

    /**
     * @param Request $request
     * @param array $params
     * @return array
     */
    public static function createQuery(Request $request): array
    {
        $query = [];
        if (!is_null($request->query('query'))) {
            $query['body']['query']['bool']['must'][] = [
                "multi_match" => [
                    "fields" => "_generic_all_std",
                    "query" => Purifier::clean($request->query('query'), array('HTML.Allowed' => '')),
                    "operator" => "AND",
                ]
            ];
        }
        return $query;

    }

    /**
     * @param Request $request
     * @param array $params
     * @return array
     */
    public static function createQueryPlaces(Request $request): array
    {
        $query = [];
        if (!is_null($request->query('query'))) {
            $query['body']['query']['bool']['must'][] = [
                'match' => [
                    'lifecycle.creation.places.summary_title' => Purifier::clean($request->query('query'), array('HTML.Allowed' => '')),
                ]
            ];
        }
        return $query;

    }


    /**
     * @param Request $request
     * @param array $params
     * @return array
     */
    public static function createQueryMakers(Request $request): array
    {
        $query = [];
        if (!is_null($request->query('query'))) {
            $query['body']['query']['bool']['must'][] = [
                'match' => [
                    'lifecycle.creation.maker.summary_title' => Purifier::clean($request->query('query'), array('HTML.Allowed' => '')),
                ]
            ];
        }
        return $query;

    }

    /**
     * @param Request $request
     * @return array
     */
    public static function createQueryObjects(Request $request): array
    {
        $params = [];
        if (!is_null($request->query('query'))) {
            $params['body']['query']['bool']['must'][] = [
                "multi_match" => [
                    "fields" => "_generic_all_std",
                    "query" => Purifier::clean($request->query('query'), array('HTML.Allowed' => '')),
                    "operator" => "AND",
                ]
            ];
        } else {
            $params['body']['query']['bool']['must'][] = [
                "multi_match" => [
                    "fields" => "_generic_all_std",
                    "query" => 'Fitzwilliam',
                    "operator" => "AND",
                ]
            ];
        }
        return $params;

    }

    public static function createRandomQueryObjects(Request $request): array
    {
        $params = [];
        if (!is_null($request->query('query'))) {
            $params['body']['query']['function_score']['query']['bool']['must'][] = [
                "multi_match" => [
                    "fields" => "_generic_all_std",
                    "query" => Purifier::clean($request->query('query'), array('HTML.Allowed' => '')),
                    "operator" => "AND",
                ]
            ];
        } else {
            $params['body']['query']['function_score']['query']['bool']['must'][] = [
                "multi_match" => [
                    "fields" => "_generic_all_std",
                    "query" => 'Fitzwilliam',
                    "operator" => "AND",
                ]
            ];
        }
        return $params;

    }
    /**
     * @param Request $request
     * @return int
     */
    public static function getFrom(Request $request): int
    {
        if ($request->query('page') && $request->query('page') > 1) {
            return $request->query('page') * self::getSize($request);
        } else {
            return 0;
        }
    }

    /**
     * @param Request $request
     * @return int
     */
    public static function getFromID(Request $request): int
    {
        if ($request->query('page') && $request->query('page') > 1) {
            return $request->query('page') * self::getSizeID($request);
        } else {
            return 0;
        }
    }
    public static function parse($elastic): array
    {
        $data = array();
        foreach ($elastic['hits']['hits'] as $object) {
            $data[] = $object['_source'];
        }
        return $data;
    }

    /**
     * @param Request $request
     * @param array $allowed
     * @param array $mandatory
     * @return string
     */
    public static function getSourceFields(Request $request, array $allowed, array $mandatory): string
    {
        if (!empty($request['fields'])) {
            $fields = array_unique(array_filter(array_merge(explode(',', $request['fields']), $mandatory)));
            return implode(',', $fields);
        } else {
            return implode(',', $allowed);
        }
    }

    /**
     * @param Request $request
     * @return string
     */
    public static function getSortParam(Request $request): string
    {
        if (array_key_exists('sort', $request->query())) {
            return $request->query('sort');
        } else {
            return 'desc';
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function getIiifParam(Request $request): array
    {
        $params = [];
        if (array_key_exists('hasIIIF', $request->query())) {
            $filter = array(
                "exists" => [
                    "field" => "multimedia.processed.zoom"
                ]
            );
            $params['body']['query']['bool']['must'][] = [$filter];
        }
        return $params;

    }

    /**
     * @param Request $request
     * @return array
     */
    public static function getImageParam(Request $request): array
    {
        $params = [];
        if (array_key_exists('hasImage', $request->query())) {
            $filter = array(
                "exists" => [
                    "field" => "multimedia"
                ]
            );
            $params['body']['query']['bool']['must'][] = [$filter];
        }
        return $params;
    }

    public static function getGeoParam(Request $request): array
    {
        $params = [];
        if (array_key_exists('hasGeo', $request->query())) {
            $filter = array(
                "exists" => [
                    "field" => "lifecycle.collection.places"
                ]
            );
            $params['body']['query']['bool']['must'][] = [$filter];
        }
        return $params;
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function getDepartmentParam(Request $request): array
    {
        $params = [];
        if (array_key_exists('department', $request->query())) {
            $filter = array(
                'match' => [
                    'department.value' => urldecode(Purifier::clean($request->query('department'), array('HTML.Allowed' => ''))),
                ]
            );
            $params['body']['query']['bool']['must'] = [$filter];
        }
        return $params;
    }


    public static function getPlaceParam(Request $request): array
    {
        $params = [];
        if (array_key_exists('place', $request->query())) {
            $filter = array(
                'match' => [
                    'lifecycle.collection.places.admin.id' => urldecode(Purifier::clean($request->query('place'), array('HTML.Allowed' => ''))),
                ]
            );
            $params['body']['query']['bool']['must'] = [$filter];
        }
        return $params;
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function getPublicationParam(Request $request): array
    {
        $params = [];
        if (array_key_exists('publication', $request->query())) {
            $filter = array(
                'match' => [
                    'publications.admin.id' => Purifier::clean($request->query('publication'), array('HTML.Allowed' => '')),
                ]
            );
            $params['body']['query']['bool']['must'] = [$filter];
        }
        return $params;
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function getCategoriesParam(Request $request): array
    {
        $params = [];
        if (array_key_exists('category', $request->query())) {
            $filter = array(
                'match' => [
                    'categories.admin.id' => Purifier::clean($request->query('category'), array('HTML.Allowed' => '')),
                ]
            );
            $params['body']['query']['bool']['must'] = [$filter];
        }
        return $params;
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function getPeriodsParam(Request $request): array
    {
        $params = [];
        if (array_key_exists('period', $request->query())) {
            $filter = array(
                'match' => [
                    'lifecycle.creation.periods.admin.id' => Purifier::clean($request->query('period'), array('HTML.Allowed' => '')),
                ]
            );
            $params['body']['query']['bool']['must'] = [$filter];
        }
        return $params;
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function getNamesParam(Request $request): array
    {
        $params = [];
        if (array_key_exists('name', $request->query())) {
            $filter = array(
                'match' => [
                    'name.reference.admin.id' => Purifier::clean($request->query('name'), array('HTML.Allowed' => '')),
                ]
            );
            $params['body']['query']['bool']['must'] = [$filter];
        }
        return $params;
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function getAcquiredFrom(Request $request): array
    {
        $params = [];
        if (array_key_exists('acquired_from', $request->query())) {
            $filter = array(
                'match' => [
                    'lifecycle.acquisition.agents.admin.id' => Purifier::clean($request->query('acquired_from'), array('HTML.Allowed' => '')),
                ]
            );
            $params['body']['query']['bool']['must'] = [$filter];
        }
        return $params;
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function getCollectedFrom(Request $request): array
    {
        $params = [];
        if (array_key_exists('collected_place', $request->query())) {
            $filter = array(
                'match' => [
                    'lifecycle.collection.places.admin.id' => Purifier::clean($request->query('collected_place'), array('HTML.Allowed' => '')),
                ]
            );
            $params['body']['query']['bool']['must'] = [$filter];
        }
        return $params;
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function getMaker(Request $request): array
    {
        $params = [];
        if (array_key_exists('maker', $request->query())) {
            $filter = array(
                'match' => [
                    'lifecycle.creation.maker.admin.id' => Purifier::clean($request->query('maker'), array('HTML.Allowed' => '')),
                ]
            );
            $params['body']['query']['bool']['must'] = [$filter];
        }
        return $params;
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function getTechniquesParam(Request $request): array
    {
        $params = [];
        if (array_key_exists('technique', $request->query())) {
            $filter = array(
                'match' => [
                    'techniques.reference.admin.id' => Purifier::clean($request->query('technique'), array('HTML.Allowed' => '')),
                ]
            );
            $params['body']['query']['bool']['must'] = [$filter];
        }
        return $params;
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function getComponentsParam(Request $request): array
    {
        $params = [];
        if (array_key_exists('component', $request->query())) {
            $filter = array(
                'match' => [
                    'component.materials.reference.admin.id' => Purifier::clean($request->query('component'), array('HTML.Allowed' => '')),
                ]
            );
            $params['body']['query']['bool']['must'] = [$filter];
        }
        return $params;
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function getSchool(Request $request): array
    {
        $params = [];
        if (array_key_exists('school_or_style', $request->query())) {
            $filter = array(
                'match' => [
                    'school_or_style.admin.id' => Purifier::clean($request->query('school_or_style'), array('HTML.Allowed' => '')),
                ]
            );
            $params['body']['query']['bool']['must'] = [$filter];
        }
        return $params;
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function getAcquisitionDate(Request $request): array
    {
        $params = [];
        if (array_key_exists('acquired_date_start', $request->query())) {
            $filter = array(
                'range' => [
                    'lifecycle.acquisition.date.earliest' =>
                        [
                            'gte' => Purifier::clean($request->query('acquired_date_start'), array('HTML.Allowed' => ''))
                        ],
                ]
            );
            $params['body']['query']['bool']['must'] = [$filter];
        }
        return $params;
    }


    /**
     * @param Request $request
     * @return array
     */
    public static function getFirstCreationDate(Request $request): array
    {
        $params = [];
        if (array_key_exists('created_start', $request->query())) {
            $filter = array(
                'range' => [
                    'lifecycle.creation.date.from.earliest' =>
                        [
                            'gte' => Purifier::clean($request->query('created_start'), array('HTML.Allowed' => ''))
                        ],
                ]
            );
            $params['body']['query']['bool']['must'] = [$filter];
        }
        return $params;
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function getSecondCreationDate(Request $request): array
    {
        $params = [];
        if (array_key_exists('created_end', $request->query())) {
            $filter = array(
                'range' => [
                    'lifecycle.creation.date.to.earliest' =>
                        [
                            'lte' => Purifier::clean($request->query('created_end'), array('HTML.Allowed' => ''))
                        ],
                ]
            );
            $params['body']['query']['bool']['must'] = [$filter];
        }
        return $params;
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function getAcquisitionEndDate(Request $request): array
    {
        $params = [];
        if (array_key_exists('acquired_date_end', $request->query())) {
            $filter = array(
                'range' => [
                    'lifecycle.acquisition.date.latest' =>
                        [
                            'lte' => Purifier::clean($request->query('acquired_date_end'), array('HTML.Allowed' => ''))
                        ]
                ]
            );
            $params['body']['query']['bool']['must'] = [$filter];
        }
        return $params;
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function getAccessionParam(Request $request): array
    {
        $params = [];
        if (array_key_exists('accession_number', $request->query())) {
            $filter = array(
                'term' => [
                    'identifier.accession_number.keyword' => Purifier::clean($request->query('accession_number'), array('HTML.Allowed' => '')),
                ]
            );
            $filterTrue = array(
                'term' => [
                    'identifier.type' => 'accession number',
                ]
            );


            $params['body']['query']['bool']['must'][] = [$filterTrue];
            $params['body']['query']['bool']['must'][] = [$filter];

        }
        return $params;
    }


}
