<?php

namespace App\Console\Commands\Data;

//Internal Library
use Illuminate\Console\Command;
use Log;

//Libraries
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;

//Models
use App\Models\User\User;
use App\Models\User\UserAlt;
use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;
use App\Models\User\UserPermission;
use App\Models\User\UserRole;
use App\Models\Admin\AllowedLogin;

/**
 * The PurgeUsers command takes care of updating any user changes in terms of login role, as well as purging any users without at least
 * the 'User' role.  This command heavily relies on ESI being available.  If no ESI is available, then the function does nothing, in order to prevent
 * unwanted changes.
 */
class PurgeUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:PurgeUsers';

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
        //Declare some variables
        $esiHelper = new Esi;

        //Setup the esi variable
        $esi = $esiHelper->SetupEsiAuthentication();

        //Get all of the users from the database
        $users = User::all();

        //Get the allowed logins
        $legacy = AllowedLogin::where(['login_type' => 'Legacy'])->pluck('entity_id')->toArray();
        $renter = AllowedLogin::where(['login_type' => 'Renter'])->pluck('entity_id')->toArray();

        //Cycle through all of the users, and either update their role, or delete them.
        foreach($users as $user) {
            //Set the fail bit to false for the next user to check
            $failed = false;

            //Note a screen entry for when doing cli stuff
            printf("Processing character with id of " . $user->character_id . "\r\n");

            //Get the character information
            try {
                $character_info = $esi->invoke('get', '/characters/{character_id}/', [
                    'character_id' => $user->character_id,
                ]);

                $corp_info = $esi->invoke('get', '/corporations/{corporation_id}/', [
                    'corporation_id' => $character_info->corporation_id,
                ]);
            } catch(RequestFailedException $e) {
                Log::warning('Failed to get character information in purge user command for user ' . $user->character_id);
                $failed = true;
            }

            //If the fail bit is still false, then continue
            if($failed === false) {
                //Get the user's role
                $role = UserRole::where(['character_id' => $user->character_id])->first();
                
                //We don't want to modify Admin and SuperUsers.  Admins and SuperUsers are removed via a different process.
                if($role->role != 'Admin') {
                    //Check if the user is allowed to login
                    if(isset($corp_info->alliance_id)) {
                        //Warped Intentions is allowed to login
                        if($corp_info->alliance_id == '99004116') {
                            //If the alliance is Warped Intentions, then modify the role if we need to
                            if($role->role != 'User') {
                                //Upate the role of the user
                                UserRole::where([
                                    'character_id' => $user->character_id,
                                ])->update([
                                    'role' => 'User',
                                ]);

                                //Update the user type
                                User::where([
                                    'character_id' => $user->character_id,
                                ])->update([
                                    'user_type' => 'W4RP',
                                ]);
                            }
                        } else if(in_array($corp_info->alliance_id, $legacy)) {  //Legacy Users
                            if($role->role != 'User') {
                                //Update the role of the user
                                UserRole::where([
                                    'character_id' => $user->character_id,
                                ])->update([
                                    'role' => 'User',
                                ]);

                                //Update the user type
                                User::where([
                                    'character_id' => $user->character_id,
                                ])->update([
                                    'user_type' => 'Legacy',
                                ]);
                            }
                        } else if(in_array($corp_info->alliance_id, $renter)) {  //Renter Users
                            if($role->role != 'Renter') {
                                //Update the role of the user
                                UserRole::where([
                                    'character_id' => $user->character_id,
                                ])->update([
                                    'role' => 'Renter',
                                ]);

                                //Update the user type
                                User::where([
                                    'character_id' => $user->character_id,
                                ])->update([
                                    'user_type' => 'Renter',
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

                            //Delete any alts the user might have registered.
                            $altCount = UserAlt::where(['main_id' => $user->character_id])->count();
                            if($altCount > 0) {
                                UserAlt::where([
                                    'main_id' => $user->character_id,
                                ])->delete();
                            }

                            //Delete the user from the user table
                            User::where([
                                'character_id' => $user->character_id,
                            ])->delete();

                            EsiScope::where([
                                'character_id' => $user->character_id,
                            ])->delete();

                            EsiToken::where([
                                'character_id' => $user->character_id,
                            ])->delete();                        
                        }
                    }
                }
            }
        }   
    }
}
