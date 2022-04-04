<?php

namespace App\Models;

use App\MoreLikeThis;
use Psr\SimpleCache\InvalidArgumentException;
use Solarium\Core\Query\DocumentInterface;

class FindMoreLikeThis extends Model
{
    /**
     * @param string $slug
     * @param string $type
     * @return DocumentInterface[]
     * @throws InvalidArgumentException
     */
    public static function find(string $slug, string $type): array
    {
      $mlt = new MoreLikeThis;
      $mlt->setLimit(4)->setType($type)->setQuery(urlencode($slug));
      return $mlt->getData();
    }
}
