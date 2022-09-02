<?php

namespace App;

use DOMException;
use Spatie\ArrayToXml\ArrayToXml;

class CollectionXML
{
    /**
     * @param array $data
     * @return string
     * @throws DOMException
     */
    public static function createXML(array $data): string
    {
        $cleaned = self::utf8_converter($data);
        $processed = self::replaceKeys('@link', 'link', $cleaned);
        $arrayToXml = new ArrayToXml($processed);
        return $arrayToXml->prettify()->toXml();
    }

    /**
     * @param $array
     * @return array
     */
    public static function utf8_converter($array): array
    {
        array_walk_recursive($array, function (&$item, $key) {
            if (!mb_detect_encoding($item, 'utf-8', true)) {
                $item = utf8_encode($item);
                $item = str_replace('\u', 'u', $item);
                $item = preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', $item);
            }
        });
        return $array;
    }

    /**
     * @param $oldKey
     * @param $newKey
     * @param array $input
     * @return array
     */
    public static function replaceKeys($oldKey, $newKey, array $input): array
    {
        $return = array();
        foreach ($input as $key => $value) {
            if ($key === $oldKey)
                $key = $newKey;
            if (is_array($value))
                $value = self::replaceKeys($oldKey, $newKey, $value);
            $return[$key] = $value;
        }
        return $return;
    }
}
