<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $clients = Client::orderBy('name', 'ASC')->where('user_id', $request->user()->id)->get();
        return response()->json([
            'success' => true,
            'message' => 'List data Client',
            'data' => $clients
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $clients = Client::create([
            'name' => $request->name,
            'user_id'=> $request->user()->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Client created',
            'data' => $clients
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        $client = Client::find($client->id);

        $user_id_cur= $request->user()->id;
         if ($client && $client->user_id==$user_id_cur) {
            $client->update([
                'name' => $request->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'client updated',
                'data' => $client
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'client not found'
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client, Request $request)
    {
        $user_id_cur= $request->user()->id;
         if ($client && $client->user_id==$user_id_cur) {
            $client->delete();

            return response()->json([
                'success' => true,
                'message' => 'client deleted'
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'client not found'
        ], 404);
    
    }
}
