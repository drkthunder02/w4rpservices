<?php

namespace App\Console\Commands;

//Internal Library
use Illuminate\Console\Command;

//Libraries
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

//Models
use App\Models\User\User;
use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;
use App\Models\User\UserPermission;
use App\Models\User\UserRole;
use App\Models\Admin\AllowedLogin;

class PurgeUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:PurgeUsers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update and purge users from the database.';

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
        //Setup the esi variable
        $esi = new Eseye();

        //Attempt to get the server status.  Don't want to do anything if the server is down for some reason.
        try {
            $status = $esi->setQueryString([
                'datasource' => 'tranquility',
            ])->invoke('get', '/status/');
        } catch(RequestFailedException $e) {
            return;
        }

        //Get all of the users from the database
        $users = User::all();

        //Get the allowed logins
        $legacy = AllowedLogin::where(['login_type' => 'Legacy'])->pluck('entity_id')->toArray();
        $renter = AllowedLogin::where(['login_type' => 'Renter'])->pluck('entity_id')->toArray();

        //Cycle through all of the users, and either update their role, or delete them.
        foreach($users as $user) {
            //Set the fail bit to false
            $failed = false;

            //Get the character information
            try {
                $character_info = $esi->invoke('get', '/characters/{character_id}/', [
                    'character_id' => $user->character_id,
                ]);

                $corp_info = $esi->invoke('get', '/corporations/{corporation_id/', [
                    'corporation_id' => $character_info->corporation_id,
                ]);
            } catch(RequestFailedException $e) {
                Log::warning('Failed to get character information in purge user command for user ' . $user->character_id);
                $failed = true;
            }

            //If the fail bit is still false, then continue
            if($failed === false) {
                //Get the user's role
                $role = UserRole::where(['character_id'])->first();

                //Check if the user is allowed to login
                if(isset($corp_info->alliance_id)) {
                    //Warped Intentions is allowed to login
                    if($corp_info->alliance_id == '99004116') {
                        //If the role is not Warped Intentions, then modify the role
                        if($role != 'W4RP') {
                            UserRole::where([
                                'character_id' => $user->character_id,
                            ])->update([
                                'role' => 'W4RP',
                            ]);
                        }
                    } else if(in_array($corp_info->alliance_id, $legacy)) {  //Legacy Users
                        if($role != 'Legacy') {
                            UserRole::where([
                                'character_id' => $user->character_id,
                            ])->update([
                                'role' => 'Legacy',
                            ]);
                        }
                    } else if(in_array($corp_info->alliance_id, $renter)) {  //Renter Users
                        if($role != 'Renter') {
                            UserRole::where([
                                'character_id' => $user->character_id,
                            ])->update([
                                'role' => 'Renter',
                            ]);
                        }
                    } else {
                        //If the user is part of no valid login group, then delete the user.
                        //Delete all of the permissions first
                        UserPermission::where([
                            'character_id' => $user->character_id,
                        ])->delete();
                        
                        //Delete the user's role
                        UserRole::where([
                            'character_id' => $user->character_id,
                        ])->delete();

                        //Delete the user from the user table
                        User::where([
                            'character_id' => $user->character_id,
                        ])->delete();
                    }
                }
            }
        }   
    }
}
