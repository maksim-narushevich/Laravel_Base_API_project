<?php

namespace App\Http\Controllers\Api;

use App\Services\Pagination\Paginator;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class ServicesController extends BaseApiController
{

    /**
     * @OA\Post(
     *      path="/services/sms",
     *      tags={"Services"},
     *      summary="Send SMS to spevicified phone number",
     *      description="Returns successful information",
     *      @OA\RequestBody(
     *         description="Send message to valid phone number",
     *         required=true,
     *         @OA\JsonContent(
     *          required={"number","message"},
     *          @OA\Property(property="number", type="string"),
     *          @OA\Property(property="message", type="string"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param Request $request
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function sendSMS(Request $request)
    {
        //-- Validate input data
        $this->validate($request, [
            'number' => 'required|regex:/\+[0-9]{12}/|size:13',
            'message' => 'required'
        ],
            [
                'number.required' => 'Number field is required',
                'number.regex' => 'Phone number format is invalid',
                'number.size' => 'Phone number should consist of 12 digits only',
                'message.required' => 'Message field is required',
            ]);

        dd($request->all());
    }
}
