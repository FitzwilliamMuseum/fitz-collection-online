<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\Images;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 * path="/api/v1/images",
 * summary="Retrieve images used in the database",
 * description="A list of images used in the database, with pagination.",
 * tags={"Static Images and IIIF"},
 * security={{"bearerAuth": {}}},
 * @OA\Parameter(
 *    description="Query the system for objects and artworks (defaults to AND)",
 *    in="query",
 *    name="query",
 *    required=false,
 *    example="Dendera",
 *    @OA\Schema(
 *       type="string",
 *       format="string"
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
 *    )
 * ),
 *  @OA\Parameter(
 *    description="Number of items returned per page",
 *    in="query",
 *    name="size",
 *    required=false,
 *    example="20",
 *    @OA\Schema(
 *       type="integer",
 *    )
 * ),
 * @OA\Parameter(
 *    description="Sort direction",
 *    in="query",
 *    name="sort",
 *    required=false,
 *    example="asc",
 *    @OA\Schema(
 *       type="enum",
 *     enum={"asc","desc"}
 *    )
 * ),
 * @OA\Parameter(
 *    description="Sort field",
 *    in="query",
 *    name="sort_field",
 *    required=false,
 *    example="id",
 *    @OA\Schema(
 *       type="enum",
 *     enum={"id","created","updated","name","summary_title"}
 *    )
 * ),
 * @OA\Response(
 *    response=200,
 *    description="The request completed successfully."
 * ),
 * @OA\Response(
 *    response=400,
 *    description="The request cannot be processed"
 * ),
 * @OA\Response(
 *    response=404,
 *    description="Not found"
 * ),
 * ),
 * @OA\Get(
 * path="/api/v1/images/{image}",
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
 *     type="string",
 *     format="string",
 *     pattern="^media-\d+$",
 *    )
 * ),
 * @OA\Response(
 *    response=200,
 *    description="The request completed successfully."
 * ),
 * @OA\Response(
 *    response=400,
 *    description="The request cannot be processed"
 * ),
 * @OA\Response(
 *    response=404,
 *    description="Not found"
 * ),
 * ),
 * )
 */
class ImagesController extends ApiController
{

    public array $_params = array(
        'sort', 'size', 'hasIIIF', 'page',
        'query', 'fields', 'sort_field',
    );

    /**
     * @var array
     */
    private array $_showFields = array('object', 'fields');

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "*" => "in:" . implode(",", $this->_params),
            "page" => "numeric|gt:0",
            "size" => "numeric|gt:0|lte:100",
            "hasIIIF" => "boolean",
            "query" => "string|min:3",
            'sort_field' => 'string|in:id,title,created,updated|min:2',
            'sort' => 'string|in:asc,desc|min:3',
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }
        $response = Images::list($request);
        $data = $this->insertType($this->parseData($response),'images');
        if (empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $enriched = $this->enrichMultipleImages($data);
            $paginator = new LengthAwarePaginator(
                $enriched,
                $response['hits']['total']['value'],
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
        $validator = Validator::make(array_merge($request->all(),array('id' => $id)), [
            "*" => "in:".implode(',', $this->_showFields),
            'id' => "string|min:8|regex:'^media-\d+$'"
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }
        $data  = Images::show($request, $id);

        if (empty($data)) {
            return $this->jsonError(404, 'No image found.');
        } else {
            $enriched = self::enrichSingleImage($data);
            return $this->jsonSingle($this->insertSingleType(($enriched),'images'));
        }
    }

    /**
     * @param $data
     * @return array
     */
    private function enrichSingleImage($data): array
    {
        $data['apiURI'] = route('api.images.show', $data['admin']['id']);
        $data['uri'] = route('image.single', [$data['admin']['id']]);
        $data['images'] = $this->append_single_iip_url($data);
        if (array_key_exists('zoom', $data['images'])) {
            $data['manifestURI'] = $this->generateManifestURI($data['admin']['id']);
            $data['uvViewerPath'] = $this->generateUvViewer($data['admin']['id']);
            $data['uvViewerEmbedHTML'] = $this->generateUvEmbed($data['admin']['id']);
            $data['miradorPath'] = route('image.mirador', [$data['admin']['id']]);
            $data['iiifAPIURI'] = route('api.iiif.show', [$data['admin']['id']]);
        }
        $data = $this->unsetKey($data);
        if (array_key_exists('objects', $data)) {
            foreach ($data['objects'] as $record) {
                $record['URI'] = route('record', str_replace('object-', '', $record['admin']['id']));
                $record['apiURI'] = route('api.objects.show', [$record['admin']['id']]);
                $record['id'] = $record['admin']['id'];
                $data['objects'] = $record;
            }
        }
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    private function enrichMultipleImages(array $data): array
    {
        $images = array();
        foreach ($data as $image) {
            if (array_key_exists('objects', $image)) {
                foreach ($image['objects'] as $record) {
                    $record['URI'] = route('record', str_replace('object-', '', $record['admin']['id']));
                    $record['apiURI'] = route('api.objects.show', [$record['admin']['id']]);
                    $record['id'] = $record['admin']['id'];
                    unset($record['admin']);
                    $image['objects'] = $record;
                }
            }
            $image['images'] = $this->append_single_iip_url($image);
            if (array_key_exists('zoom', $image['processed'])) {
                $image['manifestURI'] = $this->generateManifestURI( $image['objects']['id']);
                $image['uvViewerPath'] = $this->generateUvViewer($image['objects']['id']);
                $image['uvViewerEmbedHTML'] = $this->generateUvEmbed( $image['objects']['id']);
                $image['miradorPath'] = route('image.mirador', [$image['admin']['id']]);
                $image['iiifAPIURI'] = route('api.iiif.show', [$image['admin']['id']]);
            }
            $image = $this->unsetKey($image);

            $image['URI'] = route('image.single', [$image['admin']['id']]);
            $image['apiURI'] = route('api.images.show', [$image['admin']['id']]);
            $images[] = $image;
        }
        return $images;
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
}
