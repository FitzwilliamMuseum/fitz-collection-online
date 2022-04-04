<?php

namespace App\Http\Controllers;

use ColorThief\ColorThief;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Nette\Utils\Image;
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
        $data = $this->getElastic()->setParams([
            'index' => 'ciim',
            'size' => 1,
            'body' => [
                'query' => [
                    'match' => [
                        'identifier.priref' => $priref
                    ]
                ]
            ]
        ])->getSearch()['hits']['hits'];
        if (array_key_exists('multimedia', $data[0]['_source'])) {
            $images = $data[0]['_source']['multimedia'];
        } else {
            abort('404');
        }
        $paginate = $this->paginate($images);
        $paginate->setPath($request->getBaseUrl());
        return view('record.images', compact('paginate', 'data'));
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
     * @param string $id
     * @return View
     */
    public function image(string $id): View
    {
        $data      = Images::getImageData($id);
        $filtered  = $this->filter_array($data, $id);
        $palette   = $this->getPalette($this->getPath($data));
        $exif      = $this->getExif($this->getPath($data));
        $object    = Images::getObject($id);
        return view('record.image', compact('filtered', 'object', 'palette', 'exif'));
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
        $palette  = $this->getPalette($this->getPath($data));
        $exif     = $this->getExif($this->getPath($data));
        $object   = Images::getObject($id);
        return view('record.iiif', compact('filtered', 'object', 'palette', 'exif'));
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
        $data      = Images::getIIIFData(request());
        $filtered  = $this->filtered($data, $id);
        $palette   = $this->getPalette($this->getPath($data));
        $exif      = $this->getExif($this->getPath($data));
        $object    = Images::getObject($id);
        return view('record.iiif-flutter', compact('filtered', 'object', 'palette', 'exif'));
    }

    /**
     * @param string $id
     * @return View
     */
    public function sketchfab(string $id): View
    {
        $data = Images::getSketchFab($id);
        return view('record.3d', compact('data'));
    }

    /**
     * @param string $id
     * @return View
     */
    public function mirador(string $id): View
    {
        $object = Images::getMirador($id);
        return view('record.mirador', compact('object'));
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
}
