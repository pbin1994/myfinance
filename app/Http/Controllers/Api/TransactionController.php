<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Models\Client;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $clients = Client::orderBy('name', 'ASC')->where('user_id', $request->user()->id)->get();

        $from=date($request->year.'-'.$request->month.'-01 00:00:00');
        $to=date('Y-m-t 23:59:59',strtotime($from));


        $transactions = Transaction::orderBy('am_date', 'ASC')
        ->join('clients', 'transactions.client_id', '=', 'clients.id')
        ->where('transactions.user_id', $request->user()->id)
        ->whereBetween('transactions.am_date', [$from, $to])
        ->select('transactions.*', 'clients.name AS client_name')
        ->get()->toArray();

        $transactions_min_year = Transaction::orderBy('am_date', 'ASC')
        ->where('transactions.user_id', $request->user()->id)
        ->limit(1)->get();
        $min_year=date('Y');
        if (isset($transactions_min_year[0])) $min_year=date('Y', strtotime($transactions_min_year[0]->am_date));



        $itog=0;
        foreach ($transactions as $key => $transaction) {
            $transactions[$key]['am_date_formated']=date('d.m.Y',strtotime($transaction['am_date']));
            $itog+=$transaction['amount'];
        }
        $itog=number_format($itog, 2, '.', '');

        $data=array('clients'=>$clients, 'transactions'=>$transactions, 'itog'=>$itog, 'min_year'=>$min_year);
        return response()->json([
            'success' => true,
            'message' => 'List data transactions',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {

        $client_id=$request->client['id'];

        if ($client_id===0)
        {
            $client = Client::create([
                'name' => $request->client['name'],
                'user_id'=> $request->user()->id
            ]);
            $client_id=$client->id;
        }


        $transaction = Transaction::create([
            'client_id' => $client_id,
            'am_date' => $request->am_date.' '.date('H:i:s'),
            'amount' => $request->amount,
            'user_id'=> $request->user()->id
        ]);


        return response()->json([
            'success' => true,
            'message' => 'transaction created',
            'data' => $transaction
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
       $transaction = Transaction::findOrFail($transaction->id);

       $user_id_cur= $request->user()->id;
       if ($transaction && $transaction->user_id==$user_id_cur) {


        $client_id=$request->client['id'];

        if ($client_id===0)
        {
            $client = Client::create([
                'name' => $request->client['name'],
                'user_id'=> $request->user()->id
            ]);
            $client_id=$client->id;
        }

        $transaction->update([
            'client_id' => $client_id,
            'am_date' => $request->am_date.' '.date('H:i:s'),
            'amount' => $request->amount,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'transaction updated',
            'data' => $transaction
        ], 200);
    }
    return response()->json([
        'success' => false,
        'message' => 'transaction not found'
    ], 404);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction, Request $request)
    {
        $user_id_cur= $request->user()->id;
        if ($transaction && $transaction->user_id==$user_id_cur) {
            $transaction->delete();

            return response()->json([
                'success' => true,
                'message' => 'transaction deleted'
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'transaction not found'
        ], 404);
    }
}
