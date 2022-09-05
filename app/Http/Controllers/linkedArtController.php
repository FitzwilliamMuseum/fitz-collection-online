<?php

namespace App\Http\Controllers;

use App\LinkedArt\Citation;
use App\LinkedArt\CitationSequence;
use App\LinkedArt\ObjectOrArtwork;
use App\LinkedArt\Identifiers;
use App\LinkedArt\Dimensions;
use App\LinkedArt\Production;
use App\Models\Agents;
use App\Models\Objects;
use App\Models\Publications;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use JetBrains\PhpStorm\ArrayShape;

class linkedArtController
{

    /**
     * @param string $priref
     * @param string $key
     * @return JsonResponse
     */
    public function identifiers(string $priref, string $key): JsonResponse
    {
        $data = Objects::find($priref);
        return response()->json(Identifiers::createLinkedArt($data, $key));
    }

    /**
     * @param string $priref
     * @return JsonResponse
     */
    public function referred_to_by(string $priref): JsonResponse
    {
        $data = Objects::find($priref);
        return response()->json(ObjectOrArtwork::createLinkedArt(Collect($data), $priref));
    }

    /**
     * @param string $priref
     * @param string $key
     * @return JsonResponse
     */
    public function dimensions(string $priref, string $key): JsonResponse
    {
        $data = Objects::find($priref);
        return response()->json(Dimensions::createLinkedArt($data, $key));
    }

    /**
     * @param string $priref
     * @param string $count
     * @return JsonResponse
     */
    public function citation(string $priref, string $count): JsonResponse
    {
        $data = Objects::find($priref);
        return response()->json(CitationSequence::createLinkedArt($data, $count));
    }

    /**
     * @param string $priref
     * @param string $uuid
     * @return JsonResponse
     */
    public function citationDetails(string $priref, string $uuid): JsonResponse
    {
        $data = Objects::find($priref);
        $publication = Publications::findByUuid($uuid);
        return response()->json(Citation::createLinkedArt($data, $publication));
    }

    /**
     * @param string $priref
     * @return JsonResponse
     */
    public function timeSpan(string $priref): JsonResponse
    {
        $data = Objects::find($priref);
        return response()->json(Production::createLinkedArtTimeSpan($data));
    }

    /**
     * @param string $priref
     * @return JsonResponse
     */
    public function timeSpanDetails(string $priref): JsonResponse
    {
        $data = Objects::find($priref);
        return response()->json(Production::createLinkedArtTimeSpanDetails($data));
    }

    /**
     * @param string $priref
     * @return JsonResponse
     */
    public function production(string $priref): JsonResponse
    {
        $data = Objects::find($priref);
        return response()->json(Production::createLinkedArt($data));
    }

    /**
     * @param $information
     * @return string
     */
    public static function buildInscriptionString($information): string
    {
        $inscription = '';
        if (Arr::has($information, 'transcription')) {
            $inscription .= $information['transcription'][0]['value'];
        }
        if (Arr::has($information, 'description')) {
            $inscription .= ' (' . $information['description'][0]['value'] . ')';
        }
        if (Arr::has($information, 'method')) {
            $inscription .= ' ' . ucfirst($information['method']);
        }
        if (Arr::has($information, 'location')) {
            $inscription .= ' ' . $information['location'];
        }
        $inscription .= '.';
        return $inscription;
    }

    /**
     * @param string $priref
     * @param int $count
     * @return JsonResponse
     */
    public function inscription(string $priref, int $count): JsonResponse
    {
        $data = Objects::find($priref);
        $key = $count - 1;
        if (Arr::has($data, 'inscription')) {
            $inscription = $data['inscription'][$key];
            $legend = [
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
                'content' => self::buildInscriptionString($inscription),
            ];
        }
        return response()->json($legend);
    }

