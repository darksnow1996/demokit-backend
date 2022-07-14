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

use App\Cognito\AwsCognitoClient as CognitoAwsCognitoClient;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

use Ellaisys\Cognito\AwsCognitoClient;

use Exception;
use Illuminate\Validation\ValidationException;
use Ellaisys\Cognito\Exceptions\AwsCognitoException;
use Ellaisys\Cognito\Exceptions\NoLocalUserException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Aws\Exception\AwsException;
use Ellaisys\Cognito\AwsCognitoClaim;
use Error;

trait AuthenticatesUsers
{

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Support\Collection  $request
     * @param  \string  $guard (optional)
     * @param  \string  $paramUsername (optional)
     * @param  \string  $paramPassword (optional)
     * @param  \bool  $isJsonResponse (optional)
     *
     * @return mixed
     */
    protected function attemptLogin(Collection $request, string $guard='web', string $paramUsername='email', string $paramPassword='password', bool $isJsonResponse=true)
    {
        try {
            //Get key fields
            $keyUsername = 'email';
            $keyPassword = 'password';
            $rememberMe = $request->has('remember')?$request['remember']:false;

            //Generate credentials array
            $credentials = [
                $keyUsername => $request[$paramUsername],
                $keyPassword => $request[$paramPassword]
            ];
           // var_dump($credentials);

            //Authenticate User
            // $authenticate = app()->make(AwsCognitoClient::class)->authenticate(
            //     $credentials["email"], $credentials["password"]);
              //  return (bool) $register;
            // var_dump($authenticate);
            // $result = new AwsCognitoClaim($authenticate, );
           //$user = $this->createLocalUser($credentials);
           $claim = Auth::guard($guard)->attempt($credentials, $rememberMe);
        //   var_dump($claim);
        //     $authenticate = app()->make(AwsCognitoClient::class)->authenticate(
        //    $credentials["email"], $credentials["password"]);
        // //   //  return (bool) $register;
        //   var_dump($authenticate);


        } catch (NoLocalUserException $e) {
            Log::error('AuthenticatesUsers:attemptLogin:NoLocalUserException');
           throw new NoLocalUserException($e);

            // if (config('cognito.add_missing_local_user_sso')) {
            //     $response = $this->createLocalUser($credentials, $keyPassword);

            //     if ($response) {
            //         return $response;
            //     } //End if
            // } //End if


        } catch (CognitoIdentityProviderException $e) {
            Log::error('AuthenticatesUsers:attemptLogin:CognitoIdentityProviderException');

            return $this->sendFailedCognitoResponse($e, $isJsonResponse, $paramUsername);

        } catch (Exception $e) {
            Log::error('AuthenticatesUsers:attemptLogin:Exception');


           throw $e;
        } //Try-catch ends

      //var_dump($claim);
        return $claim;


    } //Function ends


    protected function attemptRefreshToken(Collection $request)
    {
        try {
            //Get key fields
         //   dd($request['refreshToken']);
          $claim = app()->make(CognitoAwsCognitoClient::class)->refreshTokenAuthenticate(
            $request["refreshToken"], $request["email"]);


        }  catch (CognitoIdentityProviderException $e) {
            Log::error('AuthenticatesUsers:attemptLogin:CognitoIdentityProviderException');


          throw $e;

        } catch (Exception $e) {
            dd("here2");
            Log::error('AuthenticatesUsers:attemptLogin:Exception');


           throw $e;
        } //Try-catch ends

      //var_dump($claim);
        return $claim;


    } //Function ends


    /**
     * Create a local user if one does not exist.
     *
     * @param  array  $credentials
     * @return mixed
     */
    protected function createLocalUser($credentials, string $keyPassword='password')
    {
        $userModel = User::class;
        $user = $userModel::firstOrCreate([
            'email' => $credentials["email"],

        ]);
      //  var_dump("user");
       return $user;
    } //Function ends


    /**
     * Handle Failed Cognito Exception
     *
     * @param CognitoIdentityProviderException $exception
     */
    private function sendFailedCognitoResponse(CognitoIdentityProviderException $exception, bool $isJsonResponse=false, string $paramUsername='email')
    {
        throw ValidationException::withMessages([
            $paramUsername => $exception->getAwsErrorMessage(),
        ]);
    } //Function ends


    /**
     * Handle Generic Exception
     *
     * @param  \Collection $request
     * @param  \Exception $exception
     */
    private function sendFailedLoginResponse(Collection $request, Exception $exception=null, bool $isJsonResponse=false, string $paramUsername='email')
    {
        $message = 'FailedLoginResponse';
        if (!empty($exception)) {
            $message = $exception->getMessage();
           // var_dump($message);
        } //End if

        if ($isJsonResponse) {
            return  response()->json([
                'error' => 'cognito.validation.auth.failed',
                'message' => $message
            ], 400);
        } else {
            return redirect()
                ->back()
                ->withErrors([
                    $paramUsername => $message,
                ]);
        } //End if

        throw new HttpException(400, $message);
    } //Function ends

} //Trait ends
