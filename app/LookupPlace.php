<?php

namespace App;
use Geocoder\Query\GeocodeQuery;
use Http\Adapter\Guzzle7\Client;

class LookupPlace {

  protected $_place;

  public function setPlace(string $place){
    $this->_place = $place;
  }

  public function getPlace(){
    return $this->_place;
  }

  public function lookup(){
    $httpClient =  new \Http\Adapter\Guzzle7\Client;
    $provider =  \Geocoder\Provider\Nominatim\Nominatim::withOpenStreetMapServer($httpClient, 'fitz-beta');
    $geocoder = new \Geocoder\StatefulGeocoder($provider, 'en');
    return $geocoder->geocodeQuery(GeocodeQuery::create($this->getPlace()));
  }

}
