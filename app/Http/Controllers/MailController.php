<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationSuccessful;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{


    protected function send()
    {

        $input['to']='maxim.narushevich@cactussoft.biz';
        $input['from']='narushevich.maksim@gmail.com';
        $input['title']='Successful Registration!';
        $input['content']='Welcome to this app!';

        //-- Sent email with mailable template
        Mail::to($input['to'])->send(new RegistrationSuccessful($input));
        //-- Check if there are no email failures
        if(!Mail::failures()) {
            dd("Email was successfully sent!");
        }else{
            dd("Email was NOT sent!");
        }
    }

}
