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

class TenantRepository extends BaseRepository{
    /**
     * Get the model class
     *
     * @return Model
     */
    public function getModel()
    {
        return new \Modules\UserManagement\Models\Tenant();
    }

    /**
     * Get all Tenants
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
     * Create a new Tenant
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

        $tenant = $this->getModel();

        $resource = DB::transaction(function () use ($tenant, $data) {
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make(Str::random(8)),
            ]);

            $data['user_id'] = $user->id;

            $tenant->fill($data);
            $tenant->save();

            return $tenant;
        });

        return $resource;

    }

    /**
     * Update a Tenant
     *
     * @param array $request
     * @param int $id
     * @return Model
     */
    public function update($request, $id){

        $tenant = $this->getSingleModel($id);

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

        // Update the Tenant with the validated data within a database transaction
        $tenant->fill($data);

        DB::transaction(function () use ($tenant) {
            $tenant->save();
        });

        return $tenant;
    }

    /**
     * Delete a Tenant
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $tenant = $this->getSingleModel($id);

        if ($tenant->avatar) {
            $avatar = $tenant->avatar;
            Storage::delete($avatar->path);
            $avatar->delete();
        }

        if ($tenant->pictureId) {
            $pictureId = $tenant->pictureId;
            Storage::delete($pictureId->path);
            $pictureId->delete();
        }

        // Delete the specified Tenant
       return $tenant->delete();
    }

}
