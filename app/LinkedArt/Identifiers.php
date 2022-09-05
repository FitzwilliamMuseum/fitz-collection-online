<?php

namespace App\LinkedArt;

use JetBrains\PhpStorm\ArrayShape;

class Identifiers
{

    /**
     * @param array $data
     * @return string
     */
    public static function createBaseUri(array $data): string
    {
        return route('record', str_replace('object-', '', $data['admin']['id']));
    }

    /**
     * @param array $data
     * @return string
     */
    public static function createId(array $data): string
    {
        return str_replace('object-', '', $data['admin']['id']);
    }
    /**
     * @param array $data
     * @param string $key
     * @return array
     */
    #[ArrayShape(["@context" => "string", "id" => "string", "type" => "string", "identified_by" => "array"])] public static function createLinkedArt(array $data, string $key): array
    {
        $identifier = match ($key) {
            'accession_number' => self::accession_number($data),
            'title' => self::title($data),
            default => self::priref($data),
        };

        return [
            "@context" => "https://linked.art/ns/v1/linked-art.json",
            "id" => self::createBaseUri($data),
            "type" => "HumanMadeObject",
            "identified_by" => $identifier
        ];

    }

    /**
     * @param array $data
     * @return array
     */
    #[ArrayShape(['id' => "string", 'type' => "string", 'classified_as' => "\string[][]", 'content' => "mixed"])] public static function accession_number(array $data): array
    {
        return [
            'id' => route('linked.art.identifiers', [self::createId($data), 'accession_number']),
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
    }

    /**
     * @param array $data
     * @return array
     */
    #[ArrayShape(['id' => "string", 'type' => "string", 'classified_as' => "\string[][]", 'content' => "mixed"])] public static function priref(array $data): array
    {
        return [
            'id' => route('linked.art.identifiers', [self::createId($data), 'priref']),
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
    }

    /**
     * @param array $data
     * @return array
     */
    #[ArrayShape(['id' => "string", 'type' => "string", 'classified_as' => "\string[][]", 'content' => "mixed | string", 'language' => "\string[][]"])] public static function title(array $data): array
    {
        return [
            'id' => route('linked.art.identifiers', [self::createId($data), 'title']),

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
    }
}
