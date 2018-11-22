<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

use App\Library\Finances;

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

        //Get the characters that have the esi-wallet.read_corporation_wallets.v1
        //esi wallet scope
        $characters = DB::table('EsiScopes')->where('scope', 'esi-wallet.read_corporation_wallets.v1')->get();

        foreach($characters as $char) {
            $finance->GetWalletJournal(1, $characters->character_id);
        }
    }
}
