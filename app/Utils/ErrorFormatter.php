<?php
namespace App\Utils;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ErrorFormatter
{

    /**
     * @param Request $request
     * @param Exception $exception
     * @param $data
     * @param null $errorCode
     * @param null $placeholders
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    static function getErrorFormat($data, $errorCode = null, $placeholders = null, array $headers = [])
    {
        $statusCode=$errorCode;
        $arrMessage = [];
        if (!empty($placeholders)) {
            $arrMessage['placeholders'] = $placeholders;
        }

        if(is_array($data)){
            $arrMessage["label"] = $data["label"];
            if(isset($data['exception'])){
                $arrMessage["info"] = $data['exception']['messages'];
                if(empty($statusCode)){
                    $statusCode=$data['exception']['code'];
                }
                unset($data['exception']);
            }
        }else{
            $arrMessage["label"] = $data;
        }

        //-- If error code still empty than set default code value
        if(empty($statusCode)){
            $statusCode=Response::HTTP_BAD_REQUEST;
        }
        $errorData = ["error" => [
            "code" => $statusCode,
            "message" => $arrMessage,
             ]
        ];
        return response()->json($errorData,$statusCode);
    }
}