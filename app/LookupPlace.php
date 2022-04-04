<?php

namespace App;

use Geocoder\Collection;
use Geocoder\Exception\Exception;
use Geocoder\Provider\Nominatim\Nominatim;
use Geocoder\Query\GeocodeQuery;
use Geocoder\StatefulGeocoder;
use Http\Adapter\Guzzle7\Client;

class LookupPlace
{

    /**
     * @var string
     */
    protected string $_place;

    /**
     * @return Collection
     * @throws Exception
     */
    public function lookup(): Collection
    {
        $httpClient = new Client;
        $provider = Nominatim::withOpenStreetMapServer($httpClient, 'fitz-beta');
        $geocoder = new StatefulGeocoder($provider, 'en');
        return $geocoder->geocodeQuery(GeocodeQuery::create($this->getPlace()));
    }

    /**
     * @return string
     */
    public function getPlace(): string
    {
        return $this->_place;
    }

    /**
     * @param string $place
     * @return void
     */
    public function setPlace(string $place)
    {
        $this->_place = $place;
    }

}
