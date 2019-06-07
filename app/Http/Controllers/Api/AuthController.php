<?php
namespace App\Http\Controllers\Api;
use App\Mail\RegistrationSuccessful;
use App\Services\Mailer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Validator;


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

 *  @OA\Server(
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
    public $successStatus = 200;

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
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);                        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);

        //-- Get current environment in order prevent sending email in 'test' mode
        $env = app()->environment();
        //-- Send email after successful registration
        if($env!=='test' && !is_null($user) && !empty($user->email)){
            $emailData['content']="Welcome to this app ".$user->name."!";
            $emailData['user']=$user;
            Mailer::sendSuccessRegistrationMail($emailData);
        }

        $success['token'] =  $user->createToken('AppName')->accessToken;
        return response()->json(['success'=>$success], $this->successStatus);
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
    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('AppName')-> accessToken;
            return response()->json(['success' => $success], $this-> successStatus);
        } else{
            return response()->json(['error'=>'Unauthorised'], 401);
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
    public function getUser() {
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus);
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
     */
    public function deleteUser(User $user) {
        $id=$user->id;
        $user->delete();
        return response()->json(['success' => 'User with ID '.$id.' was successfully deleted'], $this->successStatus);
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
    public function deleteAuthUser() {
        $id=$user = Auth::id();
        Auth::user()->delete();
        return response()->json(['success' => 'User with ID '.$id.' was successfully deleted'], $this->successStatus);
    }
}
