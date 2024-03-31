<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\UserManagement\Repositories\VendorRepository;
use Modules\UserManagement\Transformers\VendorResource;

class VendorController extends Controller
{
    private $vendorRepository;

    public function __construct(VendorRepository $vendorRepository)
    {
        $this->vendorRepository = $vendorRepository;
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function index()
    {
        abort_unless(auth()->user()->tokenCan('vendor.index'), Response::HTTP_FORBIDDEN);

        $vendors = $this->vendorRepository->all();

        return VendorResource::collection($vendors);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(auth()->user()->tokenCan('vendor.create'), Response::HTTP_FORBIDDEN);

        $vendor = $this->vendorRepository->create($request->all());

        return VendorResource::make($vendor->load('pictureId', 'avatar', 'account'));
    }

    /**
     * Show the specified resource in storage by its ID.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $vendor = $this->vendorRepository->find($id);

        $vendor->load('avatar', 'account', 'pictureId');

        return VendorResource::make($vendor);
    }

    /**
     * Update the specified resource in storage by its ID.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        abort_unless(auth()->user()->tokenCan('vendor.update'), Response::HTTP_FORBIDDEN);

        $vendor = $this->vendorRepository->update($request->all(), $id);
        $vendor->load('pictureId', 'avatar', 'account');

        return VendorResource::make($vendor);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->vendorRepository->delete($id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
