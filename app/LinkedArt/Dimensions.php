<?php

namespace App\LinkedArt;
use JetBrains\PhpStorm\ArrayShape;

class Dimensions
{
    /**
     * @param $data
     * @return string
     */
    public static function createUri($data): string
    {
        return str_replace('http://', 'https://', $data['admin']['uri']);
    }

    /**
     * @param array $data
     * @param string $key
     * @return array
     */
    #[ArrayShape(['@context' => "string", 'id' => "string", 'type' => "string", 'dimension' => "array"])] public static function createLinkedArt(array $data, string $key): array
    {
        $dimension = self::dimension($data, $key);
        return [
            '@context' => 'https://linked.art/ns/v1/linked-art.json',
            'id' => self::createUri($data),
            'type' => 'HumanMadeObject',
            'dimension' => $dimension
        ];
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
            default => '',
        };
    }


    #[ArrayShape(['id' => "string", 'type' => "string", 'classified_as' => "array[]", 'value' => "mixed", 'unit' => "array"])] public static function dimension(array $data, string $key): array
    {
        $dims = $data['measurements']['dimensions'];
        foreach($dims as $dim){
            if($dim['dimension'] === $key){
                $metrics = $dim;
            }
        }
        $dimUri = self::getDimensionURI(strtolower($metrics['dimension']));

        return [
            'id' => $data['admin']['uri'] . '/dimensions/' . $key,
            'type' => 'Dimension',
            'classified_as' => [
                [
                    'id' => $dimUri,
                    'type' => 'Type',
                    '_label' => ucwords(strtolower($metrics['dimension']))
                ]
            ],
            'value' => $metrics['value'],
            'unit' => [
                'id' => self::getUnitsURI(strtolower($metrics['units'])),
                'type' => 'MeasurementUnit',
                '_label' => $metrics['units']
            ]
        ];
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
            default => ''
        };
    }
}
