<?php

namespace App\Models\Api;

use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;

class Departments extends Model
{
    /**
     * @var array
     */
    public static function list(): array
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

    /**
     * @param string $department
     * @return array
     */
    public static function show(Request $request, string $department): array
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                'query' => [
                    'match' => [
                        'department.value.keyword' => Purifier::clean($department, array('HTML.Allowed' => ''))
                    ]
                ],
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
