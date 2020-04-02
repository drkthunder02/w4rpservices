<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MoonFormatter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'file:moons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a text file to put into sql to update the moons';

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
        //Create the file handler
        $lines = file('moon_data.txt');
        //Create the output file handler
        $output = file('moon_output.txt');
        dd($lines);
    }
}
