<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\UserManagement\Repositories\ClientRepository;
use Modules\UserManagement\Transformers\ClientResource;

class ClientController extends Controller
{

    /**
     * The client repository.
     *
     * @var ClientRepository
     */
    private ClientRepository $clientRepository;

    /**
     * Create a new instance of the controller.
     *
     * @param ClientRepository $clientRepository
     */
    public function __construct(ClientRepository $clientRepository)
    {
       $this->clientRepository = $clientRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_unless(auth()->user()->tokenCan('client.index'), Response::HTTP_FORBIDDEN);

        $clients = $this->clientRepository->all();

        return ClientResource::collection($clients);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_unless(auth()->user()->tokenCan('client.create'), Response::HTTP_FORBIDDEN);

        $client = $this->clientRepository->create($request->all());

        return ClientResource::make($client->load('pictureId', 'avatar', 'account'));
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $client = $this->clientRepository->find($id);

        return ClientResource::make($client);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        abort_unless(auth()->user()->tokenCan('client.update'), Response::HTTP_FORBIDDEN);

        $client = $this->clientRepository->update($request->all(), $id);

        return ClientResource::make($client->load('pictureId', 'avatar', 'account'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->clientRepository->delete($id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
