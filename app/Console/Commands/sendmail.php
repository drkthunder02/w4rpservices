<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Library\Finances;

class sendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:sendmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mail to a character';

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
     * Gather the taxes needed and add them together.
     * Send a mail to the character owning the ESI scope with the taxes
     * owed to the holding corp
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
