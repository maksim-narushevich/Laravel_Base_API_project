<?php


namespace App\Services;


use App\Mail\RegistrationSuccessful;
use Illuminate\Support\Facades\Mail;

class Mailer
{

    static public function sendSuccessRegistrationMail(array $data)
    {
        $user = $data['user'];

        if (!empty($user->email)) {
            $data['to'] = $user->email;
            $data['from'] = 'narushevich.maksim@gmail.com';
            $data['title'] = 'Welcome ' . $user->name . "!";

            //-- Sent email with mailable template
            Mail::to($data['to'])->send(new RegistrationSuccessful($data));


            //-- Check if there are no email failures
            //if(Mail::failures()) {
            //    //TODO Add logs about email failure
            //}
        }
    }


}
