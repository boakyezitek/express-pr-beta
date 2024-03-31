<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\UserManagement\Repositories\TenantRepository;
use Modules\UserManagement\Transformers\TenantResource;

class TenantController extends Controller
{
    private $tenantRepository;

    public function __construct(TenantRepository $tenantRepository)
    {
        $this->tenantRepository = $tenantRepository;
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
        abort_unless(auth()->user()->tokenCan('tenant.index'), Response::HTTP_FORBIDDEN);

        $tenants = $this->tenantRepository->all();

        return TenantResource::collection($tenants);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(auth()->user()->tokenCan('tenant.create'), Response::HTTP_FORBIDDEN);

        $tenant = $this->tenantRepository->create($request->all());

        return TenantResource::make($tenant->load('pictureId', 'avatar', 'account'));
    }

    /**
     * Show the specified resource in storage by its ID.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $tenant = $this->tenantRepository->find($id);

        $tenant->load('avatar', 'account', 'pictureId');

        return TenantResource::make($tenant);
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
        abort_unless(auth()->user()->tokenCan('tenant.update'), Response::HTTP_FORBIDDEN);

        $tenant = $this->tenantRepository->update($request->all(), $id);
        $tenant->load('pictureId', 'avatar', 'account');

        return TenantResource::make($tenant);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->tenantRepository->delete($id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
