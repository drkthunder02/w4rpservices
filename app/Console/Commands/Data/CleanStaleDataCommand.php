<?php

namespace App\Console\Commands;

//Internal Library
use Illuminate\Console\Command;

//Library
use Commands\Library\CommandHelper;

//Models
use App\Models\Lookups\AllianceLookup;
use App\Models\Lookups\CharacterLookup;
use App\Models\Lookups\CorporationLookup;
use App\Models\Lookups\ItemLookup;

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

        //Empty the item lookup table
        ItemLookup::truncate();

        //Empty the character lookup table
        CharacterLookup::truncate();

        //Empty the corporation lookup table
        CorporationLookup::truncate();

        //Empty the alliance lookup table
        AllianceLookup::truncate();
    }
}
