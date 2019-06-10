<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Aws\Laravel\AwsFacade as AWS;

class AWSController extends Controller
{

    /**
     * @param $phone_number
     */
    protected function sendSMS($phone_number)
    {
        $sms = AWS::createClient('sns');

        $response = $sms->publish([
            'Message' => 'Hello. Thank you for registration!',
            'PhoneNumber' => $phone_number,
            'MessageAttributes' => [
                'AWS.SNS.SMS.SMSType' => [
                    'DataType' => 'String',
                    'StringValue' => 'Transactional',
                ]
            ],
        ]);
        dd($response);
    }


    protected function bucket()
    {
        //Storage::disk('s3')->makeDirectory('test');
        $image = Storage::disk('s3')->get('/aws.png');
        Storage::disk('local')->put('aws_custom.png', $image);
        dd("Success!");
    }
}
