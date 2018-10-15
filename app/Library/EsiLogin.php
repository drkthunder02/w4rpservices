<?php
/* 
 *  W4RP Services
 *  GNU Public License
 */

namespace App\Library;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;

class EsiLogin {
    
    private $characterId;
    private $characterName;
    private $tokenType;
    
    private $corporationId;
    private $corporationName;
    
    private $allianceId;
    private $allianceName;
    
    private $logged;
    
    private $refreshToken;
    private $refreshTokenExpiry;
    private $accessToken;
    
    private $clientId;
    private $secretKey;
    private $useragent;
    private $scope;
    
    public function __construct($client = null, $secret = null, $useragent = null) {
        if($client == null || $secret == null || $useragent == null) {
            //Parse the data for the ESI Configuration file
            $this->clientId = env('ESI_CLIENT_ID');
            $this->secretKey = env('ESI_SECRET_KEY');
            $this->useragent = env('ESI_USERAGENT');
            $this->scope = env('ESI_SCOPES');
            //$this->clientId = $fileEsi['client_id'];
            //$this->secretKey = $fileEsi['secret'];
            //$this->useragent = $fileEsi['useragent'];
        } else {
            $this->clientId = $client;
            $this->secretKey = $secret;
            $this->userAgent = $useragent; 
        }
    }
    
    public function GetCharacterId() {
        return $this->characterId;
    }
    
    public function GetCharacterName() {
        return $this->characterName;
    }
    
    public function GetCorporationId() {
        return $this->corporationId;
    }
    
    public function GetCorporationName() {
        return $this->corporationName;
    }
    
    public function GetAllianceId() {
        return $this->allianceId;
    }
    
    public function GetAllianceName() {
        return $this->allianceName;
    }
    
    public function GetAccessToken() {
        return $this->accessToken;
    }
    
    public function GetRefreshToken() {
        return $this->refreshToken;
    }
    
    public function GetRefreskTokenExpiry() {
        return $this->refreshTokenExpiry;
    }

    public function SetAccessToken($access) {
        $this->accessToken = $access;
    }
    
    public function SetRefreshtoken($refresh) {
        $this->refreshToken = $refresh;
    }
    
    public function SetRefreshTokenExpiry($expire) {
        $this->refreshTokenExpiry = $expire;
    }
    
    public function ESIStateMachine($state) {
        
        switch($state) {
            case 'new':
                return $this->DisplayLoginButton();
                break;
            case 'eveonlinecallback':
                $this->VerifyCallback();
                if($this->logged == true) {
                    return 'logged';
                } else {
                    return 'notlogged';
                }
                break;
            default:
                $this->UnsetState();
                break;
        }
    }
    
    public function VerifyCallback() {
        if($this->CheckState() == 'okay') {
            $this->RetrieveAccessToken();
            $this->RetrieveCharacterId();
            
            //Get all the information we might need, and store it
            $char = $this->GetESIInfo($this->characterId, 'Character', $this->useragent);
            $this->characterName = $char['name'];
            
            $corp = $this->GetESIInfo($char['corporation_id'], 'Corporation', $this->useragent);
            $this->corporationId = $char['corporation_id'];
            $this->corporationName = $corp['name'];
            
            if(isset($corp['alliance_id'])) {
                $ally = $this->GetESIInfo($corp['alliance_id'], 'Alliance', $this->useragent);
                $this->allianceId = $corp['alliance_id'];
                $this->allianceName = $ally['name'];
            }
        } else {
            $this->logged = false;
        }
        
        if($this->characterId != null) {
            $this->logged = true;
        } else {
            $this->logged = false;
        }
    }
    
    public function DisplayLoginButton($state) {
        $html = "";
        $html .= "<div class=\"container\">";
        $html .= "<br><br><br>";
        $html .= "<div class=\"jumbotron\">";
        //$html .= "<h1><p align=\"center\">Warped Intentions Services Login</p></h1>";
        //$html .= "<br>";
        //$html .= "<p align=\"center\">One stop shop for the alliance services.</p>";
        //$html .= "<br>";
        $html .= "<p align=\"center\">";
        $html .= "<a href=\"https://login.eveonline.com/oauth/authorize/?response_type=code&redirect_uri=";
        $html .= env('ESI_CALLBACK_URI');
        $html .= "&client_id=" . $this->clientId;
        $html .= "&scope=" . urlencode($this->scope);
        $html .= "&state=";
        $html .= $state . "\">";
        $html .= "<img src=\"images/EVE_SSO_Login_Buttons_Large_Black.png\">";
        $html .= "</a>";
        $html .= "</p>";
        $html .= "</div>";
        $html .= "</div>";
        return $html;
    }
    
    public function CheckState($newState) {
        if($newState != session('state')) {
            $this->UnsetState();
            return false;
        } else {
            return true;
        }
    }
    
    public function UnsetState() {
        Session::forget('state');
    }
    
    public function RetrieveCharacterId() {
        $url = 'https://login.eveonline.com/oauth/verify';
        $header = 'Authorization: Bearer ' . $this->accessToken;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $result = curl_exec($ch);
        $data = json_decode($result, true);
        
        $this->characterId =  $data['CharacterID'];
        $this->characterName = $data['CharacterName'];
        $this->tokenType = $data['TokenType'];
    }
    
    public function RetrieveAccessToken() {
        Session::forget('key');
        $url = 'https://login.eveonline.com/oauth/token';
        $header = 'Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->secretKey);
        $fields_string='';
        $fields = array(
            'grant_type' => 'authorization_code',
            'code' => $_GET['code']
        );
        foreach($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string . '&');
        //Initialize the curl channel
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $result = curl_exec($ch);
        curl_close($ch);            
        $data = json_decode($result, true);
        $this->accessToken = $data['access_token'];
        $this->refreshToken = $data['refresh_token'];
        $this->refreshTokenExpiry = time() + $data['expires_in'];
    }

    public function RefreshAccess() {
        $url = 'https://login.eveonline.com/oauth/token';
        $header = 'Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->secretKey);
        $fields_string = '';
        $fields = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->refreshToken
        );
        
        foreach($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');
        //Initialize the cURL connection
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $result = curl_exec($ch);
        //Get the resultant data from the curl call in an array format
        $data = json_decode($result, true);
        //Modify the variables of the class
        $this->refreshToken = $data['refresh_token'];
        $this->refreshTokenExpiry = now() + $data['expires_in'];
        $this->accessToken = $data['access_token'];        
    }

    public function GetESIInfo($id, $type, $useragent = null) {
        if($useragent == null) {
            $useragent = $this->useragent;
        }
        $url = $this->BuildSingleUrl($type, $id);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $result = curl_exec($ch);
        //Check for a curl error
        if(curl_error($ch)) {
            return null;
        } else {
            curl_close($ch);
            $data = json_decode($result, true);
            return $data;
        }
    }

    private function BuildSingleUrl($type, $id) {
        $firstPart = 'https://esi.tech.ccp.is/latest/';
        $lastPart = '/?datasource=tranquility';
        
        if($type == 'Character') {
            $url = $firstPart . 'characters/' . $id . $lastPart;
        } else if ($type == 'Corporation') {
            $url = $firstPart . 'corporations/' . $id . $lastPart;
        } else if ($type == 'Alliance') {
            $url = $firstPart . 'alliances/' . $id . $lastPart;
        }
        
        return $url;
    }
}