<?php

namespace Modules\PropertyManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\PropertyManagement\Repositories\PropertyReposiotry;
use Modules\PropertyManagement\Transformers\PropertyResource;

class PropertyManagementController extends Controller
{
    /**
     * @var PropertyReposiotry
     */
    private PropertyReposiotry $propertyReposiotry;

    /**
     * PropertyManagementController constructor.
     * @param PropertyReposiotry $propertyReposiotry
     */
    public function __construct(PropertyReposiotry $propertyReposiotry)
    {
        $this->propertyReposiotry = $propertyReposiotry;
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     * @see \Illuminate\Http\Request
     * @see \Modules\PropertyManagement\Resources\PropertyResource
     */
    public function index()
    {
        $properties = $this->propertyReposiotry->all();

        return PropertyResource::collection($properties);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_unless(auth()->user()->tokenCan('property.create'), Response::HTTP_FORBIDDEN);

        $property = $this->propertyReposiotry->create($request->all());

        return PropertyResource::make($property);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        abort_unless(auth()->user()->tokenCan('property.show'), Response::HTTP_FORBIDDEN);

        $property = $this->propertyReposiotry->find($id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], Response::HTTP_NOT_FOUND);
        }

        return PropertyResource::make($property);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        abort_unless(auth()->user()->tokenCan('property.update'), Response::HTTP_FORBIDDEN);

        $property = $this->propertyReposiotry->update($id, $request->all());

        return PropertyResource::make($property);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        abort_unless(auth()->user()->tokenCan('property.destroy'), Response::HTTP_FORBIDDEN);

        $this->propertyReposiotry->destroy($id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
