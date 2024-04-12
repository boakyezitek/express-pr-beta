<?php

namespace Modules\PropertyManagement\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\BaseConfig\Repositories\BaseRepository;
use Modules\PropertyManagement\Http\Requests\PropertyValidator;

class PropertyRepository extends BaseRepository
{
    /**
     * Array mapping related models to their corresponding relationships.
     *
     * @var array
     */
    private $relatedModels = [
        'property_amenity' => 'propertyAmenity',
        'property_utility_included' => 'propertyUtilityIncluded',
        'property_type' => 'propertyType',
        'property_parking_type' => 'propertyParkingType',
    ];


    /**
     * Get the model class
     *
     * @return Model
     */
    public function getModel()
    {
        return new \Modules\PropertyManagement\Models\Property();
    }

    /**
     * Get all properties
     *
     * @return Collection
     */
    public function all()
    {
        $query = $this->getModel()->query()
            ->with([
                'createdBy',
                'client',
                'propertyAmenity',
                'propertyUtilityIncluded',
                'propertyType',
                'propertyParkingType',
                'images'
            ]);

        $this->applyFilters($query);

        return $query->latest()
            ->paginate(20);
    }

    /**
     * Create a new property
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data)
    {
        $attributes = (new PropertyValidator())->validate(
            $this->getModel(),
            $data
        );

        $property = DB::transaction(function () use ($attributes) {
            $property =  $this->getModel()::create(Arr::except($attributes, ['property_amenity', 'property_utility_included', 'property_type', 'property_parking_type']));

            $this->attachRelatedModels($this->model, $attributes, $this->relatedModels);

            return $property;
        });

        return $this->load($property);
    }

    /**
     * Update a property
     *
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function find($id)
    {
        $property = $this->find($id);

        return $this->load($property);
    }

    public function update($id, array $data)
    {
        $attributes = (new PropertyValidator())->validate(
            $this->getModel(),
            $data
        );

        $property = $this->find($id);

        $property->fill(Arr::except($attributes, ['property_amenity', 'property_utility_included', 'property_type', 'property_parking_type']));

        DB::transaction(function () use ($property, $attributes) {
            if (isset($attributes['property_type'])) {
                $property->propertyType()->detach($attributes['property_type']);
            }
            $property->save();
            $this->syncRelatedModels($this->model, $attributes, $this->relatedModels);
        });

        return $this->load($property);
    }

    /**
     * Delete a property
     *
     * @param int $id
     */
    public function destroy($id)
    {
        $property = $this->find($id);

        $this->removeRelatedModels($property, $this->relatedModels);

        if($property->images()) {
            $property->images()->each(function ($image) {
                Storage::delete($image->path);

                $image->delete();
            });
        }

        $property->delete();
    }

    /**
     * Apply filters to the query
     *
     * @param Builder $query
     */
    protected function applyFilters(Builder $query)
    {
        $query->when(request('month_year'), function (Builder $builder) {
            $now = Carbon::now();
            $selectedDate = Carbon::parse(request('month_year'));

            $builder->where(function ($query) use ($now, $selectedDate) {
                if ($now->isSameMonth($selectedDate) && $now->isSameYear($selectedDate)) {
                    $query->where('available_property_date', '<=', $selectedDate)
                        ->where('status', '!=', $this->getModel()::PROPERTY_CANCELLED);
                } else {
                    $query->whereMonth('available_property_date', '=', $selectedDate->month)
                        ->whereYear('available_property_date', '=', $selectedDate->year)
                        ->where('status', '!=', $this->getModel()::PROPERTY_CANCELLED);
                }
            });
        })
        ->when(request('available_property_date'), fn ($builder) => $builder->whereDate('available_property_date', request('available_property_date')))
        ->when(request('client'), fn ($builder) => $builder->where('client_id', (int) request('client')))
        ->when(request('status'), fn ($builder) => $builder->where('status', request('status')))
        ->when(request('search'), fn ($builder) => $builder->where('property_name', 'LIKE', '%' . request('search') . '%'))
        ->when(request('property_type'), fn ($builder) => $builder->whereRelation('propertyType', 'id', '=', request('property_type')));
    }

    /**
     * Load the related models for the property
     *
     * @param Model $property
     */
    protected function load($property)
    {
        return $property->load([
            'createdBy',
            'client',
            'propertyAmenity',
            'propertyUtilityIncluded',
            'propertyType',
            'propertyParkingType',
            'images'
        ]);
    }
}
