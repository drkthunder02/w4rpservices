<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

use App\Library\Finances;

use App\Models\EsiScope;
use App\Models\EsiToken;

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
        //Setup the Finances Container
        $finance = new Finances();
        //Get the corps with structures logged in the database
        $structures = DB::table('CorpStructures')->get();
        dd($structures);
        //Get the characters that have the esi-wallet.read_corporation_wallets.v1
        //esi wallet scope
        $characters = DB::table('EsiScopes')->where('scope', 'esi-wallet.read_corporation_wallets.v1')->get();
        //For each structure let's attemp to gather the characters owning the structures and peer into their wallets.
        foreach($structures as $structure) {
            foreach($characters as $char) {
                var_dump($structure->character_id);
                dd($char->character_id);
                if($structure->character_id === $char->character_id) {
                    $finance->GetWalletJournal(1, $char->character_id);
                }
            }
        }
    }
}
