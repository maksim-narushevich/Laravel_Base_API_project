<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserRequest;
use App\Services\Mailer;
use App\Services\TokenGenerator;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="API DOCS",
 *      description="Advanced API documentation",
 *      @OA\Contact(
 *          email="narushevich.maksim@gmail.com"
 *      ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 * @OA\Server(
 *      url="/api/v1",
 *      description="API documentation"
 * )
 * @OA\SecurityScheme(
 *     @OA\Flow(
 *         flow="clientCredentials",
 *         tokenUrl="oauth/token",
 *         scopes={}
 *     ),
 *     securityScheme="bearerAuth",
 *     in="header",
 *     type="http",
 *     description="Oauth2 security",
 *     name="oauth2",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 * )
 */
class AuthController extends BaseApiController
{
    /**
     * @OA\Post(
     *      path="/register",
     *      tags={"Auth"},
     *     operationId="addUser",
     *     description="Create a new user.",
     *     summary="Register new user and return JWT token",
     *     @OA\RequestBody(
     *         description="Create user",
     *          required=true,
     *         @OA\JsonContent(ref="#/components/schemas/NewUser")
     *     ),
     *    @OA\Response(response=201, description="Null response"),
     *    @OA\Response(
     *        response="default",
     *        description="unexpected error",
     *        @OA\Schema(ref="#/components/schemas/Error")
     *    )
     * )
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(UserRequest $request)
    {
        $input = $request->all();
        $checkUser=User::where('email',$input['email'])->first();
        if(is_null($checkUser)){
            $input['password'] = bcrypt($input['password']);
            $tokenGeneratorType=!empty(config('serverless.ibm_token'))?"ibm":"local";
            $input['confirmation_token'] = TokenGenerator::generate($tokenGeneratorType);

            $user = User::create($input);

            //TODO Temporarily deactivate email sending
//        //-- Get current environment in order prevent sending email in 'test' mode
//        $env = app()->environment();
//        //-- Send email after successful registration
//        if($env!=='test' && !is_null($user) && !empty($user->email)){
//            $emailData['content']="Welcome to this app ".$user->name."!";
//            $emailData['user']=$user;
//            Mailer::sendSuccessRegistrationMail($emailData);
//        }
            return $this->view('successfully_registered', Response::HTTP_OK);
        }else{
            return $this->errorView('such_email_already_registered', Response::HTTP_BAD_REQUEST);
        }

    }

    /**
     * @OA\Post(
     *      path="/confirm",
     *      tags={"Auth"},
     *      summary="Confirm registration",
     *      description="Returns successful confirmation information",
     *     @OA\RequestBody(
     *         description="Confirmation token",
     *         required=true,
     *         @OA\JsonContent(
     *          required={"token"},
     *          @OA\Property(property="token", type="string"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirm(Request $request)
    {
        $token = $request->get('token');
        if (!is_null($token)) {
            $user = User::where(['confirmation_token' => $token])->first();
            if (!is_null($user)) {
                $user->enabled = true;
                $user->confirmation_token = "";
                $user->update();
                return $this->view('successfully_confirmed', Response::HTTP_OK);
            } else {
                return $this->errorView('confirmation_token_not_found', Response::HTTP_NOT_FOUND);
            }
        } else {
            return $this->errorView('confirmation_token_not_provided', Response::HTTP_BAD_REQUEST);
        }
    }


    /**
     * @OA\Post(
     *      path="/login",
     *      tags={"Auth"},
     *     operationId="loginUser",
     *     description="Login user.",
     *     summary="Login with existing user and obtain JWT token",
     *     @OA\RequestBody(
     *         description="Authorize user and get token",
     *          required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Login")
     *     ),
     *    @OA\Response(response=201, description="Null response"),
     *    @OA\Response(
     *        response="default",
     *        description="unexpected error",
     *        @OA\Schema(ref="#/components/schemas/Error")
     *    )
     * )
     */
    public function login()
    {
        if (Auth::attempt(['email' => request('login'), 'password' => request('password')])) {
            $user = Auth::user();
            if ($user->enabled) {
                $success['token'] = $user->createToken('AppName')->accessToken;
                return $this->view($success, Response::HTTP_OK);
            } else {
                return $this->errorView('profile_not_enabled', Response::HTTP_UNAUTHORIZED);
            }
        } else {
            return $this->errorView('bad_credentials', Response::HTTP_UNAUTHORIZED);
        }
    }
}
