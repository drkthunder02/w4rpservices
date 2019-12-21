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
                'groupname' => $newGroup->gname,
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

    /**
     * Check to see if a user is already in a group
     */
    public function UserHasGroup($user, $groupname) {

        //Get the user information
        $user = DokuUser::where(['name' => $user])->first();
        //Get the groups the user is a part of
        $groups = DokuMember::where(['uid' => $user->id])->get();
        //Get all of the groups
        $allGroups = DokuGroupNames::all();

        //cycle through all of the groups, and all of the user's groups to see if the user
        //is part of the group we are seeking
        foreach($allGroups as $all) {
            foreach($groups as $group) {
                //If the group is found, then send back the group has been found
                if($group->gid === $all->id) {
                    return true;
                }
            }
        }
        
        //If we have made it here, then the user does not have the group, therefore,
        //return the user doesn't have the group
        return false;
    }

    /**
     * Add a new user group
     */
    public function AddNewUserGroup($groupName, $description) {
        //Check if the user group already exists
        DokuGroupNames::where(['gname' => $groupName])->count();

        if($count == 0) {
            DokuGroupNames::insert([
                'gname' => $groupName,
                'description' => $description,
            ]);
        }
    }

    /**
     * Delete all traces of a wiki user
     */
    public function DeleteWikiUser($user) {
        //Get the uid of the user as we will need to purge them from the member table as well.
        //the member table holds their permissions.
        $uid = DokuUser::where([
            'name' => $user,
        ])->value('id');
        //Delete the permissions of the user first.
        DokuMember::where([
            'uid' => $uid,
        ])->delete();

        //Delete the user from the user table
        DokuUser::where(['name' => $user])->delete();
    }
}