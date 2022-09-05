<?php

namespace App\LinkedArt;

use JetBrains\PhpStorm\ArrayShape;

class Citation
{

    /**
     * @param $data
     * @param $publication
     * @return array
     */
    #[ArrayShape(['@context' => "string", 'id' => "array|string|string[]", 'type' => "string", 'referred_to_by' => "array"])] public static function createLinkedArt($data, $publication): array
    {
        return [
            '@context' => 'https://linked.art/ns/v1/linked-art.json',
            'id' => str_replace('http://', 'https://', $data['admin']['uri']),
            'type' => 'HumanMadeObject',
            'referred_to_by' => [
                'id' => route('linked.art.citation', [str_replace('object-', '', $data['admin']['id']), $publication['admin']['uuid']]),
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
            ]
        ];
    }
}
