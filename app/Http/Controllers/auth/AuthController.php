<?php

namespace App\Http\Controllers\auth;

use App\Traits\RegistersUsers;
use Ellaisys\Cognito\AwsCognitoClaim;
//use App\Traits\AuthenticatesUsers;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApiRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Traits\AuthenticatesUsers;
use App\Traits\ChangePasswords;
use App\Traits\VerifiesEmails;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Auth;
use Ellaisys\Cognito\Auth\ResetsPasswords;
use Ellaisys\Cognito\Auth\SendsPasswordResetEmails;
use Ellaisys\Cognito\Exceptions\NoLocalUserException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log as FacadesLog;
use Ellaisys\Cognito\AwsCognitoClient;
use Illuminate\Http\Response;

class AuthController extends Controller
{
  //  use AwsCognitoClient;
  //use AuthenticatesUsers;
    use RegistersUsers;
    use AuthenticatesUsers;
//     use ResetsPasswords;
//    use VerifiesEmails;
//     use ChangePasswords;
  //  use AwsCognitoClaim;
    public function register(RegisterRequest $request){
        try{

        // $validator = $request->validate([
        //     'name' => 'required|max:255',
        //     'email' => 'required|email|max:64|unique:users',
        //     'password' => 'required|min:6|max:64',
        // ]);

        //Create credentials object
        $collection = collect($request);
        $data = $collection->only('name', 'email', 'password'); //passing 'password' is optional.


   // var_dump($data["email"]);
        $cognitoRegistered=$this->createCognitoUser($data);
      // var_dump($cognitoRegistered);
        //Register User in cognito
        if ($cognitoRegistered) {
                $user = User::firstOrCreate([
        'email' => $data["email"],
        'name' => $data["name"]

       ]);
            return response()->json([
                'message' => 'registration succesful',
                'data' => $cognitoRegistered
            ], Response::HTTP_OK);

        } //End if
    }
    catch(CognitoIdentityProviderException $e){
      throw $e;
        if ($e->getAwsErrorCode() === "UsernameExistsException") {
            return response()->json([

                'error' =>"username already exists"
              ], Response::HTTP_UNAUTHORIZED);
            } //End if
        return response()->json([

            'error' =>$e->getAwsErrorCode()
          ], Response::HTTP_UNAUTHORIZED);

    }
    catch(Exception $e){
        return response()->json([
                    'error' =>$e->getMessage()
                  ], Response::HTTP_UNAUTHORIZED);

    }
}



    public function login(LoginRequest $request)
    {
        try{
              //Convert request to collection
        $collection = collect($request->all());
        //Authenticate with Cognito Package Trait (with 'api' as the auth guard)
        if ($claim = $this->attemptLogin($collection, 'api', 'email', 'password', true)) {
        // var_dump($claim);
            if ($claim instanceof AwsCognitoClaim) {
                return response()->json([
                    'user' => $claim->getUser(),
                    'token' => $claim->getToken()
                ], Response::HTTP_OK);
         }

         $claimMessage = $claim->getData()->message ? $claim->getData()->message:"Invalid credentials";
      // return $claim;
      return response()->json([
        'message' => $claimMessage
      ], Response::HTTP_UNAUTHORIZED);
    //  var_dump($claim->getData()->message);


        } //End if


        }
        catch(NoLocalUserException $e){
            // throw $e;
             return response()->json(['status' => 'error', 'message' => "User does not exist"], 400);


         }
        catch(Exception $e){
           // throw $e;
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);


        }




    }
}
