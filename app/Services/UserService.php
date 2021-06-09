<?php
namespace App\Services;
use App\Models\User;
use Carbon\Carbon;
use Mail;

class UserService
{
    public function create ($data) {
        try {
            if ($data["email"]!="" and $data["password"]!="") {
                $user = new User;
                $user->email = $data["email"];
                $user->password = $data["password"];
                $user->invitation_code = $data["code"];
                $user->verification_code =  "randkey".Carbon::now()->timestamp;

                $user->save();    
            } else {
                return "Email and Password Required.";
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $user;
    }

    public function sendVerficationEmail ($email, $code, $verification) {
        try {
            if ($email!="" and $code!="" and $verification!="") {
                $baseURL = \Request::root();
                $verificationURL = $baseURL."/api/verification/".$code."/".$verification;
                $content = "Hello, please click on this link: ".$verificationURL;
                Mail::raw(
                    $content, function($message) {
                    $message->to("mirzaanas442@gmail.com", 'Developer')->subject ('Laravel API, Verification Email');
                    $message->from('mirzaanas442@gmail.com','API LARAVEL');
                });
                return true;
            } else {
                return "Invalid Request!";
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function checkVerification ($invitationCode, $verificationCode) {
        try {
            if ($invitationCode !="" and $verificationCode != "") {
                $user = new User;
                $user->where('invitation_code', $invitationCode)
                ->where('verification_code', $verificationCode)
                ->update(['email_verified_at' => now()]);
                return true;    
            } else {
                return "Invalid Verification Code";
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function updateProfile ($data) {
        try {
            $user = new User;
            $user->where('verification_code', $data["id"])
            ->update([
                'name' => $data["name"]
                , 'avatar' => $data["avatar"]
                , 'user_name' => $data["username"]
            ]);
            return true;    
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
?>