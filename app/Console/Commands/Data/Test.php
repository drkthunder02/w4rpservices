<?php

namespace App\Console\Commands\Data;

use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;

//Models
use App\Models\ScheduledTask\ScheduleJob;
use App\Jobs\Commands\Eve\ProcessSendEveMailJob;

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

        $recipient = $config['primary'];
        $recipientType = 'character';
        $subject = "Just A Test for Eseye";
        $sender = $config['primary'];
        $body = "F0KlvSA9vrXWYK0IuMhSAbfaOAMmQO5U2CD69Dn0JOk26B8HnSPPkhSG3JzlawHjbBd16HAIbaawbv9304EaoTRctpu5cnZo0GoHINi3R7pNGi0IZTWKG4EArwWbSujwX9KPvMqGNbcSorrIEslw6neXWW1kcDN0GMcvV6SeoM23cSkK33cAbR4DTeqUXZ9ULuFy31UPXfEcaNzKREbqKPlgChYcGdCyHG1J25qrEmPlOTPI1NQQkh71HvHJTVA7bmTgLEJMdFYHbc3ZzGOB9RFLfhdkGEGl2f3OQNyDAJIKW2mNQVlRVGc3Emvm42czpsH8ojn3BX5nuEFxNfjgue8hhdBIZSKm232U2l0xsPGZOzHvQdYs8bLw7ZQX1drV9qOPnbhgzbFLxEvQoGDquhKAdlo7bhkgoCn5IiY3BbQ5qnKVodymb58gj9Pd67GxjJ8K0854c91KkrJNEOCyiVcqKYqNDtKkB7hgjBLZUKRtWUkOf9j1qIRARoGzTGdqK20yvfaVIWetVqjw5UvzQC2pynHkvIw2X3aD49ghY7UOzXUceKJ8taF4ZaMvW34r5OvyTrjVo4PKV9TylIODmzg1U0s4joxz58f1A6BNp2fCs1YzNOObuMaxGjek47jv2gDgyhQnmi5uaREcGn5AAwgMUc55GPY2jevRTHo9scMqm5amaJUBQ3TgXvFSfS33LxD8xJjdKw7KB06stDQzdjyVb52mAdm5WchOOpGy3EXntBSzsfUHc4XEqql7lKTPLgBzeYxt9EagGP96Li4dABg2MaLuU4i1CWdV49ZdPwOt1OjwNU4QtfR02C6Vw7raCFl3mqWBgLke9O5dC8Lh3ojg7FBATstSuur2n41Rn4YwzGaiWJ3qKwTsJGL3k8PaHEsvwvq56w4Qjt8CqJsmAV1nsfKMFZaVlcbK3PFN5oHaDbQwDh4IVdwA8UPPnrn2wSuugi8QlVyUA8z9iVYMW8OdzHFn98zl7a2Bua5M";

        ProcessSendEveMailJob::dispatch($body, $recipient, $recipientType, $subject, $sender)->onQueue('mail');

        /*
        try {
            $response = $esi->setBody([
                'approved_cost' => 100,
                'body' => "Welcome to this test message.<br>" . $body,
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
        
        var_dump($response);
        dd($response->getErrorCode());
        */
    }
}
