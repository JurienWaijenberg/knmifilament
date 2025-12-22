<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCitiesRequest;
use App\Http\Requests\UpdateCitiesRequest;
use App\Http\Resources\CitiesResource;
use App\Models\Cities;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $query = Cities::query()->latest();

        if (request()->has('with')) {
            $with = request('with');
            if ($with === 'locations') {
                $query->with('locations');
            } elseif ($with === 'locations.measurements') {
                $query->with('locations.measurements');
            }
        }

        $cities = $query->paginate();

        return CitiesResource::collection($cities);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCitiesRequest $request): CitiesResource
    {
        $city = Cities::create($request->validated());

        return new CitiesResource($city);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cities $city): CitiesResource
    {
        if (request()->has('with')) {
            $with = request('with');
            if ($with === 'locations') {
                $city->load('locations');
            } elseif ($with === 'locations.measurements') {
                $city->load('locations.measurements');
            }
        }

        return new CitiesResource($city);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCitiesRequest $request, Cities $city): CitiesResource
    {
        $city->update($request->validated());

        return new CitiesResource($city);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cities $city): JsonResponse
    {
        $city->delete();

        return response()->json(null, 204);
    }
}
