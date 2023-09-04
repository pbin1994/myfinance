<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePendingRequest;
use App\Http\Requests\UpdatePendingRequest;
use App\Http\Requests\PayPendingRequest;
use App\Models\Pending;
use App\Models\Transaction;
use App\Models\Client;
use Illuminate\Http\Request;

class PendingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $clients = Client::orderBy('name', 'ASC')->where('user_id', $request->user()->id)->get();

        $pendings = Pending::orderBy('am_date', 'ASC')
        ->join('clients', 'pendings.client_id', '=', 'clients.id')
        ->where('pendings.user_id', $request->user()->id)
        ->select('pendings.*', 'clients.name AS client_name')
        ->get()->toArray();

        $itog=0;
        foreach ($pendings as $key => $pending) {
            $pendings[$key]['am_date_formated']=date('d.m.Y',strtotime($pending['am_date']));
            $itog+=$pending['amount'];
        }
        $itog=number_format($itog, 2, '.', '');

        $data=array('clients'=>$clients, 'pendings'=>$pendings, 'itog'=>$itog);
        return response()->json([
            'success' => true,
            'message' => 'List data pendings',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePendingRequest $request)
    {
         $request->validate([
            'client' => 'array',
            'am_date' => 'required|string|max:255',
            'amount' => 'required|integer|min:1',
        ]);

         $client_id=$request->client['id'];

         if ($client_id===0)
         {
            $client = Client::create([
                'name' => $request->client['name'],
                'user_id'=> $request->user()->id
            ]);
            $client_id=$client->id;
        }


        $pending = Pending::create([
            'client_id' => $client_id,
            'am_date' => $request->am_date.' '.date('H:i:s'),
            'amount' => $request->amount,
            'user_id'=> $request->user()->id
        ]);


        return response()->json([
            'success' => true,
            'message' => 'pending created',
            'data' => $pending
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pending $pending)
    {
        //
    }

   /**
     * Pay the specified resource in storage.
     */
   public function pay(PayPendingRequest $request, Pending $pending)
   {
        $request->validate([
            'amount' => 'required|integer|min:1',
        ]);
        $pending = Pending::find($pending->id);
        $user_id_cur= $request->user()->id;
        if ($pending && $pending->user_id==$user_id_cur) {

                $transaction = Transaction::create([
                    'client_id' => $pending->client_id,
                    'am_date' => date('Y-m-d H:i:s'),
                    'amount' => $request->amount,
                    'user_id'=> $request->user()->id
                ]);

            if ($pending->amount<=$request->amount)
            {
                 $pending->delete();
            }
            else
            {
                 $pending->update([
                    'amount' => $pending->amount-$request->amount,
                ]); 
            }


                 return response()->json([
                    'success' => true,
                    'message' => 'pending created',
                    'data' => $pending
                ], 201);
        }
   }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePendingRequest $request, Pending $pending)
    {
        $request->validate([
            'client' => 'array',
            'am_date' => 'required|string|max:255',
            'amount' => 'required|integer|min:1',
        ]);
        $pending = Pending::find($pending->id);

        $user_id_cur= $request->user()->id;
        if ($pending && $pending->user_id==$user_id_cur) {


            $client_id=$request->client['id'];

            if ($client_id===0)
            {
                $client = Client::create([
                    'name' => $request->client['name'],
                    'user_id'=> $request->user()->id
                ]);
                $client_id=$client->id;
            }

            $pending->update([
                'client_id' => $client_id,
                'am_date' => $request->am_date.' '.date('H:i:s'),
                'amount' => $request->amount,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'pending updated',
                'data' => $pending
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'pending not found'
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pending $pending, Request $request)
    {
         $user_id_cur= $request->user()->id;
         if ($pending && $pending->user_id==$user_id_cur) {
            $pending->delete();

            return response()->json([
                'success' => true,
                'message' => 'pending deleted'
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'pending not found'
        ], 404);
    }
}
