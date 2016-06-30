<?php
namespace App\Git\Github;

use App\Git\Github\Exception\InvalidSignatureException;

/**
 * Hook signature validator.
 */
class WebHookEventValidator
{
    /**
     * @param string $userBody
     * @param string $userSignature
     * @param string $secret
     * @throws \App\Git\Github\Exception\InvalidSignatureException
     */
    public static function validate($userBody, $userSignature, $secret)
    {
        list($userSignatureAlgo, $userSignatureHash) = explode('=', $userSignature);
        $signature = hash_hmac($userSignatureAlgo, $userBody, $secret);

        if (!hash_equals($signature, $userSignatureHash)) {
            throw new InvalidSignatureException('Signatures didn\'t match!');
        }
    }
}
