<?php

namespace App\Models;

use App\FitzElastic\Elastic;
use JetBrains\PhpStorm\Pure;

class MltObjects extends Model
{
    /**
     * @return Elastic
     */
    #[Pure] public static function getElastic(): Elastic
    {
        return new Elastic();
    }

    public static function findMoreLikeThis($data, string $type)
    {
        if (array_key_exists('title', $data)) {
            $query = $data['title'][0]['value'];
        } else {
            $query = $data['summary_title'];
        }
        $string = '{ "_id" : "' . $data['admin']['uid'] . '"},"' . urlencode($query) . '"';
        $json = '{
          "query": {
            "bool": {
              "must": [
                {
                  "more_like_this": {
                    "fields": [
                      "_generic_all_std"
                    ],
                    "like": [

                      ' . $string . '

                    ],
                    "min_term_freq": 1,
                    "min_doc_freq": 1,
                    "max_query_terms": 15,
                    "stop_words": [],
                    "boost": 2,
                    "include": false
                  }
                }
              ],
              "filter": [
                {
                  "exists": {
                    "field": "multimedia"
                  }
                },
                {
                  "term": {
                    "type.base": "object"
                  }
                }
              ]
            }
          }
        }';

        $paramsMLT = [
            'index' => 'ciim',
            'size' => 4,
            'body' => $json
        ];
        $response = self::getElastic()->setParams($paramsMLT)->getSearch();
        return $response['hits']['hits'];
    }
}
