<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Client;

class StatsController extends Controller
{



   public function amount_by_client(Request $request)
   {


        $clients = Client::orderBy('name', 'ASC')->where('user_id', $request->user()->id)->get();

        $transactions_min_year = Transaction::orderBy('am_date', 'ASC')
        ->where('transactions.user_id', $request->user()->id)
        ->limit(1)->get();
        $min_year=date('Y');
        if (isset($transactions_min_year[0])) $min_year=date('Y', strtotime($transactions_min_year[0]->am_date));

        if ($request->year=='all') 
        {
            $from=date($min_year.'-01-01 00:00:00');
            $to=date('Y-12-31 23:59:59');
        }
        else
        {
            $from=date($request->year.'-01-01 00:00:00');
            $to=date('Y-12-t 23:59:59',strtotime($from));
        }

        $client_data=array();
        if ($request->year=='all') 
        {
            $client_data_temp  = Transaction::orderBy('am_year', 'ASC')
            ->where('user_id', $request->user()->id)
            ->where('client_id', $request->client_id)
            ->selectRaw('sum(amount) AS sum_amount, YEAR(am_date) as am_year')
            ->groupBy('am_year')
            ->get()->toArray();

            for ($i_2=$min_year; $i_2<=date('Y'); $i_2++)
            {
                $key = array_search($i_2, array_column($client_data_temp, 'am_year'));
                if ($key === false) $client_data[]=array('sum_amount'=>"0.00", 'am_year'=>$i_2);
                else
                {
                    $client_data[]=$client_data_temp[$key];
                }

            }

              foreach ($client_data as $key => $client_data_item) {
                $client_data[$key]['sum_amount']=number_format($client_data_item['sum_amount'], 2, '.', ' ');
             }

        } else {
            $client_data_temp = Transaction::orderBy('am_month', 'ASC')
            ->where('user_id', $request->user()->id)
            ->where('client_id', $request->client_id)
            ->whereBetween('transactions.am_date', [$from, $to])
            ->selectRaw('sum(amount) AS sum_amount, MONTH(am_date) as am_month')
            ->groupBy('am_month')
            ->get()->toArray();


            for ($i_2=1; $i_2<=12; $i_2++)
            {
                $key = array_search($i_2, array_column($client_data_temp, 'am_month'));
                if ($key === false) $client_data[]=array('sum_amount'=>"0.00", 'am_month'=>$i_2);
                else
                {
                    $client_data[]=$client_data_temp[$key];
                }

            }

             $month_names=array('',"Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь");
             foreach ($client_data as $key => $client_data_item) {
                $client_data[$key]['sum_amount']=number_format($client_data_item['sum_amount'], 2, '.', ' ');
                $client_data[$key]['month_name']=$month_names[$client_data_item['am_month']];
             }



        }




        $data=array('data'=>$client_data, 'min_year'=>$min_year, 'clients'=>$clients);

        return response()->json([
            'success' => true,
            'message' => 'List by_client',
            'data' => $data
        ], 200);

   }




