<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\UserService;

class ApiController extends Controller
{
	public function __construct()
    {
        $this->userService = new UserService();
    }

    public function invitation(Request $request) {
    	if (!$request["code"]) {
			$response = "Invitation URL incorrect";
			$responseCode = 500;
		} else {
      		$userRes = $this->userService->create($request);
		    if (isset($userRes["id"])) {
		        $userRes = $this->userService->sendVerficationEmail($userRes->email, $userRes->invitation_code, $userRes->verification_code);
		        if ( is_bool($userRes) and $userRes) {
		        	$response = "Verification link has been sent in your provided email";
		        } else {
		        	$response = $userRes;
		        }
		        $responseCode = 201;
		    } else {
		    	$response = $userRes;
		    	$responseCode = 500;
		    }
      	}
      	return response()->json([
	        "message" => $response
	    ], $responseCode);
    }

	public function verification(Request $request) {
      	if ($request["code"] == "" || $request["verficationCode"] == "") {
			$response = "URL incorrect";
			$responseCode = 500;
		} else {
      		$verificationRes = $this->userService->checkVerification($request["code"], $request["verficationCode"]);
      		if (is_bool($verificationRes) and $verificationRes) {
		        $baseURL = \Request::root();
                $updateProfileURL = $baseURL."/api/profile/".$request["verficationCode"];
		        $response = "Success! You are verified now. click mentioned URL to update your profile ".$updateProfileURL;
		        $responseCode = 201;
		    } else {
		    	$response = $verificationRes;
		    	$responseCode = 500;
		    }
      	}
      	return response()->json([
	        "message" => $response
	    ], $responseCode);
	}

	public function updateProfile(Request $request) {
      	if ($request["name"] == "" || $request["avatar"] == "" || $request["username"] == "") {
			$response = "name, username and avatar link is required";
			$responseCode = 500;
		} else {
      		$requestRes = $this->userService->updateProfile($request);
      		if (is_bool($requestRes) and $requestRes) {
		        $response = "Your Profile has been update";
		        $responseCode = 201;
		    } else {
		    	$response = $requestRes;
		    	$responseCode = 500;
		    }
      	}
      	return response()->json([
	        "message" => $response
	    ], $responseCode);
	}	
}
