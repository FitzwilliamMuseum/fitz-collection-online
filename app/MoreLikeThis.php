<?php

namespace App;

use Config;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;
use Solarium\Client;
use Solarium\Core\Client\Adapter\Curl;
use Symfony\Component\EventDispatcher\EventDispatcher;

class MoreLikeThis
{

    protected string $_query;
    protected string $_type;
    protected int $_limit = 6;

    /**
     * @return array
     * @throws InvalidArgumentException
     */
    public function getData(): array
    {
        $queryString = $this->getQuery();
        $key = md5($queryString . 'mlt-collection' . $this->getLimit() . $this->getType());
        $expiresAt = now()->addMinutes(3600);
        if (Cache::has($key)) {
            $data = Cache::store('file')->get($key);
        } else {
            $configSolr = Config::get('solarium');
            $client = new Client(new Curl(), new EventDispatcher(), $configSolr);
            $query = $client->createMoreLikeThis();
            $query->setQuery('title:' . $queryString);
            $query->setMltFields('title,description,slug');
            $query->setMinimumDocumentFrequency(2);
            $query->setMinimumTermFrequency(1);
            $query->createFilterQuery('type')->setQuery('contentType:' . $this->getType());
            $query->createFilterQuery('insta')->setQuery('-contentType:instagram*');
            $query->createFilterQuery('news')->setQuery('-contentType:news*');
            $query->createFilterQuery('twitter')->setQuery('-contentType:twitter*');
            $query->setInterestingTerms('details');
            $query->setMatchInclude(true);
            $query->setRows($this->getLimit());
            $data = $client->select($query);
            Cache::store('file')->put($key, $data, $expiresAt);
        }
        return $data->getDocuments();
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->_query;
    }

    /**
     * @param $query
     * @return $this
     */
    public function setQuery($query): static
    {
        $this->_query = $query;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->_limit;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function setLimit(int $limit): static
    {
        $this->_limit = $limit;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->_type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): static
    {
        $this->_type = $type;
        return $this;
    }

}
