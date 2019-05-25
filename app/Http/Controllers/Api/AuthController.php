<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
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
 *      url="http://laramyapi.test/api/v1",
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

class AuthController extends Controller
{
    public $successStatus = 200;

    /**
     * @OA\Post(
     *      path="/register",
     *      tags={"User"},
     *     operationId="addUser",
     *     description="Create a new user.",
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
        $success['token'] =  $user->createToken('AppName')->accessToken;
        return response()->json(['success'=>$success], $this->successStatus);
    }


    /**
     * @OA\Post(
     *      path="/login",
     *      tags={"User"},
     *     operationId="loginUser",
     *     description="Login user.",
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
     *          description="successful operation"
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
}
