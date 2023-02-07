<?php

namespace App\LinkedArt;

use App\Models\AxiellLocation;
use App\LinkedArt\CitationSequence;
use Illuminate\Support\Arr;

class ObjectOrArtwork
{

    /**
     * @var array
     */
    public static array $data;

    /**
     * @param $data
     * @return mixed
     */
    public static function getLabel($data): mixed
    {
        return $data['title'][0]['value'] ?? $data['summary_title'];
    }

    /**
     * @param $data
     * @return string
     */
    public static function getAccessionNumber($data): string
    {
        return $data['identifier'][0]['accession_number'];
    }

    /**
     * @return string
     */
    public static function createUri(): string
    {
        return str_replace('http://', 'https://', self::$data['admin']['uri']);
    }

    /**
     * @param array $data
     * @return array
     */
    public static function buildCategories(array $data): array
    {
        $categories = [];
        if (array_key_exists('categories', $data)) {
            foreach ($data['categories'] as $category) {
                $categories[] = [
                    'id' => route('terminology', $category['admin']['id']),
                    'type' => 'Type',
                    '_label' => $category['summary_title'],
                    'classified_as' => [
                        'id' => 'http://vocab.getty.edu/aat/300435444',
                        'type' => 'Type',
                        '_label' => 'Classification (Category)',
                    ]
                ];
            }
        }
        if(Arr::has($data, 'school_or_style'))
        {
            foreach ($data['school_or_style'] as $school) {
                $categories[] = [
                    'id' => route('terminology', $school['admin']['id']),
                    'type' => 'Type',
                    '_label' => $school['summary_title'],
                    'classified_as' => [
                        'id' => 'http://vocab.getty.edu/aat/300055768',
                        'type' => 'Type',
                        '_label' => 'Culture',
                    ]
                ];
            }
        }
        $categories[] = [
            'id' => 'http://vocab.getty.edu/aat/300133025',
            'type' => 'Type',
            '_label' => 'Work of Art'
        ];
        $categories[] = [
            'id' => 'https://data.getty.edu/local/thesaurus/object-record-structure-whole',
            'type' => 'Type',
            '_label' => 'Object Record Structure: Whole',
            'classified_as' => [
                'id' => 'https://data.getty.edu/local/thesaurus/object-record-structure-type',
                'type' => 'Type',
                '_label' => 'Object Record Structure Type',
            ]
        ];
        return $categories;
    }

    /**
     * @param array $data
     * @return array
     */
    public static function buildIdentifiers(array $data): array
    {
        $identifiers = [];
        // Add the primary title
        $identifiers[] = [
            'id' => route('linked.art.identifiers', [str_replace('object-', '', $data['admin']['id']), 'title']),
            'type' => 'Title',
            'classified_as' => [
                [
                    'id' => 'http://vocab.getty.edu/aat/300417193',
                    'type' => 'Name',
                    '_label' => 'Titles (General, Names)'
                ],
                [
                    'id' => 'http://vocab.getty.edu/aat/300404670',
                    'type' => 'Type',
                    '_label' => 'Preferred Term'
                ]
            ],
            'content' => $data['title'][0]['value'] ?? $data['summary_title'] ?? 'Untitled work',
            'language' => [
                [
                    'id' => 'https://vocab.getty.edu/aat/300388277',
                    'type' => 'Language',
                    '_label' => 'English'
                ]
            ]
        ];
        // Add the accession number
        $identifiers[] = [
            'id' => route('linked.art.identifiers', [str_replace('object-', '', $data['admin']['id']), 'accession_number']),
            'type' => 'Identifier',
            'classified_as' => [
                [
                    'id' => 'http://vocab.getty.edu/aat/300404626',
                    'type' => 'Type',
                    '_label' => 'Identification Number'
                ],
                [
                    'id' => 'http://vocab.getty.edu/aat/300312355',
                    'type' => 'Type',
                    '_label' => 'Accession Number'
                ],
                [
                    'id' => 'http://vocab.getty.edu/aat/300404670',
                    'type' => 'Type',
                    '_label' => 'Preferred Term'
                ]
            ],
            'content' => $data['identifier'][0]['accession_number'],
        ];
        $identifiers[] = [
            'id' => route('linked.art.identifiers', [str_replace('object-', '', $data['admin']['id']), 'priref']),

            'type' => 'Identifier',
            'classified_as' => [
                ['id' => 'https://vocab.getty.edu/aat/300404626',
                    'type' => 'Type',
                    '_label' => 'Primary Reference'
                ],
                [
                    'id' => 'http://vocab.getty.edu/aat/300404626',
                    'type' => 'Type',
                    '_label' => 'Identification Number'
                ],
            ],
            'content' => $data['identifier'][1]['priref'],
        ];
        // Add the system number
        $identifiers[] = [
            'id' => route('linked.art.identifiers', [str_replace('object-', '', $data['admin']['id']), 'axiell_system_number']),
            'type' => 'Identifier',
            'classified_as' => [
                [
                    'id' => "http://vocab.getty.edu/page/aat/300417447",
                    'type' => 'Type',
                    '_label' => 'Axiell System Identifier'
                ]
            ],
            'content' => $data['admin']['id'],
        ];
        return $identifiers;
    }

