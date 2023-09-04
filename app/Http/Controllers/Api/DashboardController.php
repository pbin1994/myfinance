<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Client;
use App\Models\Pending;
use App\Models\Debt;

class DashboardController extends Controller
{

    public function mounthly_graph(Request $request)
   {
        
        $to=date('Y-m-d 23:59:59');
        $from=date('Y-m-01 00:00:00', strtotime('-11 month', strtotime($to)));


        $months = Transaction::orderBy('am_year', 'ASC')
        ->orderBy('am_month', 'ASC')
        ->where('user_id', $request->user()->id)
        ->whereBetween('transactions.am_date', [$from, $to])
        ->selectRaw('sum(amount) AS sum_amount, MONTH(am_date) as am_month, YEAR(am_date) as am_year')
        ->groupBy('am_month')
        ->get()->toArray();

        
        $months_count=count($months);
        if ($months_count<12) //если есть пустые месяцы, то забиваем нулями
        {
            $temp_month=intval(date('m',strtotime($from)));
            $temp_year=intval(date('Y',strtotime($from)));
            for ($i=1; $i<=12; $i++)
            {
                $key = array_search($temp_month, array_column($months, 'am_month'));
                if ($key === false) $months[]=array('sum_amount'=>0.00, 'am_month'=>($temp_month), 'am_year'=>($temp_year));
                $temp_month++;
                if ($temp_month>12) { $temp_month=1; $temp_year++; }
            }
        }


        $month_names=array('',"Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь");

        //форматируем
        foreach ($months as $key => $month) {
            $months[$key]['sum_amount']=number_format($month['sum_amount'], 2, '.', ' ');
            $months[$key]['month_name']=$month_names[$month['am_month']].' '.$month['am_year'];
        }

        $data=array('months'=>$months);

        return response()->json([
            'success' => true,
            'message' => 'mounthly_graph data',
            'data' => $data
        ], 200);

   }
    
    public function text_widget(Request $request)
   {
        //за текущий месяц      
        $from=date('Y-m-01 00:00:00');
        $to=date('Y-m-t 23:59:59',strtotime($from));
        $cur_m_amount = Transaction::orderBy('id', 'DESC')
        ->where('user_id', $request->user()->id)
        ->whereBetween('am_date', [$from, $to])
        ->selectRaw('amount')
        ->get()->sum('amount');

        //за текущий месяц прошлого года
        $prev_year=date('Y')-1;    
        $from=date($prev_year.'-m-01 00:00:00');
        $to=date('Y-m-t 23:59:59',strtotime($from));
        $prev_year_m_amount = Transaction::orderBy('id', 'DESC')
        ->where('user_id', $request->user()->id)
        ->whereBetween('am_date', [$from, $to])
        ->selectRaw('amount')
        ->get()->sum('amount');


        //в ожидании  
        $pending_amount = Pending::orderBy('id', 'DESC')
        ->where('user_id', $request->user()->id)
        ->selectRaw('amount')
        ->get()->sum('amount');

        //Долги 
        $debt_amount = Debt::orderBy('id', 'DESC')
        ->where('user_id', $request->user()->id)
        ->selectRaw('amount')
        ->get()->sum('amount');


        //прогноз
        $count_years_stat=3; //количество лет для выведения среднего

        $years_arr=array();
        for ($i=date('Y')-1; $i>date('Y')-1-$count_years_stat; $i--) //делаем запрос по каждому году 
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

        //отдаем только текущий месяц
        $prediction=$prediction[intval(date('m'))];

        //форматируем суммы
        $cur_m_amount=number_format($cur_m_amount, 2, '.', ' ');
        $prev_year_m_amount=number_format($prev_year_m_amount, 2, '.', ' ');
        $pending_amount=number_format($pending_amount, 2, '.', ' ');
        $debt_amount=number_format($debt_amount, 2, '.', ' ');
        $prediction=number_format($prediction, 2, '.', ' ');



        $data=array('cur_m_amount'=>$cur_m_amount, 'prev_year_m_amount'=>$prev_year_m_amount, 'pending_amount'=>$pending_amount, 'debt_amount'=>$debt_amount, 'prediction'=>$prediction);

        return response()->json([
            'success' => true,
            'message' => 'text_widget data',
            'data' => $data
        ], 200);

   }
}
