<?php

namespace App\LinkedArt;

use App\Models\Model;

class Department
{

    public static function createLinkedArtDepartment(string $data): array
    {
        $department = [
            '@context' => 'https://linked.art/ns/v1/linked-art.json',
            'id' => route('department', urlencode($data)),
            'type' => 'Group',
            '_label' => 'Historical department of: ' . urldecode($data),
            'member_of' => [
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
                        ],
                    ],
                ]
            ],
        ];
        return array_filter($department);
    }
}
