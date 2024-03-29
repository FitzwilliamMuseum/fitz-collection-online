<?php
namespace App\Models\Api;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class IpAddress
{
    /**
     * @return array
     */
    public static function whitelist(): array
    {
        $data = self::getData();
        $whitelistedIps = [];
        if(array_key_exists('data', $data))
        {
            foreach($data['data'] as $item) {
                $whitelistedIps[] = $item['ip_address'];
            }
        }

        return $whitelistedIps;
    }

    /**
     * @return array
     */
    public static function getData(): array
    {
        $url = 'https://content.fitz.ms/fitz-website/items/api_ip_whitelist?fields=ip_address&access_token=' . env('DIRECTUS_ACCESS_TOKEN');
        $key = md5($url);
        $expiresAt = now()->addDays(60);
        if (Cache::has($key)) {
            $data = Cache::get($key);
        } else {
            $response = Http::get($url);
            $data = $response->json();
            Cache::put($key, $data, $expiresAt);
        }
        return $data;
    }
}
