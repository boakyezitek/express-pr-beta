<?php

namespace Modules\UserManagement\Repositories;

use App\Models\User;
use Modules\BaseConfig\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class VendorRepository extends BaseRepository{
    /**
     * Get the model class
     *
     * @return Model
     */
    public function getModel()
    {
        return new \Modules\UserManagement\Models\Vendor();
    }

    /**
     * Get all Vendors with pagination
     *
     * @return Model
     */
    public function all()
    {
        return $this->model->query()
        ->with(['account', 'avatar', 'pictureId'])
        ->paginate(20);
    }

    /**
     * Create a new Vendor
     *
     * @param array $request
     * @return Model
     */
    public function create($request)
    {
        $data = validator($request, [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['required'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string'],
            'state' => ['required', 'string'],
            'zipcode' => ['required', 'string'],
        ])->validate();

        $vendor = $this->getModel();

        $resource = DB::transaction(function () use ($vendor, $data) {
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make(Str::random(8)),
            ]);

            $data['user_id'] = $user->id;

            $vendor->fill($data);
            $vendor->save();

            return $vendor;
        });

        return $resource;

    }

    /**
     * Update a Vendor
     *
     * @param array $request
     * @param int $id
     * @return Model
     */
    public function update($request, $id){

        $vendor = $this->getSingleModel($id);

        $data = validator($request, [
            'first_name' => [Rule::when($tenant->exists, 'sometimes'), 'required', 'string'],
            'last_name' => [Rule::when($tenant->exists, 'sometimes'), 'required', 'string'],
            'email' => [Rule::when($tenant->exists, 'sometimes'), 'required', 'email'],
            'phone' => [Rule::when($tenant->exists, 'sometimes'), 'required'],
            'address' => [Rule::when($tenant->exists, 'sometimes'), 'required', 'string'],
            'city' => [Rule::when($tenant->exists, 'sometimes'), 'required', 'string'],
            'state' => [Rule::when($tenant->exists, 'sometimes'), 'required', 'string'],
            'zipcode' => [Rule::when($tenant->exists, 'sometimes'), 'required', 'string'],
        ])->validate();

        // Update the Vendor with the validated data within a database transaction
        $vendor->fill($data);

        DB::transaction(function () use ($vendor) {
            $vendor->save();
        });

        return $vendor;
    }

    /**
     * Delete a Vendor
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $vendor = $this->getSingleModel($id);

        if ($vendor->avatar) {
            $avatar = $vendor->avatar;
            Storage::delete($avatar->path);
            $avatar->delete();
        }

        if ($vendor->pictureId) {
            $pictureId = $vendor->pictureId;
            Storage::delete($pictureId->path);
            $pictureId->delete();
        }

        // Delete the specified Tenant
       return $vendor->delete();
    }

}
