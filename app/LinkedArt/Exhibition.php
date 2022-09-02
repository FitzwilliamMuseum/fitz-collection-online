<?php
/**
 * This is a very dirty way of creating JSON-LD for LinkedArt.
 * I built this rapidly before leaving the museum. It should be done as a proper package.
 * This follows the specs documented here: https://linked.art/model/exhibition/
 */
namespace App\LinkedArt;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class Exhibition
{
    /**
     * Build an array of venues for parsing
     *
     * @return array
     * @var array
     */
    public static function buildVenues(array $data): array
    {
        $venues = [];
        if (array_key_exists('venues', $data)) {
            if (count($data['venues']) < 2) {
                foreach ($data['venues'] as $venue) {
                    $venues = [
                        'timespan' => [
//                            self::createExhibitionTimeSpan($venue)
                        ],
                        'took_place_at' => [
                            [
                                'id' => route('terminology', $venue['admin']['id']),
                                "type" => "Place",
                                "_label" => $venue['summary_title'],
                                "classified_as" => [
                                    [
                                        "id" => "http://vocab.getty.edu/aat/300005768",
                                        "type" => "Type",
                                        "_label" => "Museum (place)"
                                    ]
                                ]
                            ]
                        ],
                        'carried_out_by' => [
                            [
                                "type" => "Group",
                                "_label" => 'The Fitzwilliam Museum',
                                "classified_as" => [
                                    [
                                        "id" => "http://vocab.getty.edu/aat/300010769",
                                        "type" => "Type",
                                        "_label" => "Museum (organization)"
                                    ]
                                ]
                            ]
                        ]
                    ];
                    $venues = array_merge_recursive($venues, self::createOverallTimeSpan($venue));
                }
            } else {
                $parsedVenues = [];
                foreach ($data['venues'] as $venue) {
                    $parsedVenues[] = [
                        'timespan' => self::createExhibitionTimeSpan($venue),
                        'took_place_at' => [
                            [
                                'id' => route('terminology', $venue['admin']['id']),
                                "type" => "Place",
                                "_label" => $venue['summary_title'],
                                "classified_as" => [
                                    [
                                        "id" => "http://vocab.getty.edu/aat/300005768",
                                        "type" => "Type",
                                        "_label" => "Museum (place)"
                                    ]
                                ]
                            ]
                        ],
                        'carried_out_by' => [
                            [
                                "type" => "Group",
                                "_label" => 'The Fitzwilliam Museum',
                                "classified_as" => [
                                    [
                                        "id" => "http://vocab.getty.edu/aat/300010769",
                                        "type" => "Type",
                                        "_label" => "Museum (organization)"
                                    ]
                                ]
                            ]
                        ]
                    ];
                    $venues = [
                        'timespan' => [],
                        'part' => $parsedVenues
                    ];
                    $venues = array_merge_recursive($venues, self::createOverallTimeSpan($venue));
                }

            }
            return $venues;
        } else {
            return [];
        }
    }

    /**
     * Build the exhibition timespan
     * @param array $venue
     * @return array
     */
    public static function createExhibitionTimeSpan(array $venue): array
    {
        $timespan = [];
        $timespan["type"] = "TimeSpan";
        if (Arr::exists($venue, '@link')) {
            if (Arr::exists($venue['@link'], 'date')) {
                $dates = $venue['@link']['date'][0];
                if (Arr::exists($dates, 'from')) {
                    $timespan['begin_of_the_begin'] = $dates['from']['value'];
                }
                if (Arr::exists($dates, 'to')) {
                    $timespan['end_of_the_end'] = $dates['to']['value'];
                }
            }
        }
        return $timespan;
    }

    /**
     * Build the overall timespan
     * @param array $venue
     * @return array
     */
    public static function createOverallTimeSpan(array $venue): array
    {
        $venues = [];
        $endDates = [];
        $beginDates = [];
        if (Arr::exists($venue, '@link')) {
            $venues['timespan']['type'] = 'TimeSpan';
            if (Arr::exists($venue['@link'], 'date')) {
                $dates = $venue['@link']['date'][0];
                if (Arr::exists($dates, 'from')) {
                    $beginDates[] = $dates['from']['value'];
                }
                if (Arr::exists($dates, 'to')) {
                    $endDates[] = $dates['to']['value'];
                }
            }
            if (!empty($beginDates)) {
                $venues['timespan']['begin_of_the_begin'] = min($beginDates);
            }
            if (!empty($endDates)) {
                $venues['timespan']['end_of_the_end'] = max($endDates);
            }
        }
        return $venues;
    }

