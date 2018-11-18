<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class corpJournal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:corpjournal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grabs the corporation journals and deposit in db.';

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
