<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class getCorps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:getCorps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get corporations in alliance and store in db.';

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
        //
    }
}
