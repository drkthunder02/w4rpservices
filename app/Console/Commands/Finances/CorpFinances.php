<?php

namespace App\Console\Commands;

//Internal Library
use Illuminate\Console\Command;
use Log;

//User Library
use Commands\Library\CommandHelper;
use App\Library\Finances\Helper\FinanceHelper;

//Models
use App\Models\Finances\CorpMarketJournal;
use App\Models\Finances\CorpMarketStructure;

class CorpFinances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:CorpFinances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the corporation finances journal to get the market fees from the master wallet';

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
        //Create the command helper container
        $task = new CommandHelper('CorpFinances');

        //Add entry into the table saying the jobs is starting
        $task->SetStartStatus();

        //Setup the Finances container
        $finance = new FinanceHelper();

        //Get the corporations who have registered for structure markets
        $structures = CorpMarketStructure::all();

        foreach($structures as $structure) {
            $pages = $finance->GetJournalPageCount(1, $structure->character_id);

            for($i = 1; $i <= $pages; $i++) {
                $job = new JobProcessCorpJournal;
                $job->division = 1;
                $job->charId = $structure->character_id;
                $job->corpId = $structure->corporation_id;
                $job->page = $i;
                ProcessCorpJournalJob::dispatch($job)->onQueue('journal');
            }
        }

        //mark the job as finished
        $task->SetStopStatus();
    }
}
