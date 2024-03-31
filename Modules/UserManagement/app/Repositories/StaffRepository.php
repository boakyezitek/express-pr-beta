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

class StaffRepository extends BaseRepository
{
    /**
     * Get the model class
     *
     * @return Model
     */
    public function getModel()
    {
        return new \Modules\UserManagement\Models\Staff();
    }

    /**
     * Get all staff with pagination. Filter by staff type if provided.
     *
     * @param string $staff_type
     * @param int $page
     * @param int $per_page
     * @return Model
     */
    public function all()
    {
        return $this->model->query()
            ->when(
                request('staff_type'),
                fn ($builder) => $builder->where('staff_type', request('staff_type')),
                fn ($builder) => $builder
            )
            ->with(['account', 'avatar'])
            ->paginate(20);
    }

    /**
     * Get all staff visible on website page with pagination.
     *
     * @param int $page
     * @param int $per_page
     * @return Model
     */
    public function visibleOnWebsite()
    {
        return $this->model->query()
            ->where('is_visible_on_website', 1)
            ->with(['account', 'avatar'])
            ->paginate(20);
    }

    /**
     * Create a new staff member with the given data.
     * The data is validated before the staff member is created.
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
            'staff_type' => ['required', 'numeric', 'in:1,2,3'],
        ])->validate();

        $model = $this->model;

        $staff = DB::transaction(function () use ($model, $data) {
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make(Str::random(8)),
            ]);

            $data['user_id'] = $user->id;
            $model->fill($data)->save;

            return $model;
        });

        return $staff;
    }

    /**
     * Update a staff member with the given ID and data.
     * The data is validated before the staff member is updated.
     *
     * @param array $request
     * @param int $id
     * @return Model
     */
    public function update($request, $id)
    {
        $staff = $this->getSingleModel($id);
        // Check if the staff member exists.
        if (!$staff) {
            return response()->json(['message' => 'Staff not found'], 404);
        }

        // Validate the request data.
        $data = validator(request()->all(), [
            'first_name' => [Rule::when($staff->exists, 'sometimes'), 'required', 'string'],
            'last_name' => [Rule::when($staff->exists, 'sometimes'), 'required', 'string'],
            'email' => [Rule::when($staff->exists, 'sometimes'), 'required', 'email'],
            'phone' => [Rule::when($staff->exists, 'sometimes'), 'required'],
            'staff_type' => [Rule::when($staff->exists, 'sometimes'), 'required', 'numeric', 'in:1,2,3'],
        ])->validate();

        $staff->fill($data);

        DB::transaction(function () use ($staff, $data) {
            $staff->save();
        });

        return $staff;
    }

    /**
     * Delete a staff member with the given ID.
     *
     * @param int $id
     * @return Model
     */
    public function delete($id)
    {
        $staff = $this->getSingleModel($id);

        if ($staff->avatar) {
            $avatar = $staff->avatar;
            Storage::delete($avatar->path);

            $avatar->delete();
        }

        DB::transaction(function () use ($staff) {
            $staff->delete();
        });

        return $staff;
    }
}
