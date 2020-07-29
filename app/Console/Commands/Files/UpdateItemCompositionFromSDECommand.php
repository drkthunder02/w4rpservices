<?php

namespace App\Console\Commands\Files;

//Internal Stuff
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class UpdateItemCompositionFromSDECommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sde:update:ItemCompositions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates item compositions from sql file.';

    /**
     * The SDE storage path
     * 
     * @var
     */
    protected $storage_path;

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
     * Query the sql file for the related database information
     *
     * @return mixed
     */
    public function handle()
    {
        //Start by warning the user about the command which will be run
        $this->comment('Warning! This Laravel command uses exec() to execute a ');
        $this->comment('mysql shell command to import an extracted dump. Due');
        $this->comment('to the way the command is constructed, should someone ');
        $this->comment('view the current running processes of your server, they ');
        $this->comment('will be able to see your SeAT database users password.');
        $this->line('');
        $this->line('Ensure that you understand this before continuing.');

        //Test we have valid database parameters
        DB::connection()->getDatabaseName();

        //Warn the user about the operation to begin
        if (! $this->confirm('Are you sure you want to update to the latest EVE SDE?', true)) {
            $this->warn('Exiting');

            return;
        }

        $fileName = $this->getSde();
        $this->importSde($fileName);

    }

    /**
     * Download the EVE Sde from Fuzzwork and save it
     * in the storage_path/sde folder
     */
    public function getSde() {


        return $fileName;
    }

    /**
     * Extract the SDE file downloaded and run the MySQL command to import the table into the database
     */
    public function importSde($fileName) {
        $import_command = 'mysql -u username -p password database < ' . $file;

        //run the command
        exec($import_command, $output, $exit_code);

        if($exit_code !== 0) {
            $this->error('Warning: Import failed with exit code ' .
                    $exit_code . ' and command outut: ' . implode('\n', $output));
        }
    }
}
