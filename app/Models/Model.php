<?php

namespace App\Models;


use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Model
{
    /**
     * @param array $params
     * @return array
     */
    public static function searchAndCache(array $params): array
    {
        $key = self::getKey($params);
        $expiresAt = now()->addMinutes(60);
        if (Cache::has($key)) {
            $data = Cache::get($key);
        } else {
            $data = self::getClient()->search($params);
            Cache::put($key, $data, $expiresAt);
        }
        return $data;
    }

    /**
     * @return Client
     */
    public static function getClient(): Client
    {
        return ClientBuilder::create()->setHosts(self::getHosts())->build();
    }

    /**
     * @return array[]
     */
    public static function getHosts(): array
    {
        return [
            [
                'host' => env('ELASTIC_API'),
                'port' => '80',
                'path' => env('ELASTIC_PATH'),
            ]
        ];
    }

    /**
     * @param array $params
     * @return string
     */
    public static function getKey(array $params): string
    {
        return md5(json_encode($params));
    }

    /**
     * @param Request $request
     * @return int
     */
    public static function getFrom(Request $request): int
    {
        if ($request->query('page') && $request->query('page') > 1) {
            return ($request->query('page') -1) * 50;
        } else {
            return 0;
        }
    }
}
