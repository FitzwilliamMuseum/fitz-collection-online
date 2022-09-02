<?php

namespace App\Models;


use App\FitzElastic\Elastic;
use JetBrains\PhpStorm\Pure;

class Objects extends Model
{
    public static function find(string $priref)
    {
        $params = [
            'index' => 'ciim',
            'size' => 1,
            'body' => [
                'query' => [
                    'match' => [
                        'identifier.priref' => $priref
                    ]
                ]
            ]
        ];

        $response = self::getElastic()->setParams($params)->getSearch();
        $records = Collect($response['hits']['hits'])->first();
        if (empty($records)) {
            abort('404');
        } else {
            return $records['_source'];
        }
    }

    /**
     * @return Elastic
     */
    #[Pure] public static function getElastic(): Elastic
    {
        return new Elastic();
    }
}
