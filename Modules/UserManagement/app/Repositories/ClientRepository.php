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


class ClientRepository extends BaseRepository
{
    /**
     * Get the model class
     *
     * @return Model
     */
    public function getModel()
    {
        return new \Modules\UserManagement\Models\Client();
    }

    public function all()
    {
        return $this->model->query()
            ->with(['account', 'avatar', 'pictureId'])
            ->withCount(['properties' => fn ($builder) => $builder->where('status', '!=', Property::PROPERTY_CANCELLED)])
            ->paginate(20);
    }

    /**
     * Create a new client
     *
     * @param array $request
     * @return Model
     */
    public function create($request)
    {
        // Validate input data
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

        $client = $this->model;

        $resource = DB::transaction(function () use ($client, $data) {
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make(Str::random(8)),
            ]);

            $data['user_id'] = $user->id;

            $client->fill($data);
            $client->save();

            return $client;
        });

        return $resource;
    }

    /**
     * Update a client
     *
     * @param array $request
     * @param int $id
     * @return Model
     */
    public function find($id)
    {
        $client = $this->getModel()->findOrFail($id);

        $client->loadCount(['properties' => fn ($builder) => $builder->where('status', '!=', Property::PROPERTY_CANCELLED)])
            ->load('avatar', 'account', 'pictureId');

        return $client;
    }

    /**
     * Update a client
     *
     * @param array $request
     * @param int $id
     * @return Model
     */
    public function update($request, $id)
    {
        $client = $this->getModel()->findOrFail($id);

        $data = validator($request, [
            'first_name' => [Rule::when($client->exists, 'sometimes'), 'required', 'string'],
            'last_name' => [Rule::when($client->exists, 'sometimes'), 'required', 'string'],
            'email' => [Rule::when($client->exists, 'sometimes'), 'required', 'email'],
            'phone' => [Rule::when($client->exists, 'sometimes'), 'required'],
            'address' => [Rule::when($client->exists, 'sometimes'), 'required', 'string'],
            'city' => [Rule::when($client->exists, 'sometimes'), 'required', 'string'],
            'state' => [Rule::when($client->exists, 'sometimes'), 'required', 'string'],
            'zipcode' => [Rule::when($client->exists, 'sometimes'), 'required', 'string'],
        ])->validate();

        $client->fill($data);

        DB::transaction(function () use ($client, $data) {
            $client->save();
        });

        return $client;
    }

    public function delete($id)
    {
        $client = $this->getModel()->findOrFail($id);

        if ($client->avatar) {
            $avatar = $client->avatar;
            Storage::delete($avatar->path);
            $avatar->delete();
        }

        if ($client->pictureId) {
            $pictureId = $client->pictureId;
            Storage::delete($pictureId->path);
            $pictureId->delete();
        }

        // Delete the client
        return $client->delete();
    }
}
