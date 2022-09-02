<?php

namespace App\LinkedArt;

use App\LookupPlace;
use Geocoder\Exception\Exception;

class Place
{
    /**
     * @throws Exception
     */
    public static function createLinkedArtPlace(array $data): array
    {
        $place = [
            '@context' => 'https://linked.art/ns/v1/linked-art.json',
            'id' => route('terminology', $data['admin']['id']),
            'type' => 'Place',
            '_label' => $data['summary_title'],
            'format' => [
                'application/ld+json'
            ],
            'approximated_by' => [
                self::createPoint($data['summary_title'])
            ],
            'subject_of' => [
                [
                    'id' => route('terminology',$data['admin']['id']),
                    'type' => 'LinguisticObject',
                    '_label' => 'Homepage for this term',
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
                            'text/html',
                            'application/ld+json'
                        ]
                    ]
                ]
            ],
            'classified_as' => [
                [
                    'id' => 'http://vocab.getty.edu/aat/300024979',
                    'type' => 'Type',
                    '_label' => 'places (agents)',
                ]
            ],
            'close_match' => CloseMatch::buildCloseMatch($data['admin']['id']),
            'identified_by' => [
                [
                    'type' => 'Identifier',
                    '_label' => 'Preferred name',
                    'content' => $data['summary_title'],
                ],
                [
                    'type' => 'Identifier',
                    '_label' => 'System assigned ID',
                    'content' => $data['admin']['id'],
                ],
            ],
        ];
        return array_filter($place);
    }

    /**
     * @param string $placeName
     * @return array|null
     * @throws Exception
     */
    public static function createPoint(string $placeName): ?array
    {
        $geo = new LookupPlace;
        $geo->setPlace($placeName);
        $results = $geo->lookup();

        if (!empty($results)) {
            $coords = $results->first()->getCoordinates();
            return [
                "type" =>  "Place",
                '_label' => 'Approximation for ' . $placeName,
                'defined_by' => 'POINT(' . $coords->getLatitude() . ' ' . $coords->getLongitude() . ')'
            ];
        } else {
            return null;
        }
    }

}
