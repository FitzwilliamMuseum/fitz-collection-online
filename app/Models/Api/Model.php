<?php

namespace App\Models\Api;


use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Model
{
    /**
     * @param array $params
     * @return array|callable|mixed
     */
    public function searchAndCache(array $params): mixed
    {
        $key = $this->getKey($params);
        $expiresAt = now()->addMinutes(60);
        if (Cache::has($key)) {
            $data = Cache::get($key);
        } else {
            $data = $this->getClient()->search($params);
            Cache::put($key, $data, $expiresAt);
        }
        return $data;
    }

    /**
     * @param array $params
     * @return string
     */
    public function getKey(array $params): string
    {
        return md5(json_encode($params));
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return ClientBuilder::create()->setHosts($this->getHosts())->build();
    }

    /**
     * @return array[]
     */
    public function getHosts(): array
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
     * @param Request $request
     * @return int
     */
    public function getSize(Request $request): int
    {
        $size = 20;
        $params = $request->query();
        if (is_array($params)) {
            if (array_key_exists('size', $params) && $params['size'] > 0) {
                $size = $params['size'];
            }
        }
        return $size;
    }

    /**
     * @param Request $request
     * @return array|string
     */
    public function getSort(Request $request): array|string
    {
        $sort = '';
        $params = $request->query();
        if (is_array($params)) {
            if (array_key_exists('sort', $params)) {
                $sort = array(
                    "admin.modified" => [
                        "order" => $params['sort']
                    ]
                );
            } else {
                $sort = array(
                    "admin.modified" => [
                        "order" => 'asc'
                    ]
                );
            }
        }
        return $sort;
    }

    /**
     * @param Request $request
     * @return mixed|void
     */
    public function getQueryFields(Request $request)
    {
        $params = $request->query();
        if (is_array($params)) {
            if (array_key_exists('fields', $params)) {
                return $params['fields'];
            }
        }
    }
}
