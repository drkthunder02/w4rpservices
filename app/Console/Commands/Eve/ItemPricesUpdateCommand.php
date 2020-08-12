<?php

namespace App\Console\Commands\Eve;

use Illuminate\Console\Command;
use Log;

//Library
use Commands\Library\CommandHelper;

//Job
use App\Jobs\Commands\Eve\ItemPricesUpdateJob;

class ItemPricesUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:ItemPriceUpdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update mineral and ore prices';

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
        $task = new CommandHelper('ItemPriceUpdate');
        $task->SetStartStatus();

        ItemPricesUpdateJob::dispatch()->onQueue('default');

        $task->SetStopStatus();
    }
}
