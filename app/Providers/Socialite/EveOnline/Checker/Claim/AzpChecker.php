<?php

namespace App\Providers\Socialite\EveOnline\Checker\Claim;

use Jose\Component\Checker\ClaimChecker;
use Jose\Component\Checker\InvalidClaimException;

/**
 * Class AzpChecker
 * 
 */
class AzpChecker implements ClaimChecker
{
    private const NAME = 'azp';

    /**
     * @var string
     */
    private $client_id;

    /**
     * Azpchecker Constructor
     * 
     * @param string $client_id
     */
    public function __construct(string $client_id) {
        $this->client_id = $client_id;
    }

    /**
     * {@inheritdoc}
     */
    public function checkClaim($value) : void {
        if(!is_string($value)) {
            throw new InvalidClaimException('"azp" must a string.', self::NAME, $value);
        }

        if ($value !== $this->client_id)
            throw new InvalidClaimException('"azp" must match the originating application.', self::NAME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function supportedClaim(): string {
        return self::NAME;
    }
}

?>