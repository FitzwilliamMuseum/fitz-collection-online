<?php

namespace App\Models\Api;

class Departments extends Model
{
    public static function list()
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                'size' => 0,
                'aggregations' => [
                    'department' => [
                        'terms' => [
                            'field' => 'department.value.keyword',
                            'size' => 10,
                        ],
                    ],
                ],
            ]
        ];
        return parent::searchAndCache($params);
    }
}
