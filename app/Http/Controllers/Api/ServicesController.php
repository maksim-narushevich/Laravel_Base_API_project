<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Image\ImageResource;
use App\Models\Image;
use App\Models\Product;
use App\Services\SMS\SMSFactory;
use App\User;
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
     * @throws \App\Services\SMS\ServiceException
     */
    public function sendSMS(Request $request)
    {
        //-- Validate input data
        $this->validate($request, [
            'number' => 'required|regex:/\+[0-9]{11,}/|min:12',
            'message' => 'required'
        ],
            [
                'number.required' => 'Number field is required',
                'number.regex' => 'Phone number format is invalid',
                'number.min' => 'Phone number should consist of minimum 11 digits',
                'message.required' => 'Message field is required',
            ]);

        $sms=SMSFactory::getService("aws");
        $response = $sms->publish([
            'Message' => $request->get('message'),
            'PhoneNumber' => $request->get('number'),
            'MessageAttributes' => [
                'AWS.SNS.SMS.SMSType' => [
                    'DataType' => 'String',
                    'StringValue' => 'Transactional',
                ]
            ],
        ]);
        dd($response);
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
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="Media to upload",
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                 ),
     *                 required={"image"}
     *             )
     *         )
     *     ),
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
     * @return \Illuminate\Http\JsonResponse
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


        $image=Image::create([
            'name'=>explode(".",request()->image->getClientOriginalName())[0],
            'storage'=>'local',
            'user_id'=>Auth::id(),
            'size'=>request()->image->getSize(),
            'extension'=>request()->image->getClientOriginalExtension(),
        ]);
        if(!is_null($image)){
            $fileName = time()."_".request()->image->getClientOriginalName();
            Storage::disk('local')->put("/public/uploads/".Auth::id()."/images/".$fileName, request()->image->getRealPath());
            return $this->view(new ImageResource($image), Response::HTTP_CREATED);
        }else{
            return $this->errorView('can_upload_image', Response::HTTP_BAD_REQUEST);
        }
    }



    /**
     * @OA\Post(
     *      path="/services/image/upload-with-resizing",
     *      tags={"Services"},
     *      summary="Image uploader service with resizing",
     *      description="Returns successful information",
     *      @OA\RequestBody(
     *         description="Upload image to provided storage (default local)",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="Media to upload",
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                 ),
     *                 required={"image"}
     *             )
     *         )
     *     ),
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
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function imageUploadWithResizing(Request $request)
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

        //$product=Product::findOrFail(1);
        ////Store Image
        //if($request->hasFile('image') && $request->file('image')->isValid()){
        //  $product->addMediaFromRequest('image')->toMediaCollection('images');
        //}

        $user=Auth::user();
        //Store Image
        if($request->hasFile('image') && $request->file('image')->isValid()){
            $user->addMediaFromRequest('image')->toMediaCollection('user_images');
        }
    }

    public function getImage()
    {

        //$product=Product::findOrFail(1);
        //dd($product->getFirstMediaUrl('images'));
        //dd($product->getMedia('images')[0]->getUrl('thumb'));

        $user=Auth::user();
        //dd($user->getFirstMediaUrl('user_images'));
        //dd($user->getMedia('user_images')[0]->getUrl('thumb'));

        //### Delete media
        //dd($user->getMedia('user_images')[0]->delete());
    }
}
