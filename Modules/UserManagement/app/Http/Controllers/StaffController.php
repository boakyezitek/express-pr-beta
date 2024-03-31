<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\UserManagement\Repositories\StaffRepository;
use Modules\UserManagement\Transformers\StaffResource;

class StaffController extends Controller
{
    /**
     * The staff repository instance.
     *
     * @var StaffRepository
     */
    private $staffRepository;

    /**
     * Create a new instance of the controller.
     *
     * @param StaffRepository $staffRepository
     */
    public function __construct(StaffRepository $staffRepository)
    {
        $this->staffRepository = $staffRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @apiResource StaffResource
     */
    public function index()
    {
        abort_unless(auth()->user()->tokenCan('staff.index'), Response::HTTP_FORBIDDEN);

        $staffs = $this->staffRepository->all();

        return StaffResource::collection($staffs);
    }

    /**
     * Get all staff members that are visible on the website.
     *
     * @return \Illuminate\Http\Response
     * @apiResource StaffResource
     */
    public function visibleOnWebsite()
    {
        $staffs = $this->staffRepository->visibleOnWebsite();

        return StaffResource::collection($staffs);
    }

    /**
     * Store a newly created resource in storage.
     *
     */
    public function store(Request $request)
    {
        abort_unless(auth()->user()->tokenCan('staff.create'), Response::HTTP_FORBIDDEN);

        $staff = $this->staffRepository->create($request->all());

        return StaffResource::make($staff->load('account', 'avatar'));
    }

    /**
     * Show the specified resource in storage by its ID or slug or email address if it exists in the database and is visible on the website.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @apiResource StaffResource
     * @apiParam int $id The ID of the staff member.
     */
    public function show($id)
    {
        abort_unless(auth()->user()->tokenCan('staff.show'), Response::HTTP_FORBIDDEN);

        $staff = $this->staffRepository->find($id);

        return StaffResource::make($staff->load('account', 'avatar'));
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request, $id)
    {
        abort_unless(auth()->user()->tokenCan('staff.update'), Response::HTTP_FORBIDDEN);

        $staff = $this->staffRepository->update($request->all(), $id);

        return StaffResource::make($staff->load('account', 'avatar'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_unless(auth()->user()->tokenCan('staff.destroy'), Response::HTTP_FORBIDDEN);

        $this->staffRepository->delete($id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
