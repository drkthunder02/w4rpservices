<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

use App\Library\Finances;
use App\Library\Esi;

use App\Models\EsiScope;
use App\Models\EsiToken;
use App\Models\Structure;

class CorpJournal extends Command
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
        //Setup the Finances Container
        $finance = new Finances();
        //Get the corps with structures logged in the database
        $structures = DB::table('CorpStructures')->get();
        //For each structure get the corp journals from the corporation owning the structure
        foreach($structures as $structure) {
            $this->line('Getting corp journal');
            $this->GetJournal($structure->character_id);
        }

    }

    private function GetJournal($charId) {
        $finances = new Finances();
        $finances->GetWalletJournal(1, $charId);
    }
}
