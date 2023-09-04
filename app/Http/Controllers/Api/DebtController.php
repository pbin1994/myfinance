<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDebtRequest;
use App\Http\Requests\UpdateDebtRequest;
use App\Http\Requests\PayDebtRequest;
use App\Models\Debt;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $debts = Debt::orderBy('am_date', 'ASC')
        ->where('debts.user_id', $request->user()->id)
        ->get()->toArray();

        $itog=0;
        foreach ($debts as $key => $pending) {
            $debts[$key]['am_date_formated']=date('d.m.Y',strtotime($pending['am_date']));
            $itog+=$pending['amount'];
        }
        $itog=number_format($itog, 2, '.', '');

        $data=array('debts'=>$debts, 'itog'=>$itog);
        return response()->json([
            'success' => true,
            'message' => 'List data debts',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDebtRequest $request)
    {
        $request->validate([
            'name' => 'required|string',
            'am_date' => 'required|string|max:255',
            'amount' => 'required|integer|min:1',
        ]);

        $debt = Debt::create([
            'name' => $request->name,
            'am_date' => $request->am_date.' '.date('H:i:s'),
            'amount' => $request->amount,
            'user_id'=> $request->user()->id
        ]);


        return response()->json([
            'success' => true,
            'message' => 'Debt created',
            'data' => $debt
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Debt $debt)
    {
        //
    }



   /**
     * Pay the specified resource in storage.
     */
   public function pay(PayDebtRequest $request, Debt $debt)
   {
        $request->validate([
            'amount' => 'required|integer|min:1',
        ]);
        $debt = Debt::find($debt->id);
        $user_id_cur= $request->user()->id;
        if ($debt && $debt->user_id==$user_id_cur) {
            if ($debt->amount<=$request->amount)
            {
                 $debt->delete();
            }
            else
            {
                 $debt->update([
                    'amount' => $debt->amount-$request->amount,
                ]); 
            }


                 return response()->json([
                    'success' => true,
                    'message' => 'debt updated',
                    'data' => $debt
                ], 201);
        }
   }






    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDebtRequest $request, Debt $debt)
    {
         $request->validate([
            'name' => 'string',
            'am_date' => 'required|string|max:255',
            'amount' => 'required|integer|min:1',
        ]);
        $debt = Debt::find($debt->id);

        $user_id_cur= $request->user()->id;
        if ($debt && $debt->user_id==$user_id_cur) {

            $debt->update([
                'name' => $request->name,
                'am_date' => $request->am_date.' '.date('H:i:s'),
                'amount' => $request->amount,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'debt updated',
                'data' => $debt
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'debt not found'
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Debt $debt, Request $request)
    {
         $user_id_cur= $request->user()->id;
         if ($debt && $debt->user_id==$user_id_cur) {
            $debt->delete();

            return response()->json([
                'success' => true,
                'message' => 'debt deleted'
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'debt not found'
        ], 404);
    }
}
