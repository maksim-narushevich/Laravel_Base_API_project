<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

    /**
     * @OA\Post(
     *      path="/services/image/upload",
     *      tags={"Services"},
     *      summary="Image uploader service",
     *      description="Returns successful information",
     *      @OA\RequestBody(
     *         description="Upload image to provided storage (default local)",
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
    public function imageUpload(Request $request)
    {
        //-- Validate input data
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10024'
        ],
            [
                'image.required' => 'Image field is required',
                'image.mimes' => 'Image field should be type jpeg,png,jpg,gif,svg',
                'image.max' => 'Image max allowed size is 1024 b',
            ]);

        $fileName = time()."_".request()->image->getClientOriginalName();
        Storage::disk('local')->put("/public/uploads/".Auth::id()."/images/".$fileName, request()->image->getRealPath());
        dd("Success!");

    }

}
