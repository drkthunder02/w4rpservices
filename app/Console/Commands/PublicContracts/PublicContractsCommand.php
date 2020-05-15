<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PublicContractsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:PublicContracts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the public contracts in a region';

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
        $regions = [
            'Immensea' => 10000025,
            'Catch' => 10000014,
            'Tenerifis' => 10000061,
            'The Forge' => 10000002,
            'Impass' => 10000031,
            'Esoteria' => 10000039,
            'Detorid' => 10000005,
            'Omist' => 10000062,
            'Feythabolis' => 10000056,
            'Insmother' => 10000009,
        ];

        foreach($regions as $key => $value) {
            PublicContractsJob::dispatch($value);
        }
    }
}
