<?php

/*
 * This file is part of AWS Cognito Auth solution.
 *
 * (c) EllaiSys <support@ellaisys.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

use Ellaisys\Cognito\AwsCognitoClient;

use Exception;
use Ellaisys\Cognito\Exceptions\AwsCognitoException;
use Error;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait VerifiesEmails
{ 

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Support\Collection  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify(Collection $request)
    {
       

        $validator = Validator::make($request->toArray(), [
            'email' => 'required|email', 
            // 'confirmation_code' => 'required|numeric',
        ]);

        // $response = app()->make(AwsCognitoClient::class)->confirmUserSignUp($request['email'], $request['confirmation_code']);
        $response = app()->make(AwsCognitoClient::class)->confirmSignUp($request['email']);
        //var_dump($response);

        
        return response()->json([
            'message' => "User verified successfully"
        ]);
    
    
    } //Function ends


    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Support\Collection  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(Collection $request)
    {
       // var_dump($request["email"]);

        $response = app()->make(AwsCognitoClient::class)->resendToken($request["email"]);
        var_dump($response);

        if ($response == 'validation.invalid_user') {
            return response()->json(['error' => 'cognito.validation.invalid_user'], 400);
        }

        if ($response == 'validation.exceeded') {
            return response()->json(['error' => 'cognito.validation.exceeded'], 400);
        }

        if ($response == 'validation.confirmed') {
            return response()->json(['error' => 'cognito.validation.confirmed'], 400);
        }

        return response()->json(['success' => 'true']);
    } //Function ends
    
} //Trait ends