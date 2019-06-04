<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Commands\Library\CommandHelper;

class CleanStaleDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:CleanData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old database data';

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
        $command = new CommandHelper;
        $command->CleanJobStatusTable();
    }
}