    /**
     * @param string $priref
     * @return JsonResponse
     */
    public function materials(string $priref): JsonResponse
    {
        $data = Objects::find($priref);
        $materials = [];
        foreach ($data['materials'] as $material) {
            if (array_key_exists('note', $material)) {
                $notes = ' (' . implode(' ', Arr::flatten($material['note'])) . ')';
            } else {
                $notes = '';
            }
            $materials[] = $material['reference']['summary_title'] . $notes;
        }
        $materialDescription = [
            '@context' => 'http://linked.art/ns/v1/linked-art.json',
            'id' => route('record', [$priref]),
            'type' => 'HumanMadeObject',
            'referred_to_by' => [
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
            ]
        ];
        return response()->json($materialDescription);
    }

    /**
     * @param string $priref
     * @return JsonResponse
     */
    public function objectType(string $priref): JsonResponse
    {
        $data = Objects::find($priref);
        return response()->json([
            '@context' => 'http://linked.art/ns/v1/linked-art.json',
            'id' => route('record', [$priref]),
            'type' => 'HumanMadeObject',
            'classified_as' => [
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
            ]
        ]);
    }

    /**
     * @param string $priref
     * @return JsonResponse
     */
    public function dimensionsStatement(string $priref): JsonResponse
    {
        $data = Objects::find($priref);
        return response()->json([
            '@context' => 'http://linked.art/ns/v1/linked-art.json',
            'id' => route('record', $priref),
            'type' => 'HumanMadeObject',
            'referred_to_by' => [
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
            ]
        ]);
    }

    /**
     * @param string $priref
     * @param string $key
     * @return JsonResponse
     */
    public function roleStatement(string $priref, string $key): JsonResponse
    {
        $data = Objects::find($priref);
        $arrayKey = 0;
        foreach ($data['lifecycle']['creation'][0]['maker'] as $producer) {
            $arrayKey = $arrayKey + 1;
            if ($producer['admin']['uuid'] === $key) {
                $keyToFind = $arrayKey - 1;
            }
        }
        $maker = Agents::findByUuid($key);
        return response()->json(Production::createLinkedArtMaker($data, $maker, $keyToFind));
    }

    /**
     * @param string $priref
     * @return JsonResponse
     */
    public function legalCreditLine(string $priref): JsonResponse
    {
        $data = Objects::find($priref);
        return response()->json(ObjectOrArtwork::createCreditLine($data));
    }

    /**
     * @param string $priref
     * @return JsonResponse
     */
    public function creditLine(string $priref): JsonResponse
    {
        $credit = [
            '@context' => 'http://linked.art/ns/v1/linked-art.json',
            'id' => route('record', $priref),
            'type' => 'HumanMadeObject',
            'referred_to_by' => [
                'id' => route('linked.art.credit_line', $priref),
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
            ]
        ];
        return response()->json($credit);
    }

    /**
     * @param $priref
     * @return JsonResponse
     */
    public function license($priref): JsonResponse
    {
        $data = Objects::find($priref);
        $description = self::createDescription($data);
        if (!empty($description)) {
            $license = $description['subject_to'][0];
        } else {
            $license = [
                "id" => "http://creativecommons.org/licenses/by-nc-sa/4.0/",
                "type" => "License",
                "_label" => "Attribution-NonCommercial-ShareAlike 4.0 International (CC BY-NC-SA 4.0)"
            ];
        }
        return response()->json($license);
    }

    /**
     * @param string $priref
     * @return JsonResponse
     */
    public function acknowledgements(string $priref): JsonResponse
    {
        return response()->json([
            'id' => route('linked.art.acknowledgements', $priref),
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
        ]);
    }

    /**
     * @param array $data
     * @return array|void
     */
    #[ArrayShape(['id' => "string", 'type' => "string", '_label' => "string", 'classified_as' => "\string[][]", 'content' => "string", 'subject_to' => "array[]", "format" => "string"])] public function createDescription(array $data)
    {
        if (array_key_exists('description', $data)) {
            return [
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
    }

    public function descriptions(string $priref): JsonResponse
    {
        $data = Objects::find($priref);
        return response()->json(self::createDescription($data));
    }
}
