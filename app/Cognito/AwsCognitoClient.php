<?php

/*
 * This file is part of AWS Cognito Auth solution.
 *
 * (c) EllaiSys <support@ellaisys.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Cognito;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Ellaisys\Cognito\AwsCognitoClient as CognitoAwsCognitoClient;
use Error;
use Exception as GlobalException;
use PHPUnit\Exception;

class AwsCognitoClient extends CognitoAwsCognitoClient{

    public function register($username, $password, array $attributes = [])
    {

        try {
            //Build payload
            $payload = [
                'ClientId' => $this->clientId,
                'Password' => $password,
                'UserAttributes' => $this->formatAttributes($attributes),
                'Username' => $username,
            ];

            //Add Secret Hash in case of Client Secret being configured
            if ($this->boolClientSecret) {
                $payload = array_merge($payload, [
                    'SecretHash' => $this->cognitoSecretHash($username)
                ]);
            } //End if

            $response = $this->client->signUp($payload);
         //   var_dump($response);
        } catch (CognitoIdentityProviderException $e) {
            // if ($e->getAwsErrorCode() === self::USERNAME_EXISTS) {
            //    throw new GlobalException("User already exists");
            // } //End if

            throw $e;
        } //Try-catch ends

        return (bool)$response['UserSub'];
    } //Function ends

}