    /**
     * A function to build the array of referenced items
     * @param array $data
     * @return array
     */
    public static function buildReferred(array $data): array
    {
        $referred = [];
        // Credit line
        $referred[] = [
            'id' => route('linked.art.credit_line', str_replace('object-', '', $data['admin']['id'])),
            "type" => "LinguisticObject",
            "_label" => "Source Credit Line",
            "classified_as" => [
                [
                    "id" => "http://vocab.getty.edu/aat/300435418",
                    "type" => "Type",
                    "_label" => "Credit Line"
                ],
                [
                    "id" => "http://vocab.getty.edu/aat/300404764",
                    "type" => "Type",
                    "_label" => "Sources (General Concept)"
                ],
                [
                    "id" => "http://vocab.getty.edu/aat/300418049",
                    "type" => "Type",
                    "_label" => "Brief Text"
                ]
            ],
            "content" => "Owned by The Chancellor, Masters, and Scholars of the University of Cambridge"
        ];
        if (Arr::has($data, 'legal')) {
            if (Arr::has($data['legal'], 'credit_line')) {
                $referred[] = [
                    'id' => route('linked.art.legal_credit_line', str_replace('object-', '', $data['admin']['id'])),
                    "type" => "LinguisticObject",
                    "_label" => "Source Credit Line",
                    "classified_as" => [
                        [
                            "id" => "http://vocab.getty.edu/aat/300435418",
                            "type" => "Type",
                            "_label" => "Credit Line"
                        ],
                        [
                            "id" => "http://vocab.getty.edu/aat/300404764",
                            "type" => "Type",
                            "_label" => "Sources (General Concept)"
                        ],
                        [
                            "id" => "http://vocab.getty.edu/aat/300418049",
                            "type" => "Type",
                            "_label" => "Brief Text"
                        ]
                    ],
                    "content" => Arr::get($data, 'legal.credit_line')
                ];
            }
        }
        if (Arr::has($data, 'publications')) {
            $count = 0;
            foreach ($data['publications'] as $publication) {
                $count = $count + 1;
                $pub = [
                    'id' => route('linked.art.citation', [str_replace('object-','',$data['admin']['id']),$publication['admin']['uuid']]),
                    'type' => 'LinguisticObject',
                    '_label' => 'Citation (Bibliographic Reference)',
                    'classified_as' => [
                        [
                            'id' => "http://vocab.getty.edu/aat/300311705",
                            "type" => "Type",
                            "_label" => "Citations (Bibliographic References)"
                        ],
                        [
                            "id" => "http://vocab.getty.edu/aat/300418049",
                            "type" => "Type",
                            "_label" => "Brief Text"
                        ]
                    ],
                    'content' => $publication['summary_title'],
                    'about' => [
                        [
                            'id' => route('publication.record', $publication['admin']['id']),
                            'type' => 'LinguisticObject',
                            '_label' => $publication['summary_title'],
                        ]
                    ],
                    "format" => "text/html",
                ];
                $pub['dimension'] = [CitationSequence::createLinkedArt($data, $count)];
                $referred[] = $pub;
            }
        }
        // Object description
        if (array_key_exists('description', $data)) {
            $referred[] = [
                'id' => route('linked.art.description', str_replace('object-', '', $data['admin']['id'])),
                'type' => 'LinguisticObject',
                '_label' => 'Object Description',
                'classified_as' => [
                    [
                        'id' => 'http://vocab.getty.edu/aat/300080091',
                        'type' => 'Type',
                        '_label' => 'Description'
                    ],
                    [
                        'id' => 'http://vocab.getty.edu/aat/300418049',
                        'type' => 'Type',
                        '_label' => 'Brief Text'
                    ],
                ],
                'content' => implode(';', Arr::flatten($data['description'])),
                'subject_to' => [
                    [
                        'id' => route('linked.art.license', str_replace('object-', '', $data['admin']['id'])),
                        'type' => 'Right',
                        '_label' => 'License for Object Description',
                        'classified_as' => [
                            [
                                "id" => "https://creativecommons.org/publicdomain/zero/1.0/",
                                "type" => "Type",
                                "_label" => "Public Domain Dedication CC ZERO"
                            ]
                        ],
                        'possessed_by' => [
                            [
                                'id' => 'http://vocab.getty.edu/ulan/' . env('FITZ_ULAN'),
                                'type' => 'Group',
                                "_label" => "The Fitzwilliam Museum",
                                'member_of' => [
                                    [
                                        'id' => 'http://vocab.getty.edu/ulan/500247221',
                                        'type' => 'Group',
                                        '_label' => 'The University of Cambridge',
                                        'classified_as' => [
                                            [
                                                "id" => "http://vocab.getty.edu/ulan/500000003",
                                                "type" => "Type",
                                                "_label" => "Corporate Bodies"
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'subject_of' => [
                            [
                                'id' => route('linked.art.acknowledgements', str_replace('object-', '', $data['admin']['id'])),
                                'type' => 'LinguisticObject',
                                '_label' => 'Acknowledgements for Object Description',
                                'classified_as' => [
                                    [
                                        "id" => "http://vocab.getty.edu/aat/300026687",
                                        "type" => "Type",
                                        "_label" => "Acknowledgements"
                                    ]
                                ],
                                'content' => 'Text provided by the Fitzwilliam Museum. Licensed under CC Zero (https://creativecommons.org/publicdomain/zero/1.0/).'
                            ]
                        ],
                    ]
                ],


                "format" => "text/markdown"
            ];
        }
        if (array_key_exists('measurements', $data)) {
            $referred[] = [
                'id' => route('linked.art.dimensions.statement', str_replace('object-', '', $data['admin']['id'])),
                'type' => 'LinguisticObject',
                "_label" => "Dimensions Statement",
                "classified_as" => [
                    [
                        "id" => "http://vocab.getty.edu/aat/300435430",
                        "type" => "Type",
                        "_label" => "Dimensions Description"
                    ],
                    [
                        "id" => "http://vocab.getty.edu/aat/300418049",
                        "type" => "Type",
                        "_label" => "Brief Text"
                    ]
                ],
                'content' => implode(' ', array_reverse(Arr::flatten($data['measurements'])))
            ];
        }
        if (array_key_exists('materials', $data)) {
            $materials = [];
            foreach ($data['materials'] as $material) {
                if (array_key_exists('note', $material)) {
                    $notes = ' (' . implode(' ', Arr::flatten($material['note'])) . ')';
                } else {
                    $notes = '';
                }
                $materials[] = $material['reference']['summary_title'] . $notes;
            }
            $referred[] = [
                'id' => route('linked.art.materials', str_replace('object-', '', $data['admin']['id'])),
                "type" => "LinguisticObject",
                "_label" => "Materials Description",
                "classified_as" => [
                    [
                        "id" => "http://vocab.getty.edu/aat/300435429",
                        "type" => "Type",
                        "_label" => "Materials Description",
                    ],
                    [
                        "id" => "http://vocab.getty.edu/aat/300418049",
                        "type" => "Type",
                        "_label" => "Brief Text"
                    ]
                ],
                'content' => implode(' ', $materials)
            ];
        }
        // Object type
        $referred[] = [
            'id' => route('linked.art.objectType', str_replace('object-', '', $data['admin']['id'])),
            'type' => 'LinguisticObject',
            '_label' => 'Object Type',
            'classified_as' => [
                [
                    "id" => "http://vocab.getty.edu/aat/300435443",
                    "type" => "Type",
                    "_label" => "Object/Work Type (Category)"
                ],
                [
                    "id" => "http://vocab.getty.edu/aat/300418049",
                    "type" => "Type",
                    "_label" => "Brief Text"
                ],
            ],
            'content' => $data['summary_title']
        ];
        // Rights statement
        if (array_key_exists('lifecycle', $data)) {
            if (array_key_exists('creation', $data['lifecycle'])) {
                if (array_key_exists('date', $data['lifecycle']['creation'][0])) {
                    if (is_array($data['lifecycle']['creation'][0]['date']) && !empty($data['lifecycle']['creation'][0]['date'])) {
                        if (array_key_exists('earliest', $data['lifecycle']['creation'][0]['date'][0])) {
                            if ($data['lifecycle']['creation'][0]['date'][0]['earliest'] <= '1900') {
                                $referred[] = [
                                    "id" => "https://rightsstatements.org/vocab/NKC/1.0/",
                                    "type" => "LinguisticObject",
                                    "_label" => "RightsStatements.org Rights Assertion",
                                    "classified_as" => [
                                        [
                                            "id" => "https://data.getty.edu/museum/ontology/linked-data/object/rights-statement",
                                            "type" => "Type",
                                            "_label" => "Rights Statement"
                                        ]
                                    ],
                                    "content" => "No known copyright (based on the object's creation date - United Kingdom)"
                                ];
                            }
                        }
                    }
                }
            }
        }
        return $referred;
    }

    /**
     * @param array $data
     * @return array
     */
    public static function buildProduction(array $data): array
    {
        $production = (array)null;
        if (array_key_exists('lifecycle', $data)) {
            if (array_key_exists('creation', $data['lifecycle'])) {
                $production = [
                    'id' => route('linked.art.production', str_replace('object-', '', $data['admin']['id'])),
                    'type' => 'Production',
                    '_label' => 'Production of Artwork',
                    'timespan' => self::buildTimeSpan($data['lifecycle']['creation'][0]),
                    'carried_out_by' => self::buildMakers($data['lifecycle']['creation'][0])
                ];
            }
        }
        return $production;
    }

    public static function buildTimeSpan(array $data): array
    {
        if (array_key_exists('date', $data)) {
            $dates = [];
            if (array_key_exists('earliest', $data['date'][0])) {
                $dates['earliest'] = $data['date'][0]['earliest'];
            } else {
                $dates['earliest'] = null;
            }
            if (array_key_exists('precision', $data['date'][0])) {
                $precision = ucwords($data['date'][0]['precision']) . ' ';
            } else {
                $precision = '';
            }
            if (array_key_exists('latest', $data['date'][0])) {
                $dates['latest'] = $data['date'][0]['latest'];
            } else {
                $dates['latest'] = null;
            }
            $content = $precision . implode(' - ', array_unique(Arr::flatten($dates)));
            return [
                'id' => route('linked.art.timespan', [str_replace('object-', '', self::$data['admin']['id'])]),
                'type' => "TimeSpan",
                'identified_by' => [
                    'id' => route('linked.art.timespan.details', [str_replace('object-', '', self::$data['admin']['id']),'name']),
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
        $makers = [];
        if (array_key_exists('maker', $data)) {
            foreach ($data['maker'] as $maker) {
                if (Arr::has($maker, '@link') && Arr::has($maker['@link'], 'role')) {
                    $role = ucwords($maker['@link']['role'][0]['value']);
                } else {
                    $role = 'Maker';
                }
                $makers[] = [
                    'id' => route('agent', ['id' => $maker['admin']['id']]),
                    'type' => 'Person',
                    '_label' => $maker['summary_title'],
                    'referred_to_by' => [
                        [
                            'id' => route('linked.art.maker', [str_replace('object-', '', self::$data['admin']['id']),$maker['admin']['uuid']]),
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
     * @param array $array
     * @return array
     */
    public static function cleanUp(array $array): array
    {
//        $clean = array_map('array_filter', $array);
        return array_filter($array);
    }

    /**
     * @param array $data
     * @return array
     */
    public static function buildRepresentation(array $data): array
    {
        $representation = [];
        if (Arr::has($data, 'multimedia')) {
            foreach ($data['multimedia'] as $image) {
                if (Arr::has($image['processed'], 'large')) {
                    $representation[] = [
                        "id" => env('FITZ_IMAGE_URL') . $image['processed']['large']['location'],
                        "type" => "VisualItem",
                        "_label" => "A representation of the artwork",
                        "classified_as" => [
                            "id" => "http://vocab.getty.edu/aat/300215302",
                            "type" => "Type",
                            "_label" => "Digital Image"

                        ],
                        "format" => "image/jpeg",
                        'dimension' => [
                            [
                                "id" => env('FITZ_IMAGE_URL') . $image['processed']['large']['location'],
                                "type" => "Dimension",
                                "classified_as" => [
                                    [
                                        "id" => "http://vocab.getty.edu/aat/300055644",
                                        "type" => "Type",
                                        "_label" => "Height"
                                    ]
                                ],
                                "value" => $image['processed']['large']['measurements']['dimensions'][0]['value'],
                                "unit" => [
                                    "id" => "http://vocab.getty.edu/aat/300266190",
                                    "type" => "MeasurementUnit",
                                    "_label" => "pixels"
                                ]
                            ],
                            [
                                "id" => env('FITZ_IMAGE_URL') . $image['processed']['large']['location'],

                                "type" => "Dimension",
                                "classified_as" => [
                                    [
                                        "id" => "http://vocab.getty.edu/aat/300055647",
                                        "type" => "Type",
                                        "_label" => "Width"
                                    ]
                                ],
                                "value" => $image['processed']['large']['measurements']['dimensions'][1]['value'],
                                "unit" => [
                                    "id" => "http://vocab.getty.edu/aat/300266190",
                                    "type" => "MeasurementUnit",
                                    "_label" => "pixels"
                                ]
                            ]

                        ],
                        'access_point' => [
                            [
                                "id" => env('FITZ_IMAGE_URL') . $image['processed']['original']['location'],
                                "type" => "DigitalObject",
                            ]
                        ]
                    ];
                }
            }
        }
        return $representation;
    }

    /**
     * Function to build the array of dimensions for an object
     * It will ignore non-standard terms
     * @param array $data
     * @return array
     */
    public static function buildDimensions(array $data): array
    {
        $dimensions = [];
        if (array_key_exists('measurements', $data)) {
            if (array_key_exists('dimensions', $data['measurements'])) {
                foreach ($data['measurements']['dimensions'] as $dimension) {
                    $dim = self::getDimensionURI(strtolower($dimension['dimension']));
                    $unit = self::getUnitsURI(strtolower($dimension['units']));
                    if ($dim != '' && $unit != '') {
                        $dimensions[] = [
                            'id' => route('record', (str_replace('object-', '', $data['admin']['id']))) . '/dimensions/' . $dimension['dimension'],
                            'type' => 'Dimension',
                            'classified_as' => [
                                [
                                    'id' => $dim,
                                    'type' => 'Type',
                                    '_label' => ucwords(strtolower($dimension['dimension']))
                                ]
                            ],
                            'value' => (int) $dimension['value'],
                            'unit' => [
                                'id' => $unit,
                                'type' => 'MeasurementUnit',
                                '_label' => $dimension['units']
                            ]
                        ];
                    }
                }
            }
        }
        return $dimensions;
    }

    public static function buildCurrentLocation(string $priref): array
    {
        $location = AxiellLocation::find($priref);
        if (property_exists($location->adlibJSON, 'recordList') && is_array($location->adlibJSON->recordList->record)) {
            foreach ($location->adlibJSON->recordList->record as $record) {
                if (property_exists($record, "current_location")) {
                    if (property_exists($record, "current_location.type")) {
                        $display = $record->{"current_location.type"}[0];
                    }
                    if (property_exists($record, "current_location.description")) {
                        $granular = ' ' . $record->{"current_location.description"}[0];
                    }
                }
            }
        } else {
            $display = null;
            $granular = null;
        }

        if (isset($display) && ($display === 'display')) {
            $class = [
                "id" => "http://vocab.getty.edu/aat/300240057",
                "type" => "Type",
                "_label" => "Galleries (Display Spaces) [Object On Display]"
            ];
        } else {
            $class = [
                "id" => "http://vocab.getty.edu/aat/300004465",
                "type" => "Type",
                "_label" => "Object in storage"
            ];
        }

        if (!empty($display)) {
            return [
                'id' => 'http://vocab.getty.edu/ulan/500219279',
                'type' => 'Place',
                '_label' => ucwords($display) . ' Fitzwilliam Museum' . $granular,
                'classified_as' => [$class]
            ];
        } else {
            return [];
        }
    }

    /**
     * @param string $unit
     * @return string
     */
    public static function getUnitsURI(string $unit): string
    {
        return match ($unit) {
            'cm' => 'http://vocab.getty.edu/aat/300379098',
            'mm' => 'http://vocab.getty.edu/aat/300379097',
            'in' => 'http://vocab.getty.edu/aat/300379100',
            'ft' => 'http://vocab.getty.edu/aat/300379101',
            'yd' => 'http://vocab.getty.edu/aat/300379102',
            'm' => 'http://vocab.getty.edu/aat/300379099',
            'km' => 'http://vocab.getty.edu/aat/300379103',
            'degrees' => 'https://qudt.org/vocab/unit/DEG',
            default => ''
        };
    }

    /**
     * @param string $dimension
     * @return string
     */
    public static function getDimensionURI(string $dimension): string
    {
        return match ($dimension) {
            'height' => 'http://vocab.getty.edu/aat/300055644',
            'width' => 'http://vocab.getty.edu/aat/300055647',
            'depth' => 'http://vocab.getty.edu/aat/300072633',
            'circumference' => 'http://vocab.getty.edu/aat/300055623',
            'area' => 'http://vocab.getty.edu/aat/300055621',
            'diameter' => 'http://vocab.getty.edu/aat/300055624',
            'length' => 'http://vocab.getty.edu/aat/300055645',
            'thickness' => 'http://vocab.getty.edu/aat/300055646',
            'volume' => 'http://vocab.getty.edu/aat/300055649',
            'breadth' => 'http://vocab.getty.edu/aat/300404164',
            'die axis' => 'http://nomisma.org/id/axis',
            default => '',
        };
    }

    public static function buildOwner(): array
    {
        return [
            [
                'id' => 'http://vocab.getty.edu/ulan/' . env('FITZ_ULAN'),
                'type' => 'Group',
                '_label' => 'The Fitzwilliam Museum',
                'classified_as' => [
                    [
                        'id' => 'http://vocab.getty.edu/aat/300312281',
                        'type' => 'Type',
                        '_label' => 'Museum'
                    ]
                ],
                'member_of' => [
                    [
                        'id' => 'http://vocab.getty.edu/ulan/500247221',
                        'type' => 'Group',
                        '_label' => 'The University of Cambridge',
                        'classified_as' => [
                            [
                                'id' => 'http://vocab.getty.edu/ulan/500000003',
                                'type' => 'Type',
                                '_label' => 'Corporate Body'
                            ]
                        ]
                    ]
                ],
                'subject_of' => [
                    [
                        'id' => 'https://fitzmuseum.cam.ac.uk',
                        'type' => 'LinguisticObject',
                        '_label' => 'Homepage for the Museum',
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
                        'format' => 'text/html',
                    ]
                ],
                'exact_match' => [
                    [
                        'id' => 'https://www.wikidata.org/wiki/Q1421440',
                        'type' => 'Type',
                        '_label' => 'The Fitzwilliam Museum'
                    ]
                ]
            ],
        ];
    }

    /**
     * @return array
     * @todo this is an empty array as no data is available at the moment for who edited records via the CIIM.
     *
     */
    public static function buildAttributions(): array
    {
        return (array)null;
    }

    public static function buildCurrentKeeper(array $data): array
    {
        return [
            [
                'id' => route('department', $data['department']['value']),
                'type' => 'Groups',
                '_label' => $data['department']['value'],
                'format' => [
                    'application/ld+json;'
                ],
                'LinguisticObject' => [
                    'id' => 'https://vocab.getty.edu/aat/300263534',
                    'type' => 'Type',
                    '_label' => 'Department'
                ],
            ]
        ];
    }

    /**
     * @param $array
     * @param $keySearch
     * @return bool
     */
    public static function findKey($array, $keySearch): bool
    {
        foreach ($array as $key => $item) {
            if ($key == $keySearch) {
                return true;
            } elseif (is_array($item) && self::findKey($item, $keySearch)) {
                return true;
            }
        }
        return false;
    }

    public static function buildSubjects(array $data, string $priref): array
    {
        $subjects = (array)null;
        $subjects[] = [
            'id' => self::createUri(),
            'type' => 'LinguisticObject',
            '_label' => 'Fitzwilliam Homepage for ' . self::getAccessionNumber($data),
            'digitally_carried_by' => [
                [
                    'id' => self::createUri(),
                    'type' => 'DigitalObject',
                    '_label' => 'Fitzwilliam Homepage for ' . self::getAccessionNumber($data),
                    'format' => 'text/html',
                    'access_point' => [
                        [
                            'id' => route('record', $data['identifier'][1]['priref']),
                            'type' => 'DigitalObject',
                            'format' => [
                                'application/ld+json'
                            ]
                        ]
                    ],
                    'classified_as' => [
                        [
                            'id' => 'http://vocab.getty.edu/aat/300264578',
                            'type' => 'Type',
                            '_label' => 'Web Page'
                        ]
                    ],
                    'identified_by' => [
                        [
                            'id' => self::createUri(),
                            'type' => 'Name',
                            'content' => 'Fitzwilliam Museum homepage for ' . self::getAccessionNumber($data) . ': ' . self::getLabel($data),
                        ]
                    ]
                ]
            ],
        ];
        if (Arr::has($data, 'multimedia')) {
            if (self::findKey($data['multimedia'], 'zoom')) {
                $subjects[] = [
                    "id" => 'https://api.fitz.ms/data-distributor/iiif/object-' . $priref . '/manifest',
                    "type" => "InformationObject",
                    "_label" => 'IIIF Manifest for ' . self::getAccessionNumber($data) . ': ' . self::getLabel($data),
                    "classified_as" => [
                        [
                            "id" => "https://data.getty.edu/local/thesaurus/iiif-manifest",
                            "type" => "Type",
                            "_label" => "IIIF Manifest"
                        ]
                    ],
                    "conforms_to" => [
                        [
                            "id" => "http://iiif.io/api/presentation",
                            "type" => "InformationObject",
                            "_label" => "IIIF Presentation API"
                        ]
                    ],
                    "format" => 'application/ld+json;profile=\'http://iiif.io/api/presentation/2/context.json\'',
                ];
            }
        }
        return $subjects;
    }

    /**
     * @param object $data
     * @param string $priref
     * @return array
     */
    public static function createLinkedArt(object $data, string $priref): array
    {
        self::$data = $data->toArray();
        $object = [
            '@context' => 'https://linked.art/ns/v1/linked-art.json',
            'id' => route('record', $priref),
            'type' => 'HumanMadeObject',
            '_label' => self::getLabel($data),
            'classified_as' => self::buildCategories($data->toArray()),
            'identified_by' => self::buildIdentifiers($data->toArray()),
            'referred_to_by' => self::buildReferred($data->toArray()),
            'dimension' => self::buildDimensions($data->toArray()),
            'produced_by' => self::buildProduction($data->toArray()),
            'current_keeper' => self::buildCurrentKeeper($data->toArray()),
            'current_location' => self::buildCurrentLocation($priref),
            'current_owner' => self::buildOwner(),
//            'attributed_by' => self::buildAttributions($data->toArray()),
            'subject_of' => self::buildSubjects($data->toArray(), $priref),
            'representation' => self::buildRepresentation($data->toArray()),
            'carries' => self::buildCarrierInformation($data->toArray(), $priref),
            'made_of' => self::buildMaterials($data->toArray()),
            'member_of' => self::buildExhibitions($data->toArray()),
        ];
        return self::cleanUp($object);
    }

    public static function buildExhibitions(array $data): array
    {
        $exhibitions = [];
        if (Arr::has($data, 'exhibitions')) {
            foreach ($data['exhibitions'] as $exhibition) {
                $exhibitions[] = [
                    'id' => route('exhibition.record', $exhibition['admin']['id']),
                    'type' => 'Set',
                    '_label' => 'Objects exhibited in : ' . $exhibition['summary_title']
                ];
            }
        }
        return $exhibitions;
    }

    public static function buildMaterials(array $data): array
    {
        $materials = [];
        if (Arr::has($data, 'materials')) {
            foreach ($data['materials'] as $material) {
                $materials[] = [
                    'id' => route('terminology', $material['reference']['admin']['id']),
                    'type' => 'Material',
                    '_label' => $material['reference']['summary_title'],
                ];
            }
        }
        return $materials;
    }

    public static function buildInscriptionString($information){
        $inscription = '';
        if (Arr::has($information, 'transcription')) {
            $inscription .= $information['transcription'][0]['value'];
        }
        if (Arr::has($information, 'description')) {
            $inscription .= ' (' . $information['description'][0]['value'] . ')';
        }
        if(Arr::has($information,'method')){
            $inscription .= ' ' . ucfirst($information['method']);
        }
        if(Arr::has($information,'location')){
            $inscription .= ' ' . $information['location'];
        }
        $inscription .= '.';
        return $inscription;
    }
    /**
     * @param array $data
     * @param $priref
     * @return array
     */
    public static function buildCarrierInformation(array $data, $priref): array
    {
        $carries = [];
        if (Arr::has($data, 'inscription')) {
            $count = 0;
            foreach ($data['inscription'] as $information) {
                $count = $count + 1;
                if (Arr::has($information, 'transcription') || Arr::has($information, 'description')) {
                    $carries[] = [
                        'id' => route('linked.art.inscription', [$priref, $count]),
                        'type' => 'LinguisticObject',
                        'classified_as' => [
                            [
                                "id" => "http://vocab.getty.edu/aat/300435414",
                                "type" => "Type",
                                "_label" => "Inscription Description"
                            ],
                            [
                                "id" => "http://vocab.getty.edu/aat/300418049",
                                "type" => "Type",
                                "_label" => "Brief Text"
                            ]
                        ],
                        'content' => self::buildInscriptionString($information),
                    ];
                }
            }
        }
        return $carries;
    }


    /**
     * @param $data
     * @return array|void
     */
    public static function createCreditLine($data)
    {
        if (Arr::has($data, 'legal')) {
            if (Arr::has($data['legal'], 'credit_line')) {
                return [
                    'id' => route('linked.art.legal_credit_line', str_replace('object-', '', $data['admin']['id'])),
                    "type" => "LinguisticObject",
                    "_label" => "Source Credit Line",
                    "classified_as" => [
                        [
                            "id" => "http://vocab.getty.edu/aat/300435418",
                            "type" => "Type",
                            "_label" => "Credit Line"
                        ],
                        [
                            "id" => "http://vocab.getty.edu/aat/300404764",
                            "type" => "Type",
                            "_label" => "Sources (General Concept)"
                        ],
                        [
                            "id" => "http://vocab.getty.edu/aat/300418049",
                            "type" => "Type",
                            "_label" => "Brief Text"
                        ]
                    ],
                    "content" => Arr::get($data, 'legal.credit_line')
                ];
            }
        }
    }
}


