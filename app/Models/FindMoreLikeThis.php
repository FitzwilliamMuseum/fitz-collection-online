<?php

namespace App\Models;

use App\MoreLikeThis;

class FindMoreLikeThis extends Model
{
    public static function find(string $slug, string $type)
    {
      $mlt = new MoreLikeThis;
      $mlt->setLimit(4)->setType($type)->setQuery(urlencode($slug));
      return $mlt->getData();
    }
}
