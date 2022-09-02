<?php

namespace App\Http\Controllers;

use App\CollectionXML;
use App\LinkedArt\Image;
use ColorThief\ColorThief;
use DOMException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use PHPExif\Exif;
use PHPExif\Reader\Reader;
use App\Models\Images;

class imagesController extends Controller
{
    /**
     * @param Request $request
     * @param string $priref
     * @return View
     */
    public function images(Request $request, string $priref): View
    {
        $ciim = $this->getElastic()->setParams([
            'index' => 'ciim',
            'size' => 1,
            'body' => [
                'query' => [
                    'match' => [
                        'identifier.priref' => $priref
                    ]
                ]
            ]
        ])->getSearch();
        if (!empty($ciim['hits']['hits'])) {
            $data = Collect($ciim['hits']['hits'])->first()['_source'];
        } else {
            abort(404);
        }
        if (array_key_exists('multimedia', $data)) {
            $images = $data['multimedia'];
        } else {
            abort('404');
        }
        $paginate = $this->paginate($images);
        $paginate->setPath($request->getBaseUrl());
        return view('images.images', compact('paginate', 'data'));
    }

    /**
     * @param array|Collection $items
     * @param int $perPage
     * @param int|null $page
     * @param array $options
     * @return LengthAwarePaginator
     */
    public function paginate(array|Collection $items, int $perPage = 24, int|null $page = NULL, array $options = []): LengthAwarePaginator
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return Application|ResponseFactory|Factory|\Illuminate\Contracts\View\View|JsonResponse|Response|View
     * @throws DOMException
     */
    public function image(Request $request, string $id): Factory|\Illuminate\Contracts\View\View|Response|JsonResponse|View|Application|ResponseFactory
    {
        $data = Images::getImageData($id);
        $filtered = $this->filter_array($data, $id);
        $palette = $this->getPalette($this->getPath($data));
        $exif = $this->getExif($this->getPath($data));
        $object = Images::getObject($id);
        return $this->machineResponse($request, $filtered, $object, $palette, $exif);
    }

    /**
     * @param $array
     * @param $term
     * @return array
     */
    public function filter_array($array, $term): array
    {
        $matches = array();
        foreach ($array as $a) {
            if ($a['admin']['id'] == $term)
                $matches[] = $a;
        }
        return $matches;
    }

    /**
     * @param Request $request
     * @return View
     */
    public function slowiiif(Request $request): View
    {
        $object = Images::getIIIFData($request);
        return view('record.slow', compact('object'));
    }

    /**
     * @param string $id
     * @return View
     */
    public function iiif(string $id): View
    {
        $data = Images::getIIIF($id);
        $filtered = $this->filtered($data, $id);
        $palette = $this->getPalette($this->getPath($data));
        $exif = $this->getExif($this->getPath($data));
        $object = Images::getObject($id);
        return view('images.iiif', compact('filtered', 'object', 'palette', 'exif'));
    }

    /**
     * @param array $data
     * @param string $id
     * @return array
     */
    public function filtered(array $data, string $id): array
    {
        return $this->filter_array($data, $id);
    }

    /**
     * @param int $id
     * @return View
     */
    public function flutteriiif(int $id): View
    {
        $data = Images::getIIIFData(request());
        $filtered = $this->filtered($data, $id);
        $palette = $this->getPalette($this->getPath($data));
        $exif = $this->getExif($this->getPath($data));
        $object = Images::getObject($id);
        return view('images.iiif-flutter', compact('filtered', 'object', 'palette', 'exif'));
    }

    /**
     * @param string $id
     * @return View
     */
    public function sketchfab(string $id): View
    {
        $data = Images::getSketchFab($id);
        return view('images.3d', compact('data'));
    }

    /**
     * @param string $id
     * @return View
     */
    public function mirador(string $id): View
    {
        $object = Images::getMirador($id);
        return view('images.mirador', compact('object'));
    }

    /**
     * @param array $data
     * @return string
     */
    public function getPath(array $data): string
    {
        return env('CIIM_IMAGE_URL') . $data[0]['processed']['large']['location'];
    }

    /**
     * @param $path
     * @return array
     */
    public function getPalette($path): array
    {
        return ColorThief::getPalette($path, 12);
    }

    /**
     * @param $path
     * @return Exif
     */
    public function getExif($path): Exif
    {
        $reader = Reader::factory(Reader::TYPE_NATIVE);
        return $reader->read($path);
    }


    /**
     * @param Request $request
     * @param array $filtered
     * @param $object
     * @param $palette
     * @param $exif
     * @return \Illuminate\Contracts\View\View|Factory|Response|JsonResponse|Application|ResponseFactory
     * @throws DOMException
     */
    public function machineResponse(Request $request, array $filtered, $object, $palette, $exif ): View|Factory|Response|JsonResponse|Application|ResponseFactory
    {
        $formats = array(null, 'json', 'xml', 'html', 'linked-art');
        $validator = Validator::make(['format' => $request->get('format')], [
            "format" => "in:" . implode(",", $formats)
        ]);

        if ($validator->fails()) {
            abort(500, $validator->errors());
        }
        switch ($request) {
            case $request->get('format') == 'json' || $request->header('Accept') == 'application/json':
                return response()->json(array($object, $palette, $exif));
            case $request->get('format') == 'linked-art' || $request->header('Accept') === 'application/ld+json;profile=\"https://linked.art/ns/v1/linked-art.json\"':
                return response()->json(Image::createLinkedArtImage(Collect($object)->toArray()));
            case $request->get('format') == 'xml' || $request->header('Accept') == 'application/xml':
                $xml = CollectionXML::createXML($object);
                return response($xml, 200)->header('Content-Type', 'application/xml');
            case $request->get('format') == 'html' || $request->header('Accept') == 'text/html':
            default:
            return view('images.image', compact('filtered', 'object', 'palette', 'exif'));

        }
    }
}