   public function amount_by_year(Request $request)
   {

        $transactions_min_year = Transaction::orderBy('am_date', 'ASC')
        ->where('transactions.user_id', $request->user()->id)
        ->limit(1)->get();
        $min_year=date('Y');
        if (isset($transactions_min_year[0])) $min_year=date('Y', strtotime($transactions_min_year[0]->am_date));

        $from=date($request->year.'-01-01 00:00:00');
        $to=date('Y-12-t 23:59:59',strtotime($from));

        $months = Transaction::orderBy('am_month', 'ASC')
        ->where('user_id', $request->user()->id)
        ->whereBetween('transactions.am_date', [$from, $to])
        ->selectRaw('sum(amount) AS sum_amount, MONTH(am_date) as am_month')
        ->groupBy('am_month')
        ->get()->toArray();

        $months_count=count($months);
        if ($months_count<12) //если есть пустые месяцы, то забиваем нулями
        {
            for ($i=1; $i<=12; $i++)
            {
                $key = array_search($i, array_column($months, 'am_month'));
                if ($key === false) $months[]=array('sum_amount'=>0.00, 'am_month'=>($i));
            }
        }

      //предсказание 
     $count_years_stat=3; //количество лет для выведения среднего

     $years_arr=array();
     for ($i=$request->year-1; $i>$request->year-1-$count_years_stat; $i--) //делаем запрос по каждому году 
     {

        $from=date($i.'-01-01 00:00:00');
        $to=date('Y-12-t 23:59:59',strtotime($from));

        $months_temp = Transaction::orderBy('am_month', 'ASC')
        ->where('user_id', $request->user()->id)
        ->whereBetween('transactions.am_date', [$from, $to])
        ->selectRaw('sum(amount) AS sum_amount, MONTH(am_date) as am_month')
        ->groupBy('am_month')
        ->get()                        
        ->keyBy('am_month')->toArray();

        if (count($months_temp)) //если год не пустой, то добиваем нулям пустые месяцы если необходимо
        {
            $years_arr[$i]=$months_temp;
            for ($i_2=1; $i_2<=12; $i_2++)
            {
             if (!isset($years_arr[$i][$i_2])) $years_arr[$i][$i_2]=array('sum_amount'=>0.00, 'am_month'=>$i_2);
            }
            
         }
         else
         {    
            unset($years_arr[$i]); //пустой год удаляем
        }
    }

    $count_years_stat=count($years_arr);  //количество оставшихся лет
    $prediction=array();



    for ($i_2=1; $i_2<=12; $i_2++) //забиваеем массив нулями 
    {
       $prediction[$i_2]=0;
    }

    if ($count_years_stat==1) //если остался один год, то просто переносим данные из этог года в масив 
    {

        $firstKey_year = array_key_first($years_arr);

        for ($i_2=1; $i_2<=12; $i_2++)
        {
         $prediction[$i_2]=$years_arr[$firstKey_year][$i_2]['sum_amount'];
        }
    }
    elseif ($count_years_stat!=0) //если больше одного то считаем среднее
    {
        $firstKey_year = array_key_first($years_arr);
         for ($i_2=1; $i_2<=12; $i_2++)
        {
          
            foreach ($years_arr as $key => $year) {
                 $prediction[$i_2]+=$year[$i_2]['sum_amount'];
            }
            $prediction[$i_2]=$prediction[$i_2]/$count_years_stat;
            $prediction[$i_2]=round($prediction[$i_2], 2);
        }

    }




    //за прошлый год
    $prev_year_arr=array();
    $prev_year=$request->year-1;

    for ($i_2=1; $i_2<=12; $i_2++)
    {
     $prev_year_arr[$i_2]=0;
    }


    if (isset($years_arr[$prev_year]))
    {
        for ($i_2=1; $i_2<=12; $i_2++)
        {
            $prev_year_arr[$i_2]=$years_arr[$prev_year][$i_2]['sum_amount'];
       }    
    }


    $month_names=array('',"Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь");

    //добавляем в основной массив
    foreach ($months as $key => $month) {
        $months[$key]['prev_year']=$prev_year_arr[$month['am_month']];
        $months[$key]['prediction']=$prediction[$month['am_month']];

        $months[$key]['prev_year']=number_format($months[$key]['prev_year'], 2, '.', ' ');
        $months[$key]['prediction']=number_format($months[$key]['prediction'], 2, '.', ' ');
        $months[$key]['sum_amount']=number_format($month['sum_amount'], 2, '.', ' ');
        $months[$key]['month_name']=$month_names[$month['am_month']];
    }

    array_multisort( array_column($months, "am_month"), SORT_ASC, SORT_NUMERIC, $months );


$data=array('data'=>$months, 'min_year'=>$min_year);

return response()->json([
    'success' => true,
    'message' => 'List amount_by_year',
    'data' => $data,
], 200);

}



public function amount_by_all_years(Request $request)
{


    $years = Transaction::orderBy('am_year', 'ASC')
    ->where('user_id', $request->user()->id)
    ->selectRaw('sum(amount) AS sum_amount, YEAR(am_date) as am_year')
    ->groupBy('am_year')
    ->get()->toArray();


    foreach ($years as $key => $year) {
        if ($year['am_year']!=date('Y'))   $years[$key]['am_avg']=round($year['sum_amount']/12, 2);
            else //для текущего года учитываем до текущего месяца
            {

                $prev_month=date('m')-1;
                if ($prev_month<1) { $years[$key]['am_avg']=0; continue;} //если первый месяц - будет деление на ноль

                $from=date('Y-m-01 00:00:00');
                $to=date('Y-m-t 23:59:59',strtotime($from));
                $cur_m_amount = Transaction::orderBy('id', 'DESC')
                ->where('user_id', $request->user()->id)
                ->whereBetween('transactions.am_date', [$from, $to])
                ->selectRaw('amount')
                ->get()->sum('amount');

                $years[$key]['am_avg']=($year['sum_amount']-$cur_m_amount)/$prev_month;
                $years[$key]['am_avg']=round($years[$key]['am_avg'], 2);
            }

        }



          foreach ($years as $key => $item) {
            $years[$key]['am_avg']=number_format($item['am_avg'], 2, '.', ' ');
            $years[$key]['sum_amount']=number_format($item['sum_amount'], 2, '.', ' ');
        }


        $data=array('data'=>$years);

        return response()->json([
            'success' => true,
            'message' => 'List amount_by_all_years',
            'data' => $data
        ], 200);



    }



    public function top_clients(Request $request)
    {

        $transactions_min_year = Transaction::orderBy('am_date', 'ASC')
        ->where('transactions.user_id', $request->user()->id)
        ->limit(1)->get();
        $min_year=date('Y');
        if (isset($transactions_min_year[0])) $min_year=date('Y', strtotime($transactions_min_year[0]->am_date));

        if ($request->year=='all') 
        {
            $from=date($min_year.'-01-01 00:00:00');
            $to=date('Y-12-31 23:59:59');
        }
        else
        {
            $from=date($request->year.'-01-01 00:00:00');
            $to=date('Y-12-t 23:59:59',strtotime($from));
        }




        $data = Transaction::orderBy('sum_amount', 'DESC')
        ->where('transactions.user_id', $request->user()->id)
        ->whereBetween('transactions.am_date', [$from, $to])
        //->select('transactions.*', 'clients.name AS client_name')
        ->selectRaw('sum(transactions.amount) AS sum_amount, clients.name  AS client_name')
        ->groupBy('transactions.client_id')
        ->join('clients', 'transactions.client_id', '=', 'clients.id')
        ->limit(10)
        ->get()->toArray();


        foreach ($data as $key => $item) {
            $data[$key]['sum_amount']=number_format($item['sum_amount'], 2, '.', ' ');
        }


        $data=array('data'=>$data, 'min_year'=>$min_year);

        return response()->json([
            'success' => true,
            'message' => 'List top_clients',
            'data' => $data
        ], 200);
    }
}