    /**
     * Build an array of connected objects
     * @param LengthAwarePaginator $connected
     * @return array
     */
    public static function buildConnectedObjects(LengthAwarePaginator $connected): array
    {

        $objects = [];
        foreach ($connected->items() as $object) {
            $objects[] = [
                'id' => route('record', str_replace('object-','',$object['_source']['admin']['id'])),
                'type' => 'ObjectOrArtwork',
                '_label' => $object['_source']['summary_title'],
                'classified_as' => [
                    [
                        'id' => 'http://vocab.getty.edu/aat/300264578',
                        'type' => 'Type',
                        '_label' => 'Web Page'
                    ],
                    [
                        'id' => 'http://vocab.getty.edu/aat/300404670',
                        'type' => 'Type',
                        '_label' => 'Primary'
                    ]
                ],
                'format' => [
                    [
                        'text/html'
                    ]
                ]
            ];
        }
        return $objects;
    }

    /**
     * Create the array to parse as json-ld
     * @param array $data
     * @param LengthAwarePaginator $connected
     * @return array
     */
    public static function createLinkedArtExhibition(array $data, LengthAwarePaginator $connected): array
    {
        $base = [
            '@context' => 'https://linked.art/ns/v1/linked-art.json',
            'id' => 'https://data.fitzmuseum.cam.ac.uk/id/agent/' . $data['admin']['id'],
            'type' => 'Activity',
            '_label' => $data['summary_title'],
            'format' => [
                'application/ld+json'
            ],
            'classified_as' => [
                self::buildClassification($data['venues'])
            ],
            'subject_of' => [
                [
                    'id' => 'https://data.fitzmuseum.cam.ac.uk/id/agent/' . $data['admin']['id'],
                    'type' => 'LinguisticObject',
                    '_label' => 'Homepage for exhibition details',
                    'classified_as' => [
                        [
                            [
                                'id' => 'http://vocab.getty.edu/aat/300264578',
                                'type' => 'Type',
                                '_label' => 'Web Page'
                            ],
                            [
                                'id' => 'http://vocab.getty.edu/aat/300404670',
                                'type' => 'Type',
                                '_label' => 'Primary'
                            ]
                        ]
                    ],
                    'format' => [
                        [
                            'text/html'
                        ]
                    ]
                ]
            ],
            'identified_by' => [
                [
                    'type' => 'Identifier',
                    '_label' => 'Preferred name',
                    'content' => $data['summary_title'],
                    'classified_as' =>
                        [
                            [
                                'id' => 'http://vocab.getty.edu/aat/300417193',
                                'type' => 'Name',
                                '_label' => 'Titles (General, Names)',
                            ],
                            [
                                'id' => 'http://vocab.getty.edu/aat/300404670',
                                'type' => 'Type',
                                '_label' => 'Preferred Term'
                            ]
                        ]
                ],
                [
                    'type' => 'Identifier',
                    '_label' => 'Axiell internal ID',
                    'content' => $data['admin']['id'],
                    'classified_as' =>
                        [
                            [
                                'id' => 'http://vocab.getty.edu/aat/300404626',
                                'type' => 'Type',
                                '_label' => 'Identification numbers',
                            ]
                        ]
                ]
            ],
            "used_specific_object" => self::buildConnectedObjects($connected),
        ];
        return array_merge($base, self::buildVenues($data));
    }

    /**
     * Build classification based on number of venues
     * @param array $venues
     * @return array
     */
    public static function buildClassification(array $venues): array
    {
        if (count($venues) > 1) {
            $classification = [
                [
                    'id' => 'http://vocab.getty.edu/aat/300054773',
                    'type' => 'Type',
                    '_label' => 'Exhibiting in multiple locations'
                ]
            ];
        } else {
            $classification = [
                [
                    'id' => 'http://vocab.getty.edu/aat/300054766',
                    'type' => 'Type',
                    '_label' => 'Exhibition at a single venue'
                ]
            ];
        }
        return $classification;
    }
}
