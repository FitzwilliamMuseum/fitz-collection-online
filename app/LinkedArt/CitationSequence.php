<?php

namespace App\LinkedArt;

class CitationSequence
{
    public static function createLinkedArt(array $data, $count): array
    {
        $priref = str_replace('object-', '', $data['admin']['id']);
        return [
            '@context' => 'https://linked.art/ns/v1/linked-art.json',
            'id' => str_replace('http://', 'https://', $data['admin']['uri']),
            'type' => 'HumanMadeObject',
            'referred_to_by' => [
                "id" => route('linked.art.citation.sequence', [$priref, $count]),
                "type" => "Dimension",
                "_label" => "Citation (Bibliographic Reference) Sequence",
                'classified_as' => [
                    [
                        "id" => "http://vocab.getty.edu/aat/300010269",
                        "type" => "Type",
                        "_label" => "Positional Attributes"
                    ],
                    [
                        "id" => "http://vocab.getty.edu/aat/300192339",
                        "type" => "Type",
                        "_label" => "Sequences"
                    ]
                ],
                'value' => (int)$count,
                'unit' => [
                    "id" => "http://vocab.getty.edu/aat/300055665",
                    "type" => "MeasurementUnit",
                    "_label" => "Numbers"
                ]
            ]
        ];
    }
}
