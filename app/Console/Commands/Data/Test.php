<?php

namespace App\Console\Commands\Data;

use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;

//Models
use App\Models\ScheduledTask\ScheduleJob;

//Library
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use App\Library\Helpers\FinanceHelper;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test ESI stuff.';

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
     * @return int
     */
    public function handle()
    {
        $esiHelper = new Esi;
        $config = config('esi');

        $refreshToken = $esiHelper->GetRefreshToken($config['primary']);
        $esi = $esiHelper->SetupEsiAuthentication($refreshToken);

        try {
            $response = $esi->setBody([
                'approved_cost' => 100,
                'body' => "Welcome to this test message.",
                'recipients' => [[
                    'recipient_id' => $config['primary'],
                    'recipient_type' => 'character',
                ]],
                'subject' => 'Just a Test',
            ])->invoke('post', '/characters/{character_id}/mail/', [
                'character_id' => $config['primary'],
            ]);
        } catch(RequestFailedException $e) {
            return null;
        }

        dd($response->response_code);
    }
}
