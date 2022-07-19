<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\IIIF;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 * path="/api/v1/iiif",
 * summary="Retrieve iiif used in the database",
 * description="A list of iiif used in the database, with pagination.",
 * tags={"Static Images and IIIF"},
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 *    description="Query",
 *    in="query",
 *    name="query",
 *    required=false,
 *    example="Titian",
 *    @OA\Schema(
 *       type="string",
 *     format="string"
 *    )
 * ),
 * @OA\Parameter(
 *    description="Digital data created after",
 *    in="query",
 *    name="created_after",
 *    required=false,
 *    @OA\Schema(
 *       type="string",
 *     nullable=true,
 *     format="Y-m-d",
 *     description="Format: YYYY-MM-DD",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Digital data created before",
 *    in="query",
 *    name="created_before",
 *    required=false,
 *    @OA\Schema(
 *       type="string",
 *     nullable=true,
 *     format="Y-m-d",
 *     description="Format: YYYY-MM-DD",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Digital data modified after",
 *    in="query",
 *    name="modified_after",
 *    required=false,
 *    @OA\Schema(
 *       type="string",
 *     nullable=true,
 *     format="Y-m-d",
 *     description="Format: YYYY-MM-DD",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Digital data modified before",
 *    in="query",
 *    name="modified_before",
 *    required=false,
 *    @OA\Schema(
 *       type="string",
 *     nullable=true,
 *     format="Y-m-d",
 *     description="Format: YYYY-MM-DD",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Page number",
 *    in="query",
 *    name="page",
 *    required=false,
 *    example="1",
 *    @OA\Schema(
 *       type="integer",
 *       format="int64"
 *    )
 * ),
 *  @OA\Parameter(
 *    description="Size of the page response",
 *    in="query",
 *    name="size",
 *    required=false,
 *    example="20",
 *    @OA\Schema(
 *       type="integer",
 *       format="int64"
 *    )
 * ),
 * @OA\Response(
 *    response=200,
 *    description="Success"
 *     ),
 * ),
 * @OA\Get(
 * path="/api/v1/iiif/{image}",
 * summary="Retrieve an image's data ",
 * description="An image's paths and associated metadata.",
 * tags={"Static Images and IIIF"},
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 *    description="Query",
 *    in="path",
 *    name="image",
 *    required=true,
 *    example="media-3049538914",
 *    @OA\Schema(
 *       type="string",
 *     format="string"
 *    )
 * ),
 * @OA\Response(
 *    response=200,
 *    description="Success"
 *     ),
 * )
 */
class IiifController extends ApiController
{

    public array $_params = array(
        'sort', 'size', 'page',
        'query', 'fields', 'sort_field',
        'created_after', 'created_before', 'modified_after',
        'modified_before',
    );

    /**
     * @var array
     */
    private array $_showFields = array('id', 'fields');

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "page" => "numeric|gt:0",
            "size" => "numeric|gte:0|lte:100",
            "query" => "string|min:3",
            "object" => "string|min:8|regex:'^object-\d+$'",
            'sort_field' => 'string|in:id,title,created,modified|min:2',
            'sort' => 'string|in:asc,desc|min:3',
            'created_before' => 'date|date_format:Y-m-d|after:created_after|after:modified_after',
            'created_after' => 'date|date_format:Y-m-d|before:created_before|before:modified_before',
            'modified_before' => 'date|date_format:Y-m-d|after:modified_after|after:created_after',
            'modified_after' => 'date|date_format:Y-m-d|before:modified_before|before:created_before',
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $response = IIIF::list($request);
        $data = $this->parse($response);
        if (empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $images = $this->insertType($this->enrichMultipleIIIF($data), 'IIIF images');
            $paginator = new LengthAwarePaginator(
                $images,
                $response['hits']['total']['value'],
                $request->get('size', 20),
                LengthAwarePaginator::resolveCurrentPage()
            );
            $paginator->setPath(route('api.images.index'));
            return $this->jsonGenerate($request, $paginator, $paginator->total());
        }
    }

    /**
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make(array_merge($request->all(), array('id' => $id)), [
            "*" => "in:" . implode(',', $this->_showFields),
            'id' => "string|min:7|regex:'^media-\d+$'"
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $data = IIIF::show($request, $id);
        if (empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            return $this->jsonSingle($this->insertSingleType($this->enrichIIIFSingle($data), 'IIIF images'));
        }
    }

    public function enrichIIIFSingle(array $data): array
    {
        $data['apiURI'] = route('api.iiif.show', $data['admin']['id']);

                $id = $data['admin']['id'];
                $data['manifestURI'] = env('FITZ_MANIFEST_URL') . $id . '/manifest';
                $data['uvViewerPath'] = env('FITZ_UV_VIEWER_PATH') . env('FITZ_MANIFEST_URL') . $id . '/manifest&c=0&m=0&cv=0&config=&locales=en-GB:English (GB),cy-GB:Cymraeg,fr-FR:Français (FR),pl-PL:Polski,sv-SE:Svenska&r=0';
                $data['uvViewerEmbedHTML'] = '<iframe src="' . env('FITZ_UV_VIEWER_PATH') . env('FITZ_MANIFEST_URL') . $id . '/manifest&c=0&m=0&cv=0&config=&locales=en-GB:English (GB),cy-GB:Cymraeg,fr-FR:Français (FR),pl-PL:Polski,sv-SE:Svenska&r=0" width="560" height="420" allowfullscreen></iframe>';
                $data['miradorPath'] = route('image.mirador', [$id]);
                $files = $this->generateIiifFiles($id);
                foreach ($files as $key => $file) {
                    $data[$key] = $file;
                }

        if (array_key_exists('objects', $data)) {
            foreach ($data['objects'] as $record) {
                $record['apiURI'] = route('api.objects.show', [$record['admin']['id']]);
                $record['id'] = $record['admin']['id'];
                unset($record['summary']);
                unset($record['@link']);
                unset($record['admin']);
                $data['objects'] = $record;

            }
        }
        unset($data['multimedia']);
        unset($data['admin']);
        $data['admin']['id'] = $id;
        return $data;
    }

    /**
     * @param array $images
     * @return array
     */
    public function enrichMultipleIIIF(array $images): array
    {
        $data = array();
        foreach ($images as $image) {
            if (array_key_exists('objects', $image)) {
                foreach ($image['objects'] as $record) {
                    $record['apiURI'] = route('api.objects.show', [$record['admin']['id']]);
                    $record['id'] = $record['admin']['id'];
                    unset($record['admin']);
                    $image['objects'] = $record;
                }
            }
            if (array_key_exists('zoom', $image['processed'])) {
                $image['manifestURI'] = $this->generateManifestURI($image['objects']['id']);
                $image['uvViewerPath'] = $this->generateUvViewer($image['objects']['id']);
                $image['uvViewerEmbedHTML'] = $this->generateUvEmbed($image['objects']['id']);
                $files = $this->generateIiifFiles($image['admin']['id']);
                foreach ($files as $key => $file) {
                    $image[$key] = $file;
                }
            }
            $image = $this->unsetKey($image);
            $image['apiURI'] = route('api.iiif.show', [$image['admin']['id']]);
            unset($image['admin']['created']);
            unset($image['admin']['modified']);
            $data[] = $image;
        }
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    private function unsetKey(array $data): array
    {
        unset($data['processed']);
        return $data;
    }

    /**
     * @param string $id
     * @return string
     */
    private function generateManifestURI(string $id): string
    {
        return env('FITZ_MANIFEST_URL') . $id . '/manifest';
    }

    /**
     * @param string $id
     * @return string
     */
    private function generateUvEmbed(string $id): string
    {
        return '<iframe src="' . env('FITZ_UV_VIEWER_PATH') . env('FITZ_MANIFEST_URL') . $id . '/manifest&c=0&m=0&cv=0&config=&locales=en-GB:English (GB),cy-GB:Cymraeg,fr-FR:Français (FR),pl-PL:Polski,sv-SE:Svenska&r=0" width="560" height="420" allowfullscreen></iframe>';
    }

    /**
     * @param string $id
     * @return string
     */
    private function generateUvViewer(string $id): string
    {
        return env('FITZ_UV_VIEWER_PATH') . env('FITZ_MANIFEST_URL') . $id . '/manifest&c=0&m=0&cv=0&config=&locales=en-GB:English (GB),cy-GB:Cymraeg,fr-FR:Français (FR),pl-PL:Polski,sv-SE:Svenska&r=0';
    }

    /**
     * @var array
     */
    public array $derivatives = array(
        'iiifMetadata' => '/info.json',
        'greyScale' => '/full/full/0/grey.jpg',
        'default' => '/full/full/0/default.jpg',
        '600x400' => '/full/!600,400/0/default.jpg',
        '100x100Aspect' => '/full/!100,100/0/default.jpg'
    );

    /**
     * @param string $id
     * @return array
     */
    public function generateIiifFiles(string $id): array
    {
        $files = array();
        foreach ($this->derivatives as $k => $v) {
            $files[$k] = 'https://api.fitz.ms/data-distributor/iiif/image/portfolio-' . $id . $v;
        }
        return $files;
    }
}
