<?php

namespace App\Jobs\Commands\Structures;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;

//App Library
use App\Library\Structures\StructureHelper;
use App\Library\Esi\Esi;
use Seat\Eseye\Exceptions\RequestionFailedException;

//App Models
use App\Models\Jobs\JobStatus;
use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;
use App\Models\Structure\Structure;
use App\Models\Structure\Service;

class ProcessStructureJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds
     * 
     * @var int
     */
    public $timeout = 3600;

    /**
     * Number of job retries
     */
    public $tries = 3;

    /**
     * Job Variables
     */
    private $charId;
    private $corpId;
    private $totalPages;
    private $currentPage;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($charId, $corpId)
    {
        //Set the connection for the job
        $this->connection = 'redis';

        $this->charId = $charId;
        $this->corpId = $corpId;
        $this->currentPage = 1;
        $this->totalPages = 1;        
    }

    /**
     * Execute the job.
     * The job's task is to get all of the information for a particular structure
     * and store it in the database.  This task can take a few seconds because of the ESI
     * calls required to store the information.  We leave this type of job up to the queue
     * in order to take the load off of the cron job.
     *
     * @return void
     */
    public function handle()
    {
        //Declare variables
        $config = config('esi');
        $sHelper = new StructureHelper($this->charId, $this->corpId);
        $structures = null;

        //ESI Scope Check
        $esiHelper = new Esi;
        $structureScope = $esiHelper->HaveEsiScope($charId, 'esi-universe.read_structures.v1');
        $corpStructureScope = $esiHelper->HaveEsiScope($charId, 'esi-corporations.read_structures.v1');

        //Check scopes
        if($structureScope == false || $corpStructureScope == false) {
            if($structureScope == false) {
                Log::critical("Scope check for esi-universe.read_structures.v1 has failed.");
            }
            if($corpStructureScope == false) {
                Log::critical("Scope check for esi-corporations.read_structures.v1 has failed.");
            }
            return null;
        }

        //Get the refresh token from the database
        $token = $esiHelper->GetRefreshToken($this->charId);
        //Create the esi authentication container
        $esi = $esiHelper->SetupEsiAuthentication($token);

        //Attempt to get the ESI data
        try {
            $structures = $esi->page($this->currentPage)
                              ->invoke('get', '/corporations/{corporation_id}/structures/', [
                                    'corporation_id' => $this->corpId,
                              ]);
        } catch (RequestFailedException $e) {
            Log::critical("Failed to get structure list in ProcessStructureJob");
            return null;
        }

        $this->totalPages = $structures->pages;

        //Truncate the structures and the structure services in order to add the latest data
        Structure::truncate();
        Service::truncate();

        do {
            if($this->currentPage > 1) {
                try {
                    $structures = $esi->page($this->currentPage)
                                      ->invoke('get', '/corporations/{corporation_id}/structures/', [
                                            'corporation_id' => $this->corpId,
                                      ]);
                } catch(RequestFailedException $e) {
                    Log::critical("Failed to get structure list on page" . $this->currentPage . " in ProcessStructureJob.");
                    return null;
                }
            }

            //For each set of data, process the data
            foreach($structures as $structure) {
                $sHelper->ProcessStructure($structure);
            }
            //Increment the current page
            $this->currentPage++;
        } while($this->currentPage <= $this->totalPages);

        //After the job is completed, delete the job
        $this->delete();
    }
}
