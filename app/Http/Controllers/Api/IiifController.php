<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class IiifController extends ApiController
{

    /**
     * @var array|string[]
     */
    private array $admin = array(
        'admin.id',
        'admin.created',
        'admin.modified'
    );

    /**
     * @var array|string[]
     */
    private array $exif = array(
        'source.attributes.EXIF_-_ISO_Speed',
        'source.attributes.Cataloged',
        'source.attributes.Copyright',
        'source.attributes.EXIF_-_Camera_Make',
        'source.attributes.EXIF_-_Camera_Model',
        'source.attributes.EXIF_-_Exposure_Time',
        'source.attributes.EXIF_-_Focal_Length',
        'source.attributes.EXIF_-_ISO_Speed',
        'source.attributes.EXIF_-_Shutter_Speed',
        'source.attributes.File_Description',
        'source.attributes.IPTC_-_Rights_Usage_Terms',
        'source.attributes.Keywords'
    );

    private array $images = array(
        'processed.large',
        'processed.medium',
        'processed.preview',
        'processed.zoom',
        'processed.original',
    );

    /**
     * @var array|string[]
     */
    private array $objects = array(
        'objects.admin.id',
        'objects.admin.source',
        'objects.summary_title'
    );

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {

        $params = [
            'index' => 'ciim',
            'size' => $this->getSize($request),
            'from' => $this->getFrom($request),
            'track_total_hits' => true,
            'body' => [
                'sort' => $this->getSort($request),
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "term" => ["type.base" => 'media']
                            ]
                        ]
                    ]
                ],

            ],
            '_source' => [
                'admin.id,admin.source,admin.created,admin.modified,objects.admin.id,processed.large,processed.mid,processed.preview,processed.zoom,source.attributes'
            ],

        ];
        $filter = array(
            "exists" => [
                "field" => "processed.zoom"
            ]
        );
        $params['body']['query']['bool']['must'][] = [$filter];


        if(!is_null($request->query('object'))) {
            $params['body']['query']['bool']['must'][] = ["multi_match" => [
                "fields" => "objects.admin.id",
                "query" => $request->query('object'),
                "operator" => "OR",
            ]
            ];
        }
        $response = $this->searchAndCache($params);
        $count = $this->getClient()->count(Arr::except($params, ['_source', 'from', 'size', 'body.sort','track_total_hits']));
        $data = array();
        foreach ($response['hits']['hits'] as $object) {
            $image =  $object['_source'];
            if(array_key_exists('objects', $image)) {
                foreach($image['objects'] as $record) {
                    $record['URI'] = route('record',str_replace('object-','', $record['admin']['id']));
                    $record['apiURI'] = route('api.objects.show',[$record['admin']['id']]);
                    $record['id'] = $record['admin']['id'];
                    unset($record['admin']);
                    $image['objects'] = $record;
                }
            }
            $image['images'] = $this->append_single_iip_url($image);
            if(array_key_exists('zoom', $image['processed'])) {
                $image['manifestURI'] = env('FITZ_MANIFEST_URL') . $object['_source']['objects'][0]['admin']['id'] . '/manifest';
                $image['uvViewerPath'] = env('FITZ_UV_VIEWER_PATH') . env('FITZ_MANIFEST_URL') . $object['_source']['objects'][0]['admin']['id'] . '/manifest&c=0&m=0&cv=0&config=&locales=en-GB:English (GB),cy-GB:Cymraeg,fr-FR:Français (FR),pl-PL:Polski,sv-SE:Svenska&r=0';
                $image['uvViewerEmbedHTML'] = '<iframe src="'. env('FITZ_UV_VIEWER_PATH') . env('FITZ_MANIFEST_URL') . $object['_source']['objects'][0]['admin']['id'] . '/manifest&c=0&m=0&cv=0&config=&locales=en-GB:English (GB),cy-GB:Cymraeg,fr-FR:Français (FR),pl-PL:Polski,sv-SE:Svenska&r=0" width="560" height="420" allowfullscreen frameborder="0"></iframe>';
                $image['miradorPath'] = route('image.mirador',[$object['_source']['admin']['id']]);
                $image['iiifMetadata'] = 'https://api.fitz.ms/data-distributor/iiif/image/portfolio-'.$object['_source']['admin']['id'].'/info.json';
                $image['greyScale'] = 'https://api.fitz.ms/data-distributor/iiif/image/portfolio-'.$object['_source']['admin']['id'].'/full/full/0/grey.jpg';
                $image['default'] =  'https://api.fitz.ms/data-distributor/iiif/image/portfolio-'.$object['_source']['admin']['id'].'/full/full/0/default.jpg';
                $image['600x400'] =  'https://api.fitz.ms/data-distributor/iiif/image/portfolio-'.$object['_source']['admin']['id'].'/full/600,400/0/default.jpg';
                $image['100x100Aspect'] =  'https://api.fitz.ms/data-distributor/iiif/image/portfolio-'.$object['_source']['admin']['id'].'/full/!100,100/0/default.jpg';
            }
            unset($image['processed']);

            $image['URI'] = route('image.iiif', [$object['_source']['admin']['id']]);
            $image['apiURI'] = route('api.iiif.show', [$object['_source']['admin']['id']]);
            $data[] = $image;
        }
        if (empty($data)) {
            return $this->jsonError(404, 'No images found.');
        }
        $paginator = new LengthAwarePaginator(
            $data,
            $response['hits']['total']['value'],
            $count['count'],
            LengthAwarePaginator::resolveCurrentPage()
        );
        $paginator->setPath(route('api.images.index'));
        return $this->jsonGenerate($request, $paginator, $paginator->total());
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                'query' => [
                    'match' => [
                        'admin.id' => $id
                    ]
                ]
            ],
            '_source' => [
                implode(',', array_merge($this->exif, $this->objects, $this->images, $this->admin))
            ],
        ];

        $data = Collect($this->parse($this->searchAndCache($params)))->first();
        if(empty($data)) {
            return $this->jsonError(404, 'No IIIF image found.');
        } else {
            $data['apiURI'] = route('api.iiif.show', $data['admin']['id']);
            $data['uri'] = route('image.iiif', [$data['admin']['id']]);
            $data['images'] = $this->append_single_iip_url($data);
//            dd($data['images']);
            if(array_key_exists('zoom', $data['images'])) {
                $data['manifestURI'] = env('FITZ_MANIFEST_URL') . $data['admin']['id'] . '/manifest';
                $data['uvViewerPath'] = env('FITZ_UV_VIEWER_PATH') . env('FITZ_MANIFEST_URL') . $data['admin']['id'] . '/manifest&c=0&m=0&cv=0&config=&locales=en-GB:English (GB),cy-GB:Cymraeg,fr-FR:Français (FR),pl-PL:Polski,sv-SE:Svenska&r=0';
                $data['uvViewerEmbedHTML'] = '<iframe src="'. env('FITZ_UV_VIEWER_PATH') . env('FITZ_MANIFEST_URL') . $data['admin']['id'] . '/manifest&c=0&m=0&cv=0&config=&locales=en-GB:English (GB),cy-GB:Cymraeg,fr-FR:Français (FR),pl-PL:Polski,sv-SE:Svenska&r=0" width="560" height="420" allowfullscreen frameborder="0"></iframe>';
                $data['miradorPath'] = route('image.mirador',[$data['admin']['id']]);
                $data['iiifMetadata'] = 'https://api.fitz.ms/data-distributor/iiif/image/portfolio-'.$data['admin']['id'].'/info.json';
                $data['greyScale'] = 'https://api.fitz.ms/data-distributor/iiif/image/portfolio-'.$data['admin']['id'].'/full/full/0/grey.jpg';
                $data['default'] =  'https://api.fitz.ms/data-distributor/iiif/image/portfolio-'.$data['admin']['id'].'/full/full/0/default.jpg';
                $data['600x400'] =  'https://api.fitz.ms/data-distributor/iiif/image/portfolio-'.$data['admin']['id'].'/full/600,400/0/default.jpg';
                $data['100x100Aspect'] =  'https://api.fitz.ms/data-distributor/iiif/image/portfolio-'.$data['admin']['id'].'/full/!100,100/0/default.jpg';
            }
            unset($data['processed']);
            if(array_key_exists('objects', $data)) {
                foreach($data['objects'] as $record) {
                    $record['URI'] = route('record',str_replace('object-','',$record['admin']['id']));
                    $record['apiURI'] = route('api.objects.show', [$record['admin']['id']]);
                    $record['id'] = $record['admin']['id'];
                    $data['objects'] = $record;
                }
            }
            return $this->jsonSingle($data);
        }
    }
}
