<?php

namespace App\LinkedArt;

use App\Models\LinkedArtIdentifiers;
use Illuminate\Support\Arr;
use App\LinkedArt\CloseMatch;

class Person
{

    /**
     * @param array $data
     * @return array
     */
    public static function createLinkedArtPerson(array $data): array
    {
        $person = [
            '@context' => 'https://linked.art/ns/v1/linked-art.json',
            'id' => route('agent', $data['admin']['id']),
            'type' => 'Person',
            '_label' => $data['summary_title'],
            'format' => [
                'application/ld+json'
            ],
            'subject_of' => [
                [
                    'id' => 'https://data.fitzmuseum.cam.ac.uk/id/agent/' . $data['admin']['id'],
                    'type' => 'LinguisticObject',
                    '_label' => 'Homepage for agent',
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
            'classified_as' => [
                [
                    'id' => 'http://vocab.getty.edu/aat/300024979',
                    'type' => 'Type',
                    '_label' => 'people (agents)',
                ]
            ],
            'close_match' => CloseMatch::buildCloseMatch($data['admin']['id']),
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

        ];
        return array_filter($person);
    }
}
