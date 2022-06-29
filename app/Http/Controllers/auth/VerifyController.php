<?php

namespace App\Http\Controllers\auth;

use App\Traits\RegistersUsers;
use Ellaisys\Cognito\AwsCognitoClaim;
//use App\Traits\AuthenticatesUsers;
use App\Http\Controllers\Controller;
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

class VerifyController extends Controller
{
  //  use AwsCognitoClient;
  //use AuthenticatesUsers;
     
  //  use ResetsPasswords;
   use VerifiesEmails;
  //  use ChangePasswords;
  //  use AwsCognitoClaim;
    



      
    
//change user email when user is forced to change it
    // public function resetPassword(Request $request){
    //     try{
    //         $resetPassword = $this->reset($request);
    //         return $resetPassword;
    //     }
    //     catch(Exception $e){
    //         return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);

    //     }

    // }


    public function confirmUser(Request $request){
        try{
            $confirm = $this->verify(collect($request));
            return $confirm;
        }
        catch(Exception $e){
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);

        }

    }

    public function resendConfToken(Request $request){
        try{
            $resend = $this->resend(collect($request));
            return $resend;
        }
        catch(Exception $e){
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);

        }

    }
}


