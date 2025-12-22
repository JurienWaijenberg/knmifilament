<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LocationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $query = Location::query()->latest();

        if (request()->has('with') && request('with') === 'measurements') {
            $query->with('measurements');
        }

        $locations = $query->paginate();

        return LocationResource::collection($locations);
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location): LocationResource
    {
        if (request()->has('with') && request('with') === 'measurements') {
            $location->load('measurements');
        }

        return new LocationResource($location);
    }
}
