<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Models\Pending;
use App\Models\Transaction;
use App\Models\Debt;

class GenerateDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate demo data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $demo_user_id=2;

        $client_names=['Исаев Марк', 'Горшков Андрей', 'Демин Илья', 'Жуков Артём', 'Румянцев Давид', 'Шубин Александр', 'Мельникова Алиса', 'Соловьев Алексей', 'Кузнецов Роберт', 'Смирнова Валерия', 'Чеботарев Даниэль', 'Зотов Герман', 'Токарева Вероника', 'Степанова Алисия', 'Новиков Алексей', 'Лебедева Анна', 'Зорин Степан', 'Соколова Наталья', 'Андреев Даниэль', 'Орлов Иван'];

        foreach ($client_names as $client_name) 
        {
            $clients = Client::create([
                'name' => $client_name,
                'user_id'=> $demo_user_id
            ]);
            $this->info($client_name);
        }

        $this->info("Clients added");


        $clients = Client::orderBy('id', 'ASC')
        ->where('user_id', $demo_user_id)
        ->select('id')
        ->get()->toArray();



        //генерируем "в ожидании"
        
        $to=date('Y-m-d H:i:s');
        $from=date('Y-m-d H:i:s', strtotime('-2 month', strtotime($to)));      
        $temp_date=date('Y-m-d H:i:s');
        $pending_max_count=6;
        $pending_count=0;
        for($i_date = strtotime($to); $i_date >= strtotime($from); $i_date -= 86400) {
            $temp_date = date('Y-m-d', $i_date);

            if ($pending_count==$pending_max_count) break;
            if (!rand(0, 5))
            {
                //получаем рандомного клиента
                $client_key=array_rand($clients);
                $client_id=$clients[$client_key]['id'];

                //генерим сумму 
                $amount=rand(1, 20)*500;

                //пишем в базу
                $pending = Pending::create([
                    'client_id' => $client_id,
                    'am_date' => $temp_date.' '.date('H:i:s'),
                    'amount' => $amount,
                    'user_id'=> $demo_user_id
                ]);
                $pending_count++;

                $this->info($temp_date.' '.$client_id.' '.$amount);
            } 

        }
         $this->info("Pendings added");



       //генерируем "долги"
       $debts=['Никите', 'Родиону', 'Тимофею', 'Леониду', 'Ярославу', 'Елисею', 'Глебу', 'Степану', 'Александру', 'Михаилу'];
        $to=date('Y-m-d H:i:s');
        $from=date('Y-m-d H:i:s', strtotime('-2 month', strtotime($to)));      
        $temp_date=date('Y-m-d H:i:s');
        $debt_max_count=3;
        $debt_count=0;
        for($i_date = strtotime($to); $i_date >= strtotime($from); $i_date -= 86400) {
            $temp_date = date('Y-m-d', $i_date);

            if ($debt_count==$debt_max_count) break;
            if (!rand(0, 5))
            {

                //получаем рандомный долг
                $debt_key=array_rand($debts);
                $name='Другу '.$debts[$debt_key];

                //генерим сумму 
                $amount=rand(1, 10)*500;

                //пишем в базу
                $debt = Debt::create([
                    'name' => $name,
                    'am_date' => $temp_date.' '.date('H:i:s'),
                    'amount' => $amount,
                    'user_id'=> $demo_user_id
                ]);
                $debt_count++;

                $this->info($temp_date.' '.$name.' '.$amount);
            } 

        }
        $this->info("Debts added");


        //генерируем "доходы"

        $to=date('Y-m-d H:i:s');
        $from=date('Y-m-d H:i:s', strtotime('-5 year', strtotime($to)));      
        $temp_date=date('Y-m-d H:i:s');
        for($i_date = strtotime($to); $i_date >= strtotime($from); $i_date -= 86400) {
            $temp_date = date('Y-m-d', $i_date);

            if (!rand(0, 3))
            {
                //получаем рандомного клиента
                $client_key=array_rand($clients);
                $client_id=$clients[$client_key]['id'];

                //генерим сумму 
                $amount=rand(1, 25)*500;

                //пишем в базу
                $transaction = Transaction::create([
                    'client_id' => $client_id,
                    'am_date' => $temp_date.' '.date('H:i:s'),
                    'amount' => $amount,
                    'user_id'=> $demo_user_id
                ]);

                $this->info($temp_date.' '.$client_id.' '.$amount);
            } 

        }
        $this->info("Transactions added");


        $this->info("Demo data generated");
    }
}
