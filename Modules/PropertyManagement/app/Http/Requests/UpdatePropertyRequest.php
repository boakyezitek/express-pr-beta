<?php

namespace Modules\PropertyManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\PropertyManagement\Models\Property;

class UpdatePropertyRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(Property $property)
    {
        $commonRules = [
            Rule::when($property->exists, 'sometimes'),
            'required',
        ];

        return [
            'property_name' => [...$commonRules, 'string'],
            'description' => [...$commonRules, 'string'],
            'monthly_rent_wanted' => [...$commonRules, 'integer'],
            'min_security_deposit' => [...$commonRules, 'integer'],
            'min_lease_term' => [...$commonRules, 'integer', 'max:12'],
            'max_lease_term' => [...$commonRules, 'integer', 'max:12'],
            'bedroom' => [...$commonRules, 'integer', 'max:9'],
            'bath_full' => [...$commonRules, 'max:9'],
            'bath_half' => [...$commonRules, 'max:9'],
            'size' => [...$commonRules, 'integer'],
            'street_address' => [...$commonRules, 'string'],
            'city' => [...$commonRules, 'string'],
            'state' => [...$commonRules, 'string'],
            'zipcode' => [...$commonRules, 'string'],
            'furnished' => [...$commonRules, 'integer'],
            'featured' => [...$commonRules, 'integer'],
            'parking_spaces' => [...$commonRules, 'integer'],
            'parking_number' => [...$commonRules, 'string'],
            'parking_fees' => [...$commonRules, 'integer'],
            'is_lease_the_start_date_and_end_date' => [...$commonRules, 'integer'],
            'lease_start_date' => [...$commonRules, 'date:Y-m-d'],
            'lease_end_date' => [...$commonRules, 'date:Y-m-d'],
            'available_property_date' => [...$commonRules, 'date:Y-m-d'],
            'lat' => [...$commonRules, 'numeric'],
            'lng' => [...$commonRules, 'numeric'],
            'client_id' => [...$commonRules, 'integer'],
            'created_by' => [...$commonRules, 'integer'],
            'property_amenity' => ['array'],
            'property_amenity.*' => ['integer', Rule::exists('amenity_items', 'id')],
            'property_utility_included' => ['array'],
            'property_utility_included.*' => ['integer', Rule::exists('utility_items', 'id')],
            'property_type' => ['array'],
            'property_type.*' => ['integer', Rule::exists('property_type_items', 'id')],
            'property_parking_type' => ['array'],
            'property_parking_type.*' => ['integer', Rule::exists('parking_items', 'id')],
        ];
    }
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
