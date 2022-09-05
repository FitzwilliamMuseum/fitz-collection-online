<?php

namespace App\LinkedArt;

use Illuminate\Support\Arr;

class Production
{

    public static array $_data;

    /**
     * @param array $data
     * @return array
     */
    public static function createLinkedArt(array $data): array
    {
        self::$_data = $data;

        $art =  [
            '@context' => 'https://linked.art/ns/v1/linked-art.json',
            'id' => route('record', str_replace('object-', '', $data['admin']['id'])),
            'type' => 'HumanMadeObject',
            'produced_by' => [
                'id' => route('linked.art.production', str_replace('object-', '', $data['admin']['id'])),
                'type' => 'Production',
                '_label' => 'Production of Artwork',
                'timespan' => self::buildTimeSpan($data),
                'carried_out_by' => self::buildMakers($data)
            ]
        ];
        return array_filter($art);
    }

    /**
     * @param array $data
     * @return array
     */
    public static function buildTimeSpan(array $data): array
    {
        $lifecycle = $data['lifecycle']['creation'][0];
        if (array_key_exists('date', $lifecycle)) {
            $dates = [];
            if (array_key_exists('earliest', $lifecycle['date'][0])) {
                $dates['earliest'] = $lifecycle['date'][0]['earliest'];
            } else {
                $dates['earliest'] = null;
            }
            if (array_key_exists('precision', $lifecycle['date'][0])) {
                $precision = ucwords($lifecycle['date'][0]['precision']) . ' ';
            } else {
                $precision = '';
            }
            if (array_key_exists('latest', $lifecycle['date'][0])) {
                $dates['latest'] = $lifecycle['date'][0]['latest'];
            } else {
                $dates['latest'] = null;
            }
            $content = $precision . implode(' - ', array_unique(Arr::flatten($dates)));
            return [
                'id' => route('linked.art.timespan', str_replace('object-', '', $data['admin']['id'])),
                'type' => "TimeSpan",
                'identified_by' => [
                    'id' => route('linked.art.timespan.details', [str_replace('object-', '', $data['admin']['id']), 'name']),
                    "type" => "Name",
                    "_label" => "Date",
                    "content" => $content
                ],
                "begin_of_the_begin" => $dates['earliest'],
                "end_of_the_end" => $dates['latest']
            ];
        } else {
            return [];
        }
    }

    /**
     * @param array $data
     * @return array
     */
    public static function buildMakers(array $data): array
    {
        $lifecycle = $data['lifecycle']['creation'][0];

        $makers = [];
        if (array_key_exists('maker', $lifecycle)) {
            foreach ($lifecycle['maker'] as $maker) {
                if (Arr::has($maker, '@link') && Arr::has($maker['@link'], 'role')) {
                    $role = ucwords($maker['@link']['role'][0]['value']);
                } else {
                    $role = 'Maker';
                }
                $makers[] = [
                    'id' => route('terminology', ['id' => $maker['admin']['id']]),
                    'type' => 'Person',
                    '_label' => $maker['summary_title'],
                    'referred_to_by' => [
                        [
                            'id' => route('linked.art.maker', [str_replace('object-', '', $data['admin']['id']), $maker['admin']['id']]),
                            'type' => 'Type',
                            '_label' => $role,
                            'classified_as' => [
                                [
                                    "id" => "https://data.getty.edu/local/thesaurus/producer-role-statement",
                                    "type" => "Type",
                                    "_label" => "Artist/Maker (Producer) Role Statement"
                                ],
                                [
                                    "id" => "http://vocab.getty.edu/aat/300418049",
                                    "type" => "Type",
                                    "_label" => "Brief Text"
                                ]
                            ],
                            "content" => "Artists (Visual Artists)",
                            'close_match' => [
                                "id" => "http://vocab.getty.edu/aat/300025103",
                                "type" => "Type",
                                "_label" => "Artists (Visual Artists)"
                            ]
                        ]
                    ],
                ];
            }
            $matches = CloseMatch::buildCloseMatch($maker['admin']['id']);
            if (!empty($matches)) {
                $makers[0]['close_match'] = $matches;
            }
        }
        return array_filter($makers);
    }

    /**
     * @param array $data
     * @param array $maker
     * @param int $arrayKey
     * @return array
     */
    public static function buildMakersStatement(array $data, array $maker, int $arrayKey): array
    {
        $creator = $data['lifecycle']['creation'][0]['maker'][$arrayKey];
        if (Arr::has($creator, '@link') && Arr::has($creator['@link'], 'role')) {
            $role = ucwords($creator['@link']['role'][0]['value']);
        } else {
            $role = 'Maker';
        }
        $makers[] = [
            'id' => route('linked.art.maker', [str_replace('object-', '', $data['admin']['id']), $maker['admin']['uuid']]),
            'type' => 'Type',
            "_label" => $role,
            'classified_as' => [
                [
                    "id" => "https://data.getty.edu/local/thesaurus/producer-role-statement",
                    "type" => "Type",
                    "_label" => "Artist/Maker (Producer) Role Statement"
                ],
                [
                    "id" => "http://vocab.getty.edu/aat/300418049",
                    "type" => "Type",
                    "_label" => "Brief Text"
                ]
            ],
            "content" => "Artists (Visual Artists)",
            'close_match' => [
                "id" => "http://vocab.getty.edu/aat/300025103",
                "type" => "Type",
                "_label" => "Artists (Visual Artists)"
            ]
        ];

        $matches = CloseMatch::buildCloseMatch($maker['admin']['id']);
        if (!empty($matches)) {
            $makers[0]['close_match'] = $matches;
        }

        return array_filter($makers);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function createLinkedArtTimeSpanDetails(array $data)
    {
        return self::buildTimeSpan($data)['identified_by'];
    }

    /**
     * @param array $data
     * @return array
     */
    public static function createLinkedArtTimeSpan(array $data): array
    {
        return self::buildTimeSpan($data);
    }

    /**
     * @param array $data
     * @param array $maker
     * @param int $arrayKey
     * @return array
     */
    public static function createLinkedArtMaker(array $data, array $maker, int $arrayKey): array
    {
        return [
            '@context' => 'https://linked.art/ns/v1/linked-art.json',
            'id' => route('record', [str_replace('object-', '', $data['admin']['id'])]),
            'type' => 'ManMadeObject',
            'referred_to_by' =>  self::buildMakersStatement($data, $maker, $arrayKey)
            ];
    }
}
