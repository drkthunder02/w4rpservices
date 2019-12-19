<?php

namespace App\Library\Wiki;

//Internal Library
use Carbon\Carbon;

//Library
use App\Library\Lookups\LookupHelper;

//Models
use App\Models\User\User;
use App\Models\User\UserRole;
use App\Models\User\UserPermission;
use App\Models\Doku\DokuGroupNames;
use App\Models\Doku\DokuMember;
use App\Models\Doku\DokuUser;
use App\Models\Admin\AllowedLogin;

class WikiHelper {

    /**
     * Check whether the user is allwoed or not allowed
     */
    public function AllowedUser($user) {
        //Declare some variables
        $purge = true;

        //Declare helper class
        $lookup = new LookupHelper;

        //Get the allowed logins list from the database
        $legacy = AllowedLogin::where(['login_type' => 'Legacy'])->pluck('entity_id')->toArray();
        $renter = AllowedLogin::where(['login_type' => 'Renter'])->pluck('entity_id')->toArray();

        $charIdTemp = User::where(['name' => $user])->get(['character_id']);

        //Set the character id
        $charId = $charIdTemp[0]->character_id;

        //Set the corp id
        $char = $lookup->GetCharacterInfo($charId);
        $corpId = $char->corporation_id;

        //Set the alliance id
        $corp = $lookup->GetCorporationInfo($corpId);
        $allianceId = $corp->alliance_id;

        if(in_array($allianceId, $legacy) || in_array($allianceId, $renter) || $allianceId == 99004116) {
            $purge = false;
        } else {
            $purge = true;
        }

        return $purge;
    }

    /**
     * Add a user to a particular group
     */
    public function AddUserToGroup($name, $groupName) {
        //Get the group information
        $groups = DokuGroupNames::all();

        //Check if the user already belongs to the group
        $userGroups = DokuMember::where(['groupname' => $groupName])->count();
        if($count > 0) {
            //If the count is greater than zero then we don't need to do anything,
            //just return false to indicate nothing was changed
            return false;
        } else {
            //If the person is not part of the group, then we need to add them to the group
            
            //Get the uid from the user
            $user = DokuUser::where(['name' => $name])->first();
            
            //Get the group the person needs to be added to.
            $newGroup = DokuGroupNames::where(['groupname' => $groupName])->first();
            //Add the user to the group
            DokuMember::insert([
                'uid' => $user->id,
                'gid' => $newGroup->id,
            ]);

            //Return true saying we have inserted the user into the group
            return true;
        }
    }

    /**
     * Remove a user from a particular group
     */
    public function RemoveUserFromGroup($name, $groupName) {
        
        $user = DokuUser::where(['name' => $name])->first();

        $group = DokuGroupNames::where(['groupname' => $groupName])->first();

        DokuMember::where([
            'uid' => $user->id,
            'gid' => $group->gid,
        ])->delete();
    }

    /**
     * Remove a user from all groups
     */
    public function RemoveUserFromAllGroups($name) {
        $user = DokuUser::where(['name' => $name])->first();

        DokuMember::where([
            'uid' => $user->id,
        ])->delete();
    }
}