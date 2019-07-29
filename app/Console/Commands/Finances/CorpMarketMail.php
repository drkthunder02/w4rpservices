<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CorpMarketMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:MarketMail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a mail about a market.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //Create the command helper container
        $task = new CommandHelper('CorpMarketMail');

        //Add entry into the table saying the job is starting
        $task->SetStartStatus();

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
