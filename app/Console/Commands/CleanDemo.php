<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Models\Pending;
use App\Models\Transaction;
use App\Models\Debt;



class CleanDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean demo data';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $demo_user_id=2;

        $clients = Client::where('user_id', $demo_user_id)->delete();
        $this->info("Clients removed");

        $clients = Pending::where('user_id', $demo_user_id)->delete();
        $this->info("Pendings removed");

        $clients = Transaction::where('user_id', $demo_user_id)->delete();
        $this->info("Transactions removed");

        $clients = Debt::where('user_id', $demo_user_id)->delete();
        $this->info("Debts removed");

        $this->info("Demo data cleaned");
    }
}
