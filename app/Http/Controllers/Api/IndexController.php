<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/api/v1/",
 *     description="Home page",
 *     @OA\Response(response="default", description="Welcome page")
 * )
 */

class IndexController extends ApiController
{
    /**
     * [public description]
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(
            [
                'data' => [],
                'message' => 'Welcome to the Fitzwilliam Museum Collection API',
                'description'  =>
                    'This is the API for the Fitzwilliam Museum Collection. It is a purely JSON API, generated off Knowledge Integration\'s integration between Axiell Collections and their CIIM software. ',
                'version' => '1.0.0',
                'releaseDate' => '01-07-2022',
                'docs' => route('api.home') . '/docs',
                'api' => [
                    'Get root' => route('api.home'),
                    'Get a list of agents' => route('api.agents.index'),
                    'Get a specific agent' => route('api.home') . '/agents/{id}',
                    'Get a list of departments' => route('api.departments.index'),
                    'Get a list of exhibitions' => route('api.exhibitions.index'),
                    'Get a specific exhibition' => $this->createApiPath('exhibitions/', '{exhibition}'),
                    'Get a list of IIIF images' => route('api.iiif.index'),
                    'Get a specific IIIF image attached to an object' => $this->createApiPath('iiif/', '{object}'),
                    'Get a list of images' => route('api.images.index'),
                    'Get a specific image' => $this->createApiPath('images/', '{image}'),
                    'Get a list of institutions' => route('api.institutions.index'),
                    'Get a specific institution' => $this->createApiPath('institutions/', '{institution}'),
                    'Get a list of makers' => route('api.makers.index'),
                    'Get a specific maker' => $this->createApiPath('makers/', '{maker}'),
                    'Get a list of objects or artworks' =>  route('api.objects.index'),
                    'Get a spefific object' => $this->createApiPath('objects/', '{object}'),
                    'Get a list of periods' => route('api.periods.index'),
                    'Get a specific period' => $this->createApiPath('periods/', '{period}'),
                    'Get a list of places' =>  route('api.places.index'),
                    'Get a specific place' => $this->createApiPath('places/', '{place}'),
                    'Get a list of terms' => route('api.terminology.index'),
                    'Get a specific term' => $this->createApiPath('terminology/', '{term}'),
                ],
                'author' => 'Professor Daniel Pett',
                'httpCode' => 200
            ],
            200,
            $this->getHeaders(),
            JSON_PRETTY_PRINT
        );
    }

    public function createApiPath($fragment, $param)
    {
        return route('api.home') . '/' . $fragment . $param ;
    }
}
