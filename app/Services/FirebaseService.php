<?php

namespace App\Services;

class FirebaseService{
    private $auth;
    public function __construct(){
        $this->auth = app('firebase.auth');
    }

    public function createUser($email, $password){
        $user = $this->auth->createUser([
            'email' => $email,
            'password' => $password
        ]);
        return $user;
    }

    public function getUserByEmail($email){
        try{
            $user = $this->auth->getUserByEmail($email);
            return $user;
        }catch(\Throwable $th){
            info('Unable to get user by email', ['error' => $th->getMessage()]);
            return null;
        }
    }

    public function deleteUser($uid){
        try{
            $this->auth->deleteUser($uid);
            info('User with UID '.$uid.' deleted successfully.');
        }catch(\Throwable $th){
            info('Unable to delete user', ['error' => $th->getMessage()]);
        }
    }
    public function sendVerficationMail($email){
        try{
            $queryString = http_build_query(['email'=>$email]);
            $actionCodeSettings = [
                'continueUrl' => 'https://service.citio.cool/verify-email?'.$queryString
            ];
            $this->auth->sendEmailVerificationLink($email,$actionCodeSettings);
            info('Verification Link Sent to '.$email);
        }catch(\Throwable $th){
            info('Unable to Send Verification Link', ['error' => $th->getMessage()]);
        }
    }

    public function sendPasswordResetLink($id,$email){
        try{
            $queryString = http_build_query(['id' => $id]);
            $actionCodeSettings = [
                'continueUrl'=>'https://service.citio.cool/reset-password?'.$queryString,
                'handleCodeInApp' => true,
            ];
            $this->auth->sendPasswordResetLink($email,$actionCodeSettings);
            info('Reset Password Link Sent to '.$email);
        }catch(\Throwable $th){
            info('Unable to Send Reset Passwod Link', ['error' => $th->getMessage()]);
        }
    }
}
