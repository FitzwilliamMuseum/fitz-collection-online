<?php

namespace App\Http\Controllers\Api;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Mews\Purifier\Facades\Purifier;
use OpenApi\Annotations as OA;
use function env;
use function now;
use function response;
use App\LookupPlace;
use App\Models\AxiellPlaceNames;
/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Fitzwilliam Museum Collection Database API",
 *     description="Api for the Fitzwilliam Museum Collection Database",
 * ),
 *  @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     in="header",
 *     name="Authorization",
 *     ),
 */
class ApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var string
     */
    public string $_success = 'Retrieved successfully';
    /**
     * @var string
     */
    public string $_notFound = 'Nothing found for that query';
    /**
     * @var string
     */
    public string $_error = 'An error occurred';
    /**
     * @var array|string[]
     */
    protected array $_headers = ['Content-type' => 'application/json; charset=utf-8'];

    public array $_sortFields = array(
        'id','created','updated',
        'name','summary_title'
    );
    /**
     * @param $items
     * @param int $perPage
     * @param $page
     * @param array $options
     * @return LengthAwarePaginator
     */
    public function paginate($items, int $perPage = 20, $page = null, array $options = []): LengthAwarePaginator
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    /**
     * @param array $data
     * @param string $type
     * @return array
     */
    public function insertType(array $data, string $type):array
    {
        $typedData = [];
        foreach($data as $datum){
            $datum['type'] = $type;
            $typedData[] = $datum;
        }
        return $typedData;
    }

    public function insertSingleType(array $data, string $type):array
    {
        $data['type'] = $type;
        return $data;
    }

    /**
     * @param Request $request
     * @param LengthAwarePaginator $paginator
     * @param $total
     * @return JsonResponse
     */
    public function jsonGenerate(Request $request, LengthAwarePaginator $paginator, $total): JsonResponse
    {
        return response()->json(
            [
                'data' => $paginator->items(),
                'meta' => array(
                    "current_page" => $paginator->currentPage(),
                    "from" => $paginator->firstItem(),
                    "to" => $paginator->lastItem(),
                    "last_page" => $paginator->lastPage(),
                    "path" => $paginator->path(),
                    "per_page" => $paginator->perPage(),
                    "total" => $total,
                    "parameters" => $request->query->all(),
                ),
                'links' => array(
                    "first" => $paginator->url(1),
                    "last" => $paginator->url($paginator->lastPage()),
                    "self" => $paginator->url($paginator->currentPage()),
                    "prev" => $paginator->previousPageUrl(),
                    "next" => $paginator->nextPageUrl()
                ),
                'message' => $this->_success,
                'httpCode' => 200
            ],
            200,
            $this->getHeaders(),
            JSON_PRETTY_PRINT
        );
    }

    /**
     * [getHeaders description]
     * @return array Headers for request
     */
    public function getHeaders(): array
    {
        return $this->_headers;
    }

    /**
     * @param Request $request
     * @param LengthAwarePaginator $paginator
     * @param array|Collection $items
     * @param int $total
     * @return JsonResponse
     */
    public function jsonAggGenerate(Request $request, LengthAwarePaginator $paginator, array|Collection $items, int $total): JsonResponse
    {
        return response()->json(
            [
                'data' => $items->values(),
                'meta' => array(
                    "current_page" => $paginator->currentPage(),
                    "from" => $paginator->firstItem(),
                    "to" => $paginator->lastItem(),
                    "last_page" => $paginator->lastPage(),
                    "path" => $paginator->path(),
                    "per_page" => $paginator->perPage(),
                    "total" => $total,
                    "parameters" => $request->query->all()
                ),
                'links' => array(
                    "first" => $paginator->url(1),
                    "last" => $paginator->url($paginator->lastPage()),
                    "self" => $paginator->url($paginator->currentPage()),
                    "prev" => $paginator->previousPageUrl(),
                    "next" => $paginator->nextPageUrl()
                ),
                'message' => $this->_success,
                'httpCode' => 200
            ],
            200,
            $this->getHeaders(),
            JSON_PRETTY_PRINT
        );
    }

    /**
     * @param array $data
     * @return JsonResponse
     */
    public function jsonSingle(array $data): JsonResponse
    {
        return response()->json(
            [
                'data' => $data,
                'message' => $this->_success,
                'httpCode' => 200
            ],
            200,
            $this->getHeaders(),
            JSON_PRETTY_PRINT
        );
    }

    /**
     * @param $elastic
     * @return array
     */
    public function parseData($elastic): array
    {
        $data = array();
        foreach ($elastic['hits']['hits'] as $object) {
            $data[] = $this->enrich('http:', 'https:', $object['_source']);
        }
        return $data;
    }

    /**
     * @param $elastic
     * @return array
     */
    public function parseIdData($elastic): array
    {
        $data = array();
        foreach ($elastic['hits']['hits'] as $object) {
            foreach($object['_source'] as $key => $value){
                $data[] = $value['id'];
            }
        }
        return $data;
    }

    /**
     * @param $elastic
     * @return array
     */
    public function parseIiifIdData($elastic): array
    {
        $data = array();
        foreach ($elastic['hits']['hits'] as $object) {
            foreach($object['_source'] as $key => $value){
                $data[] = $value['id'];
            }
        }
        return $data;
    }
    /**
     * @param $find
     * @param $replace
     * @param $array
     * @return mixed
     */
    public function enrich($find, $replace, &$array): mixed
    {
        if ($this->multiKeyExists($array, 'summary')) {
            $array['summary'] = $this->enrichSummary($array['summary']);
        }
        if ($this->multiKeyExists($array, 'component')) {
            $array['component'] = $this->enrichComponents($array['component']);
        }
        if ($this->multiKeyExists($array, 'multimedia')) {
            $array['multimedia'] = $this->enrichAndLinkImages($array['multimedia']);
        }
        if ($this->multiKeyExists($array, 'admin')) {
            $array['apiURI'] = route('api.objects.show', $array['admin']['id']);
            $array['URI'] = route('record', str_replace('object-', '', $array['admin']['id']));
            $array['object'] = $array['admin']['id'];
        }
        if ($this->multiKeyExists($array, 'owners')) {
            $array['owners'] = $this->enrichOwners($array['owners']);
        }

        if ($this->multiKeyExists($array, 'techniques')) {
            $array['techniques'] = $this->enrichTechniques($array['techniques']);
        }
        if ($this->multiKeyExists($array, 'institutions')) {
            $array['institutions'] = $this->enrichInstitutions($array['institutions']);
        }

        if ($this->multiKeyExists($array, 'school_or_style')) {
            $array['school_or_style'] = $this->enrichSchool($array['school_or_style']);
        }

        if ($this->multiKeyExists($array, 'categories')) {
            $array['categories'] = $this->enrichCategories($array['categories']);
        }
        if ($this->multiKeyExists($array, 'name')) {
           if(array_key_exists('name', $array)) {
               $array['name'] = $this->enrichNames($array['name']);
           }
        }
        if ($this->multiKeyExists($array, 'publications')) {
            $array['publications'] = $this->enrichPublications($array['publications']);
        }
        if ($this->multiKeyExists($array, 'lifecycle')) {
            $array['lifecycle'] = $this->enrichLifeCycle($array['lifecycle']);
        }
        if (env('APP_ENV') == 'production') {
            array_walk_recursive($array, function (&$array) use ($find, $replace) {
                $array = str_replace($find, $replace, $array);
            });
        }
        if(array_key_exists('multimedia', $array)) {
            if ($this->multiKeyExists($array['multimedia'], 'zoom')) {
                $array['manifestURI'] = $this->generateManifestURI($array['admin']['id']);
            }
        }
        return $array;
    }

    private function generateManifestURI(string $id): string
    {
        return env('FITZ_MANIFEST_URL') . $id . '/manifest';
    }

    /**
     * @param array $array
     * @param $key
     * @return bool
     */
    public function multiKeyExists(array $array, $key): bool
    {
        if (array_key_exists($key, $array)) {
            return true;
        }
        foreach ($array as $element) {
            if (is_array($element)) {
                if ($this->multiKeyExists($element, $key)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param array $summary
     * @return array
     */
    public function enrichSummary(array $summary): array
    {
        $enrichSummary = array();
        foreach ($summary as $summa) {
            $enrichSummary[] = array(
                'term' => $summa['admin']['id'],
                'URI' => $this->getWebURI('terminology', $summa['admin']['id']),
                'apiURI' => $this->getTermURI('api.terminology.show', $summa['admin']['id']),
                'summary_title' => $summa['summary_title'],
            );
        }
        return $enrichSummary;
    }

//    /**
//     * @param $array
//     * @param $remove
//     * @return mixed
//     */
//    public function array_unset_recursive(&$array, $remove): mixed
//    {
//        foreach ($array as $key => &$value) {
//            if (is_array($value)) {
//                $arraySize = $this->array_unset_recursive($value, $remove);
//                if (!$arraySize) {
//                    unset($array[$key]);
//                }
//            } else if (in_array($key, $remove, true)) {
//                unset($array[$key]);
//            }
//        }
//        return $array;
//    }

    /**
     * @param string $route
     * @param $id
     * @return string
     */
    public function getWebURI(string $route, $id): string
    {
        return route($route, [$id]);
    }

    /**
     * @param string $route
     * @param string $id
     * @return string
     */
    public function getTermURI(string $route, string $id): string
    {
        return route($route, [$id]);
    }

    public function enrichComponents(array $components): array
    {
        $enrichComponents = array();
        foreach ($components as $component) {
            $materials = array();
            if (array_key_exists('materials', $component)) {
                foreach ($component['materials'] as $material) {
                    $materials[] = array(
                        'term' => $material['reference']['admin']['id'],
                        'URI' => $this->getWebURI('terminology', $material['reference']['admin']['id']),
                        'apiURI' => $this->getTermURI('api.terminology.show', $material['reference']['admin']['id']),
                        'summary_title' => $material['reference']['summary_title']
                    );
                }
            }
            $enrichComponents[] = array(
                'name' => $component['name'],
                'materials' => $materials
            );
        }
        return $enrichComponents;
    }

    /**
     * @param $multimedia
     * @return array
     */
    public function enrichAndLinkImages($multimedia): array
    {
        $enrichedImages = array();
        foreach ($multimedia as $image) {
            $enrichedImages[] = $this->append_single_iip_url($image);
        }
        return $enrichedImages;
    }

    /**
     * @param array $array
     * @return array
     */
    public function append_single_iip_url(array $array): array
    {
        $images = array();
        $sizes = array();
        if(array_key_exists('processed', $array)) {
            $sizes = array_keys($array['processed']);
            foreach ($array['processed'] as &$surrogate) {
                if (array_key_exists('format', $surrogate)) {
                    if ($surrogate['format'] === 'pyramid tiff') {
                        $surrogate['location'] = env('FITZ_IIP_URL') . '/' . $surrogate['location'] . '&cvt=jpeg';
                    } else {
                        $surrogate['location'] = env('FITZ_IMAGE_URL') . $surrogate['location'];
                    }

                }
                $images[] = $surrogate;
            }
        }
        return array_combine($sizes, $images);
    }

    /**
     * @param $data
     * @return array
     */
    public function enrichOwners($data): array
    {
        $owners = array();
        foreach ($data as $datum) {
            $owners[] = array(
                'URI' => $this->getWebURI('agent', $datum['admin']['id']),
                'apiURI' => $this->getTermURI('api.agents.show', $datum['admin']['id']),
                'owner' => $datum['admin']['id'],
                'summary_title' => $datum['summary_title']
            );
        }
        return $owners;
    }

    /**
     * @param array $techniques
     * @return array
     */
    public function enrichTechniques(array $techniques): array
    {
        $technical = array();
        foreach ($techniques as $technique) {
            $descriptions = array();
            if (array_key_exists('description', $technique)) {
                foreach ($technique['description'] as $description) {
                    $descriptions[] = array('description' => $description['value']);
                }
            }
            $technical[] = array(
                'term' => $technique['reference']['admin']['id'],
                'URI' => $this->getWebURI('terminology', $technique['reference']['admin']['id']),
                'apiURI' => $this->getTermURI('api.terminology.show', $technique['reference']['admin']['id']),
                'summary_title' => $technique['reference']['summary_title'],
                'descriptions' => $descriptions,
            );
        }
        return $technical;
    }

    /**
     * @param array $institutions
     * @return array
     */
    public function enrichInstitutions(array $institutions): array
    {
        $institutionEnriched = array();
        foreach ($institutions as $institution) {
            $institutionEnriched[] = array(
                'term' => $institution['admin']['id'],
                'URI' => $this->getWebURI('agent', $institution['admin']['id']),
                'apiURI' => $this->getTermURI('api.agents.show', $institution['admin']['id']),
                'summary_title' => $institution['summary_title'],
            );
        }
        return $institutionEnriched;
    }

    /**
     * @param array $school_or_style
     * @return array
     */
    public function enrichSchool(array $school_or_style): array
    {
        $schools = array();
        foreach ($school_or_style as $school) {
            $schools[] = array(
                'term' => $school['admin']['id'],
                'URI' => $this->getWebURI('terminology', $school['admin']['id']),
                'apiURI' => $this->getTermURI('api.terminology.show', $school['admin']['id']),
                'summary_title' => $school['summary_title'],
            );
        }
        return $schools;
    }

    /**
     * @param array $categories
     * @return array
     */
    public function enrichCategories(array $categories): array
    {
        $enrichCategories = array();
        foreach ($categories as $category) {
            $enrichCategories[] = array(
                'term' => $category['admin']['id'],
                'URI' => $this->getWebURI('terminology', $category['admin']['id']),
                'apiURI' => $this->getTermURI('api.terminology.show', $category['admin']['id']),
                'summary_title' => $category['summary_title'],
            );
        }
        return $enrichCategories;
    }

    /**
     * @param array $names
     * @return array
     */
    public function enrichNames(array $names): array
    {
        $enrichNames = array();
        if(is_array($names)) {
            foreach ($names as $name) {
                if (array_key_exists('reference', $name)) {
                    $enrichNames[] = array(
                        'term' => $name['reference']['admin']['id'] ?? $name['admin']['id'],
                        'URI' => $this->getWebURI('terminology', $name['reference']['admin']['id'] ?? $name['admin']['id']),
                        'apiURI' => $this->getTermURI('api.terminology.show', $name['reference']['admin']['id'] ?? $name['admin']['id']),
                        'summary_title' => $name['reference']['summary_title'] ?? $name['summary_title'],
                    );
                } elseif (array_key_exists('value', $name)) {
                    $enrichNames[] = array(
                        'value' => $name['value'],
                    );
                }
            }
        }
        return $enrichNames;
    }

    /**
     * @param array $publications
     * @return array
     */
    public function enrichPublications(array $publications): array
    {
        $enrichPublications = array();
        foreach ($publications as $publication) {
            $publicationEnriched = array(
                'term' => $publication['admin']['id'],
                'URI' => $this->getWebURI('publication.record', $publication['admin']['id']),
                'apiURI' => $this->getTermURI('api.publications.show', $publication['admin']['id']),
                'summary_title' => $publication['summary_title']
            );
            if(array_key_exists('@link', $publication)) {
                if (array_key_exists('page', $publication['@link'])) {
                    $publicationEnriched['page'] = $publication['@link']['page'];
                }
                if (array_key_exists('notes', $publication['@link'])) {
                    $publicationEnriched['notes'] = $publication['@link']['notes'];
                }
            }
            $enrichPublications[] = $publicationEnriched;
        }
        return $enrichPublications;
    }

    /**
     * @param array $lifecycle
     * @return array[]
     */
    public function enrichLifeCycle(array $lifecycle): array
    {
        $lifeCycleData = array();
        if (array_key_exists('acquisition', $lifecycle)) {
            $lifeCycleData['acquisition'] = array();
            foreach ($lifecycle['acquisition'] as $acquisition) {
                if(array_key_exists('method',$acquisition)) {
                    $lifeCycleData['acquisition']['method'] = $acquisition['method']['value'];
                }
                if (array_key_exists('date', $acquisition)) {
                    $lifeCycleData['acquisition']['date'] = $acquisition['date'];
                }
                if (array_key_exists('agents', $acquisition)) {
                    $agents = array();
                    foreach ($acquisition['agents'] as $agent) {
                        $agents['agent'] = $this->enrichAgent($agent);
                    }
                    $lifeCycleData['acquisition']['agents'] = $agents;
                }
            }
        }
        if (array_key_exists('collection', $lifecycle)) {
            $lifeCycleData['collection'] = array();
            $places = array();
            foreach ($lifecycle['collection'] as $collection) {
                if (array_key_exists('places', $collection)) {
                    foreach ($collection['places'] as $place) {
                        $transformed = array(
                            'summary_title' => $place['summary_title'],
                            'apiURI' => $this->getTermURI('api.places.show', $place['admin']['id']),
                            'URI' => $this->getWebURI('terminology', $place['admin']['id'])
                        );
                        $coordinates = $this->getPlaceData($place['summary_title']);
                        if(!empty($coordinates)) {
                            $geo = array (
                                'lat' => $coordinates['lat'],
                                'lng' => $coordinates['lng'],
                            );
                            $transformed['coordinates'] = $geo;
                        }
                        $places[] = $transformed;

                    }
                }
            }
            $lifeCycleData['collection']['places'] = $places;
        }
        if (array_key_exists('creation', $lifecycle)) {
            $lifeCycleData['creation'] = array();
            foreach ($lifecycle['creation'] as $creation) {
                if (array_key_exists('method', $creation)) {
                    $lifeCycleData['creation']['method'] = $creation['method']['value'];
                }
                if (array_key_exists('date', $creation)) {
                    $lifeCycleData['creation']['date'] = $creation['date'];
                }

                if (array_key_exists('periods', $creation)) {
                    foreach ($creation['periods'] as $period) {
                        $lifeCycleData['creation']['periods'][] = array(
                            'summary_title' => $period['summary_title'],
                            'period' => $period['admin']['id'],
                            'apiURI' => $this->getTermURI('api.periods.show', $period['admin']['id']),
                            'URI' => $this->getWebURI('terminology', $period['admin']['id'])

                        );
                    }
                }
                if (array_key_exists('maker', $creation)) {
                    $makers = array();
                    foreach ($creation['maker'] as $maker) {
                        $makers['maker'] = $this->enrichAgents($maker);
                    }
                    $lifeCycleData['creation']['maker'] = $makers;
                }
                if(array_key_exists('places',$creation)){
                    $places = array();
                    foreach ($creation['places'] as $place) {
                        $transformed = array(
                            'summary_title' => $place['summary_title'],
                            'apiURI' => $this->getTermURI('api.places.show', $place['admin']['id']),
                            'URI' => $this->getWebURI('terminology', $place['admin']['id']),
                            'place' => $place['admin']['id']
                        );
                        $coordinates = $this->getPlaceData($place['summary_title']);
                        if(!empty($coordinates)) {
                            $geo = array (
                                'lat' => $coordinates['lat'],
                                'lng' => $coordinates['lng'],
                            );
                            $transformed['coordinates'] = $geo;
                        }
                        if(array_key_exists('note',$place)) {
                            foreach ($place['note'] as $note) {
                                $transformed['note'] = $note['value'];
                            }
                        }
                        if(array_key_exists('hierarchies',$place)) {
                            $hierarchies = array();
                            foreach ($place['hierarchies'] as $hierarchGeo) {
                                foreach ($hierarchGeo as $hierarchy) {
                                    $hierarchies[] = array(
                                        'summary_title' => $hierarchy['summary_title'],
                                        'type' => $hierarchy['type'],
                                    );
                                }
                            }
                            $transformed['hierarchies'] = $hierarchies;
                        }
                        $places[] = $transformed;
                    }
                    $lifeCycleData['creation']['places'] = $places;
                }
            }
        }
        return $lifeCycleData;
    }

    /**
     * @param array $data
     * @return array
     */
    public function enrichAgent(array $data): array
    {
        $data['URI'] = $this->getWebURI('agent', $data['admin']['id']);
        $data['apiURI'] = $this->getTermURI('api.agents.show', $data['admin']['id']);
        $data['agent'] = $data['admin']['id'];
        unset($data['admin']);
        unset($data['@link']);
        return $data;
    }

    public function enrichAgents(array $data): array
    {
        $data['URI'] = $this->getWebURI('terminology', $data['admin']['id']);
        $data['apiURI'] = $this->getTermURI('api.agents.show', $data['admin']['id']);
        $data['term'] = $data['admin']['id'];
        if (array_key_exists('@link', $data)) {
            if (array_key_exists('role', $data['@link'])) {
                $data['role'] = $data['@link']['role'];
            }
            unset($data['@link']);
        }

        unset($data['admin']);
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public function enrichTerms(array $data): array
    {
        $data['URI'] = $this->getWebURI('terminology', $data['admin']['id']);
        $data['apiURI'] = $this->getTermURI('api.terminology.show', $data['admin']['id']);
        $data['term'] = $data['admin']['id'];
        if (array_key_exists('@link', $data)) {
            if (array_key_exists('role', $data['@link'])) {
                $data['role'] = $data['@link']['role'];
            }
            unset($data['@link']);
        }
        if (array_key_exists('parent', $data)) {
            foreach ($data['parent'] as &$parent) {
                $parent['URI'] = route('terminology', $parent['admin']['id']);
                $parent['apiURI'] = route('api.terminology.show', $parent['admin']['id']);
                $parent['period'] = $parent['admin']['id'];
                unset($parent['@link']);
                unset($parent['admin']['uuid']);
                unset($parent['admin']['uid']);
            }
        }
        if (array_key_exists('equivalent', $data)) {
            foreach ($data['equivalent'] as &$equivalent) {
                $equivalent['URI'] = route('terminology', $equivalent['admin']['id']);
                $equivalent['apiURI'] = route('api.terminology.show', $equivalent['admin']['id']);
                $equivalent['period'] = $equivalent['admin']['id'];
                unset($equivalent['@link']);
                unset($equivalent['admin']['uuid']);
                unset($equivalent['admin']['uid']);
            }
        }
        if (array_key_exists('related', $data)) {
            foreach ($data['related'] as &$related) {
                $related['URI'] = route('terminology', $related['admin']['id']);
                $related['apiURI'] = route('api.terminology.show', $related['admin']['id']);
                $related['period'] = $related['admin']['id'];
                unset($related['@link']);
                unset($related['admin']['uuid']);
                unset($related['admin']['uid']);
            }
        }

        unset($data['admin']['uuid']);
        unset($data['admin']['uid']);
        return $data;
    }

    /**
     * @param $elastic
     * @return array
     */
    public function parse($elastic): array
    {
        $data = array();
        foreach ($elastic['hits']['hits'] as $object) {
            $data[] = $object['_source'];
        }
        return $data;
    }

    public function parsePublicationsData($elastic): array
    {
        $data = array();
        foreach ($elastic['hits']['hits'] as $object) {
            $data[] = $object['_source'];
        }
        return $data;
    }

//    /**
//     * @param $find
//     * @param $replace
//     * @param $array
//     * @return mixed
//     */
//    public function enrichImages($find, $replace, &$array): mixed
//    {
//        if (array_key_exists('processed', $array)) {
//            $array['processed'] = $this->append_image_path($array['processed']);
//        }
//
//        if ($this->multiKeyExists($array, 'zoom')) {
//            $array['manifestURI'] = env('FITZ_MANIFEST_URL') . $array['admin']['id'] . '/manifest';
//            $array['processed'] = $this->append_single_iip_url($array);
//        }
//        array_walk_recursive($array, function (&$array) use ($find, $replace) {
//            $array = str_replace($find, $replace, $array);
//        });
//        return $array;
//    }

//    /**
//     * @param $array
//     * @return array
//     */
//    public function append_image_path($array): array
//    {
//        array_walk_recursive($array, function (&$value, $key) {
//            if (!is_array($value) && $key === 'location') {
//                $value = env('FITZ_IMAGE_URL') . $value;
//            }
//        });
//        return $array;
//    }

    /**
     * @param array $elastic
     * @return array
     */
    public function parseTerminologyAggPlace(array $elastic): array
    {

        $data = array();
        foreach ($elastic['aggregations']['places']['buckets'] as $place) {
            $labels = array();
            $title = '';
            foreach ($place['place']['hits']['hits'][0]['_source']['lifecycle']['creation'] as $creation) {
                foreach ($creation['places'] as $label) {
                    if ($label['admin']['id'] != $place['key']) {
                        $labels[] = array(
                            'summary_title' => $label['summary_title'],
                            'id' => $label['admin']['id'],
                            'URI' => route('terminology', [$place['key']]),
                            'apiURI' => route('api.places.show', $place['key']),
                            'notes' => 'Often used alongside the parent place'
                        );
                    } else {
                        $title = $label['summary_title'];
                    }
                }
            }
            $info = array(
                'maker' => $place['key'],
                'URI' => route('terminology', [$place['key']]),
                'apiURI' => route('api.places.show', $place['key']),
                'records' => $place['doc_count'],
                'summary_title' => $title,
            );
            if (!empty($labels)) {
                $info['associatedPlaces'] = $labels;
            }
            $data[] = $info;
        }
        return $data;
    }

    /**
     * @param array $elastic
     * @return array
     */
    public function parseTerminologyAggPeriods(array $elastic): array
    {
        $data = array();
        foreach ($elastic['aggregations']['records']['buckets'] as $period) {
            $labels = array();
            $title = '';
            foreach ($period['period']['hits']['hits'][0]['_source']['lifecycle']['creation'] as $creation) {
                foreach ($creation['periods'] as $label) {

                    if ($label['admin']['id'] != $period['key']) {

                        $labels[] = array(
                            'summary_title' => $label['summary_title'],
                            'id' => $label['admin']['id'],
                            'URI' => route('terminology', $label['admin']['id']),
                            'apiURI' => route('api.periods.show', $label['admin']['id']),
                            'notes' => 'Often used alongside the parent period'
                        );
                    } else {
                        $title = $label['summary_title'];
                    }
                }
            }
            $info = array(
                'period' => $period['key'],
                'URI' => route('terminology', [$period['key']]),
                'apiURI' => route('api.periods.show', $period['key']),
                'records' => $period['doc_count'],
                'summary_title' => $title,
            );
            if (!empty($labels)) {
                $info['associatedPeriods'] = $labels;
            }
            $data[] = $info;
        }
        return $data;
    }


    /**
     * @param array $elastic
     * @return array
     */
    public function parseTerminologyAggPeriodsID(array $elastic): array
    {
        $labels = array();
        foreach ($elastic['aggregations']['records']['buckets'] as $period) {
            foreach ($period['period']['hits']['hits'][0]['_source']['lifecycle']['creation'] as $creation) {
                foreach ($creation['periods'] as $label) {
                    $labels[] = $label['admin']['id'];
                }
            }
        }
        return $labels;
    }
    /**
     * @param array $elastic
     * @return array
     */
    public function parseTerminologyAggMakers(array $elastic): array
    {
        $data = array();
        foreach ($elastic['aggregations']['records']['buckets'] as $maker) {
            $labels = array();
            $title = '';
            foreach ($maker['maker']['hits']['hits'][0]['_source']['lifecycle']['creation'] as $creation) {
                foreach ($creation['maker'] as $label) {

                    if ($label['admin']['id'] != $maker['key']) {
                        $labels[] = array(
                            'summary_title' => $label['summary_title'],
                            'id' => $label['admin']['id'],
                            'URI' => route('agent', [$label['admin']['id']]),
                            'apiURI' => route('api.makers.show', $label['admin']['id']),
                            'notes' => 'An agent sometimes used alongside the parent maker'
                        );
                    } else {
                        $title = $label['summary_title'];
                    }
                }
            }
            $info = array(
                'maker' => $maker['key'],
                'URI' => route('agent', [$maker['key']]),
                'apiURI' => route('api.makers.show', $maker['key']),
                'records' => $maker['doc_count'],
                'summary_title' => $title,
            );
            if (!empty($labels)) {
                $info['associatedMakers'] = $labels;
            }
            $data[] = $info;
        }
        return $data;
    }


    /**
     * @param array $elastic
     * @return array
     */
    public function parseTerminologyAggMakersID(array $elastic): array
    {
        $labels = array();
        foreach ($elastic['aggregations']['records']['buckets'] as $maker) {
            foreach ($maker['maker']['hits']['hits'][0]['_source']['lifecycle']['creation'] as $creation) {
                foreach ($creation['maker'] as $label) {
                    $labels[] = $label['admin']['id'];
                }
            }
        }
        return $labels;
    }

    /**
     * @param array $elastic
     * @return array
     */
    public function parseTerminologyAggPlacesID(array $elastic): array
    {
        $labels = array();
        foreach ($elastic['aggregations']['places']['buckets'] as $place) {
            foreach ($place['place']['hits']['hits'][0]['_source']['lifecycle']['creation'] as $creation) {
                foreach ($creation['places'] as $label) {
                    $labels[] = $label['admin']['id'];
                }
            }
        }
        return $labels;
    }
    /**
     * @param array $elastic
     * @return array
     */
    public function parseAgents(array $elastic): array
    {
        $data = array();
        foreach ($elastic as $agent) {
            $agent = $this->utf8_converter($agent);
            $data[] = array(
                'agent' => $agent['admin']['id'],
                'URI' => $this->getWebURI('agent', $agent['admin']['id']),
                'apiURI' => $this->getTermURI('api.agents.show', $agent['admin']['id']),
                'summary_title' => $agent['summary_title'],
                'name' => $agent['name'] ?? 'No name',
            );
        }
        return $data;
    }

//    /**
//     * @param array $elastic
//     * @return array
//     */
//    public function parsePublications(array $elastic): array
//    {
//        $data = array();
//        foreach ($elastic as $agent) {
//            $data[] = array(
//                'publication' => $agent['admin']['id'],
//                'URI' => $this->getWebURI('publication.record', $agent['admin']['id']),
//                'apiURI' => $this->getTermURI('api.publications.show', $agent['admin']['id']),
//                'summary_title' => $agent['summary_title'],
//                'admin' => $agent['admin'],
//                'lifecycle' => $agent['lifecycle'] ?? [],
//                'title' => $agent['title'],
//            );
//        }
//        return $data;
//    }

    /**
     * @param array $elastic
     * @return array
     */
    public function parseExhibitions(array $elastic): array
    {
        $data = array();
        foreach ($elastic as $exhibition) {
            if (array_key_exists('venues', $exhibition)) {
                $venues = $exhibition['venues'];
                $venues = $this->enrichVenues($venues);
            } else {
                $venues = array();
            }
            $data[] = array(
                'exhibition' => $exhibition['admin']['id'],
                'URI' => $this->getWebURI('exhibition.record', $exhibition['admin']['id']),
                'apiURI' => $this->getTermURI('api.exhibitions.show', $exhibition['admin']['id']),
                'summary_title' => $exhibition['summary_title'],
                'admin' => $exhibition['admin'],
                'venues' => $venues,
            );

        }
        return $data;
    }

    /**
     * Enrich Venue Data for API response
     * @param array $elastic
     * @return array
     */
    public function enrichVenues(array $elastic): array
    {
        $venues = array();
        foreach ($elastic as $data) {
            $data['URI'] = $this->getWebURI('terminology', $data['admin']['id']);
            $data['apiURI'] = $this->getTermURI('api.places.show', $data['admin']['id']);
            $data['venue'] = $data['admin']['id'];
            $data['dates'] = $data['@link'];
            unset($data['admin']['uid']);
            unset($data['admin']['uuid']);
            unset($data['@link']);
            $venues[] = $data;
        }
        return $venues;
    }

    /**
     * Parse terminology response to array
     * @param array $elastic
     * @return array
     */
    public function parseExhibition(array $elastic): array
    {
        return $this->extracted($elastic);
    }

    /**
     * @param array $elastic
     * @return array
     */
    public function extracted(array $elastic): array
    {
        $exhibitions = array();
        foreach ($elastic as $exhibition) {
            $exhibitions[] = array(
                'term' => $exhibition['admin']['id'],
                'URI' => $this->getWebURI('terminology', $exhibition['admin']['id']),
                'apiURI' => $this->getTermURI('api.terminology.show', $exhibition['admin']['id']),
                'summary_title' => $exhibition['summary_title'],
            );

        }
        return $exhibitions;
    }

    public function parseTerms(array $elastic): array
    {
        return $this->extracted($elastic);
    }

    /**
     * Parse terminology response to array
     * @param array $elastic
     * @return array
     */
    public function parseMakers(array $elastic): array
    {
        $data = array();
        foreach ($elastic as $exhibition) {
            $data[] = array(
                'term' => $exhibition['admin']['id'],
                'URI' => $this->getWebURI('terminology', $exhibition['admin']['id']),
                'apiURI' => $this->getTermURI('api.makers.show', $exhibition['admin']['id']),
                'summary_title' => $exhibition['summary_title'],
                'admin' => $exhibition['admin'],
            );

        }
        return $data;
    }

    /**
     * Enrich place data for api response
     * @param array $data
     * @return array
     */
    public function enrichPlace(array $data): array
    {
        $coordinates = $this->getPlaceData($data['summary_title']);
        if(!empty($coordinates)) {
            $data['coordinates'] = array (
                'lat' => $coordinates['lat'],
                'lng' => $coordinates['lng'],
            );
        }
        $data['URI'] = $this->getWebURI('terminology', $data['admin']['id']);
        $data['apiURI'] = $this->getTermURI('api.places.show', $data['admin']['id']);
        $data['place'] = $data['admin']['id'];
        unset($data['@link']);
        if (array_key_exists('parent', $data)) {
            foreach ($data['parent'] as &$parent) {
                $coordinatesParent = $this->getPlaceData($parent['summary_title']);
                if(!empty($coordinatesParent)) {
                    $parent['coordinates'] = array (
                        'lat' => $coordinatesParent['lat'],
                        'lng' => $coordinatesParent['lng'],
                    );
                }
                $parent['URI'] = route('terminology', $parent['admin']['id']);
                $parent['appURI'] = route('api.places.show', $parent['admin']['id']);
                $parent['id'] = $parent['admin']['id'];
                unset($parent['@link']);

            }
        }
        if (array_key_exists('related', $data)) {
            foreach ($data['related'] as &$related) {
                $coordinatesRelated = $this->getPlaceData($related['summary_title']);
                if(!empty($coordinatesRelated)) {
                    $related['coordinates'] = array (
                        'lat' => $coordinatesRelated['lat'],
                        'lng' => $coordinatesRelated['lng'],
                    );
                }
                $related['URI'] = route('terminology', $related['admin']['id']);
                $related['appURI'] = route('api.places.show', $related['admin']['id']);
                $related['id'] = $related['admin']['id'];
                unset($related['@link']);

            }
        }
        return $data;
    }

    /**
     * @param string $placeName
     * @return string
     */
    public function checkPlaceName(string $placeName): string
    {
        $place = AxiellPlaceNames::find($placeName);
        if(!empty($place)) {
            return $place['geocode_name'];
        }
        return $placeName;
    }
    /**
     * @param string $placeName
     * @return array
     * @throws \Geocoder\Exception\Exception
     */
    public function getPlaceData(string $placeName): array
    {
        $key = md5($placeName);
        $placeName = $this->checkPlaceName($placeName);
        $expiresAt = now()->addDays(60);
        if (Cache::has($key)) {
            $gd = Cache::get($key);
        } else {
            $geo = new LookupPlace();
            $geo->setPlace($placeName);
            $gd = $geo->lookup();
            Cache::put($key, $gd, $expiresAt);
        }
        if(!$gd->isEmpty()){
            $geoData = $gd->first()->getCoordinates();
            $lat = $geoData->getLatitude();
            $lon = $geoData->getLongitude();
            return array('lat' => $lat, 'lng' => $lon);
        } else {
            return [];
        }
    }

    public function enrichMaker(array $data): array
    {
        $data['URI'] = $this->getWebURI('terminology', $data['admin']['id']);
        $data['apiURI'] = $this->getTermURI('api.makers.show', $data['admin']['id']);
        $data['maker'] = $data['admin']['id'];
        return $data;
    }

    /**
     * Enrich period data for API response
     * @param array $data
     * @return array
     */
    public function enrichPeriod(array $data): array
    {
        $data['URI'] = $this->getWebURI('terminology', $data['admin']['id']);
        $data['apiURI'] = $this->getTermURI('api.periods.show', $data['admin']['id']);
        $data['period'] = $data['admin']['id'];
        if (array_key_exists('parent', $data)) {
            foreach ($data['parent'] as &$parent) {
                $parent['URI'] = route('terminology', $parent['admin']['id']);
                $parent['apiURI'] = route('api.periods.show', $parent['admin']['id']);
                $parent['period'] = $parent['admin']['id'];
                unset($parent['@link']);
                unset($parent['admin']['uuid']);
                unset($parent['admin']['uid']);
            }
        }
        if (array_key_exists('equivalent', $data)) {
            foreach ($data['equivalent'] as &$equivalent) {
                $equivalent['URI'] = route('terminology', $equivalent['admin']['id']);
                $equivalent['apiURI'] = route('api.periods.show', $equivalent['admin']['id']);
                $equivalent['period'] = $equivalent['admin']['id'];
                unset($equivalent['@link']);
                unset($equivalent['admin']['uuid']);
                unset($equivalent['admin']['uid']);
            }
        }
        if (array_key_exists('related', $data)) {
            foreach ($data['related'] as &$related) {
                $related['URI'] = route('terminology', $related['admin']['id']);
                $related['apiURI'] = route('api.periods.show', $related['admin']['id']);
                $related['period'] = $related['admin']['id'];
                unset($related['@link']);
                unset($related['admin']['uuid']);
                unset($related['admin']['uid']);
            }
        }
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public function enrichPublication(array $data): array
    {
        $data['URI'] = $this->getWebURI('publication.record', $data['admin']['id']);
        $data['apiURI'] = $this->getTermURI('api.publications.show', $data['admin']['id']);
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public function enrichExhibition(array $data): array
    {
        $data['URI'] = $this->getWebURI('terminology', $data['admin']['id']);
        $data['apiURI'] = $this->getTermURI('api.exhibitions.show', $data['admin']['id']);
        $data['exhibition'] = $data['admin']['id'];
        if (array_key_exists('venues', $data)) {
            $data['venues'] = $this->enrichVenues($data['venues']);
        }
        return $data;
    }

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
    public function getFrom(Request $request): int
    {
        if ($request->query('page') && $request->query('page') > 1) {
            return $request->query('page') * $this->getSize($request);
        } else {
            return 0;
        }
    }

    /**
     * @param Request $request
     * @return int|jsonResponse
     */
    public function getSize(Request $request): int|jsonResponse
    {
        $size = $request->query('size') ?? 20;
        if (!is_numeric($size)) {
            return $this->jsonError(400, 'Size must be an integer');
        }
        if ($size > 100) {
            return $this->jsonError(400, 'Size must be less than 100');
        }
        return $size;
    }

    /**
     * @param string $code
     * @param array|string $message
     * @return JsonResponse
     */
    public function jsonError(string $code, array|string $message): JsonResponse
    {
        if($this->isJson($message)) {
            $message = json_decode($message );
        }
        return response()->json(
            [
                'message' => $message,
                'httpCode' => $code
            ],
            $code,
            $this->getHeaders(),
            JSON_PRETTY_PRINT
        );
    }

    /**
     * @param $string
     * @return bool
     */
    public function isJson($string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
    /**
     * @param Request $request
     * @return array
     */
    public function getSort(Request $request): array
    {
        if (array_key_exists('sort', $request->query()) && array_key_exists('sort_field', $request->query())) {
            return array(
                $request->query('sort_field') => [
                    "order" => $request->query('sort')
                ]
            );
        } else {
            return array(
                "admin.modified" => [
                    "order" => $request->query('sort') ?? 'asc'
                ]
            );
        }
    }

    /**
     * @param Request $request
     * @param string $default
     * @return string
     */
    public function getFields(Request $request, string $default): string
    {
        $fields = $this->getQueryFields($request);
        if (!empty($fields)) {
            return $fields;
        } else {
            return $default;
        }
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



    /**
     * @param Request $request
     * @param array $params
     * @return array
     */
    public function createQuery(Request $request, array $params ): array
    {
        $query = $params;
        if(!is_null($request->query('q'))) {
            $query['body']['query']['bool']['must'][] = [
                "multi_match" => [
                    "fields" => "_generic_all_std",
                    "query" => Purifier::clean($request->query('query'), array('HTML.Allowed' => '')),
                    "operator" => "AND",
                ]
            ];
            return $query;
        } else {
            return $query;
        }

    }

    /**
     * @param $array
     * @return array
     */
    public function utf8_converter($array): array
    {
        array_walk_recursive($array, function (&$item, $key) {

            if (!mb_detect_encoding($item, 'utf-8', true)) {
                $item = utf8_encode($item);
                $item = str_replace('\u', 'u', $item);
                $item = preg_replace('@\x{FFFD}@u', 'Æ',($item));
                $item = preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', $item);
            } else {
                $item = str_replace('\u', 'u', $item);
                $item = preg_replace('@\x{FFFD}@u', 'Æ',($item));
                $item = preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', $item);
            }
        });
        return $array;
    }



}
