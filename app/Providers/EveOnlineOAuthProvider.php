<?php

namespace App\Providers;

use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;
//use Jose\Component\Core\JWKSet;
//use Jose\Easy\Load;

class EveOnlineOAuthProvider extends AbstractProvider implements ProviderInterface {
    protected $scopeSeparator = ' ';

    /**
     * Get the authentication URL for the provider
     * 
     * @param string $state
     * @return string
     */
    protected function getAuthUrl($state) {
        return $this->buildAuthUrlFromBase('https://login.eveonline.com/oauth/authorize', $state);
    }

    protected function getAuthUrlNew($state) {
        return $this->buildAuthUrlFromBase('https://login.eveonline.com/v2/oauth/authorize', $state);
    }

    /**
     * Get the token URL for the provider
     * 
     * @return string
     */
    protected function getTokenUrl() {
        return 'https://login.eveonline.com/oauth/token';
    }

    protected function getTokenUrlNew() {
        return 'https://login.eveonline.com/v2/oauth/token';
    }

    /**
     * Get the raw user for the given access token
     * 
     * @param string $token
     * @return array
     */
    protected function getUserByToken($token) {
        $reponse = $this->getHttpClient()->get('https://login.eveonline.com/oauth/verify', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        return json_decode($reponse->getBody()->getContents(), true);
    }

    /**
     * Map the raw user array to a Socialite User instance.
     * 
     * @param array $user
     * @return \Laravel\Socialite\Two\User
     */
    protected function mapUserToObject(array $user) {

        return (new User)->setRaw($user)->map([
            'id' => $user['CharacterID'],
            'name' => $user['CharacterName'],
            'nickname' => $user['CharacterName'],
            'owner_hash' => $user['CharacterOwnerHash'],
            'avatar' => 'https://image.eveonline.com/Character/' . $user['CharacterID'] . '_128.jpg',
            //'token_type' => $user['TokenType'],
            //'expires_on' => $user['ExpiresOn'],
        ]);
    }

    /**
     * @param string $code
     * @return array
     */
    protected function getTokenFields($code) {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }

    /**
     * @param string $access_token
     * @return array
     * @throws \Exception
     */
    private function validateJwtToken(string $access_token): array {
        $scopes = array();

        // pulling JWK sets from CCP
        $sets = $this->getJwkSets();

        // loading JWK Sets Manager
        $jwk_sets = JWKSet::createFromKeyData($sets);

        // attempt to parse the JWT and collect payload
        $jws = Load::jws($access_token)
            ->algs(['RS256', 'ES256', 'HS256'])
            ->exp()
            ->iss('login.eveonline.com')
            ->header('typ', new TypeChecker(['JWT'], true))
            ->claim('scp', new ScpChecker($scopes))
            ->claim('sub', new SubEveCharacterChecker())
            ->claim('azp', new AzpChecker(config('esi.eseye_client_id')))
            ->claim('name', new NameChecker())
            ->claim('owner', new OwnerChecker())
            ->keyset($jwk_sets)
            ->run();

        return $jws->claims->all();
    }
}

?>