<?php

namespace App\Http\Controllers\Api;

use App\Mail\RegistrationSuccessful;
use App\Services\Mailer;
use App\Services\Pagination\Paginator;
use App\Services\TokenGenerator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
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
     *      tags={"User"},
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
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['confirmation_token'] = TokenGenerator::generate();

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

        return response()->json(['data' => 'successfully_registered'], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *      path="/confirm",
     *      tags={"User"},
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
                return response()->json(['data' => 'successfully_confirmed'], Response::HTTP_OK);
            } else {
                return response()->json(['error' => 'confirmation_token_not_found'], Response::HTTP_NOT_FOUND);
            }
        } else {
            return response()->json(['error' => 'confirmation_token_not_provided'], Response::HTTP_BAD_REQUEST);
        }
    }


    /**
     * @OA\Post(
     *      path="/login",
     *      tags={"User"},
     *     operationId="loginUser",
     *     description="Login user.",
     *     summary="Login with existing user and obtain JWT token",
     *     @OA\RequestBody(
     *         description="Authorize user and get token",
     *          required=true,
     *         @OA\JsonContent(ref="#/components/schemas/User")
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
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            if ($user->enabled) {
                $success['token'] = $user->createToken('AppName')->accessToken;
                return response()->json(['data' => $success], Response::HTTP_OK);
            } else {
                return response()->json(['error' => 'profile_not_enabled'], Response::HTTP_UNAUTHORIZED);
            }
        } else {
            return response()->json(['error' => 'Unauthorised'], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @OA\Get(
     *      path="/auth-user",
     *      tags={"User"},
     *      summary="Get authorized user details",
     *      description="Returns logged user data",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/UserItem"),
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function getUser(Request $request, Paginator $paginator)
    {
        return $this->view(Auth::user(),Response::HTTP_OK);
    }


    /**
     * @OA\Get(
     *      path="/users",
     *      tags={"User"},
     *      summary="All users",
     *      description="Returns liss of all users",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/UserItem"),
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param Request $request
     * @param Paginator $paginator
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserList(Request $request, Paginator $paginator)
    {
        return $this->view($paginator->paginate(User::all()->toArray(),$request),Response::HTTP_OK);
    }


    /**
     * @OA\Delete(
     *      path="/user/delete/{id}",
     *      tags={"User"},
     *      summary="Delete user by ID",
     *      description="Returns successful delete information",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function deleteUser(User $user)
    {
        $id = $user->id;
        $user->delete();
        return response()->json(['data' => 'User with ID ' . $id . ' was successfully deleted'], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *      path="/auth-user/delete",
     *      tags={"User"},
     *      summary="Delete authorized user",
     *      description="Returns successful delete information",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function deleteAuthUser()
    {
        $id = Auth::id();
        Auth::user()->delete();
        return response()->json(['data' => 'User with ID ' . $id . ' was successfully deleted'], Response::HTTP_OK);
    }
}
