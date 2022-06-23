<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\Institutions;

use App\Rules\PlacesFieldsAllowed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 * path="/api/v1/institutions",
 * summary="Institutions representing ownership",
 * description="Institutions representing ownership of objects within the museum",
 * tags={"Terminology"},
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
class InstitutionsController extends ApiController
{

    private array $_params = ['sort'];

    private array $_showParams = ['institution','fields'];

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "*" => "in:" . implode(",", $this->_params),
            "sort" => "string|in:asc,desc"
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }
        $response = Institutions::list($request);
        if(array_key_exists('aggregations', $response)){
            $data = array();
            foreach ($response['aggregations']['institutions']['buckets'] as $department) {
                $data[] = array(
                    'institutions' => $department['key'],
                    'records' => $department['doc_count'],
                    'summary_title' => $department['inst']['hits']['hits'][0]['_source']['institutions'][0]['summary_title'],
                    'URI'  => $this->getWebURI('terminology', $department['key']),
                    'apiURI' => $this->getTermURI('api.institutions.show', $department['key']),
                );
            }
            return $this->jsonSingle($data);
        } else {
            return $this->jsonError(404, $this->_notFound);
        }
    }

    /**
     * @param Request $request
     * @param string $institution
     * @return JsonResponse
     */
    public function show(Request $request, string $institution): JsonResponse
    {
        $validator = Validator::make(array_merge($request->all(), array('institution' => $institution)), [
            '*' => 'in:' . implode(",", $this->_showParams),
            'institution' => "string|min:6|regex:'^agent-\d+$'",
            "fields" => ['min:5', new PlacesFieldsAllowed],
        ]);

        if ($validator->fails()) {
            return $this->jsonError(400, $validator->errors());
        }

        $data = Institutions::show($request, $institution);

        if($data) {
            $data['URI'] = $this->getWebURI('agent', $data['admin']['id']);
            $data['apiURI'] = $this->getTermURI('api.institutions.show', $data['admin']['id']);
            return $this->jsonSingle($data);
        } else {
            return $this->jsonError(404, $this->_notFound);
        }
    }

}
