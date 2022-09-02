<?php

namespace App\LinkedArt;

class Image
{

    public static function createLinkedArtImage(array $data): array
    {
        $image = [
            '@context' => 'https://linked.art/ns/v1/linked-art.json',
            'id' => route('record', $data['admin']['id']),
            'type' => 'Person',
            '_label' => $data['summary_title'],
            'format' => [
                'application/ld+json'
            ],
        ];
        return array_filter($image);
    }

}
