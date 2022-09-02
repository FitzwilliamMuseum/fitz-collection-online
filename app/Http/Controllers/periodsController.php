<?php

namespace App\Http\Controllers;

use App\Models\Api\Periods;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;use Illuminate\Pagination\Paginator;use Illuminate\Support\Collection;

class periodsController extends Controller
{

    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $periods = Periods::list($request);
        $paginator = $this->paginate(
            $periods['aggregations']['records']['buckets'],
            50,
            LengthAwarePaginator::resolveCurrentPage());
        $paginator->setPath(route('periods'));
        return view('periods.index', compact('paginator'));
    }

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

}
