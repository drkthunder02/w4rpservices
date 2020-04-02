<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

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
        $lines = Storage::get('public/moon_data.txt');
        $lines = explode('\n', $lines);
        dd($lines);
        
        //Create the output file handler       
        Storage::put('public/moon_sql.txt', $formatted);
    }
}
