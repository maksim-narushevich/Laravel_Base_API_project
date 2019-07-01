<?php
namespace App\Http\Controllers\Api;

use App\Http\Requests\UserRequest;
use App\Http\Resources\User\UserResource;
use App\Services\Pagination\Paginator;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class UserController extends BaseApiController
{

    /**
     * @OA\Get(
     *      path="/auth-user",
     *      tags={"Auth"},
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
    public function getUser()
    {
        return $this->view(new UserResource(Auth::user()),Response::HTTP_OK);
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
     * @OA\Get(
     *      path="/users/{id}",
     *      tags={"User"},
     *      summary="Get user by ID",
     *      description="Returns specific user by ID",
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
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
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserByID(User $user)
    {
        return $this->view(new UserResource($user),Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *      path="/user/{id}",
     *      tags={"User"},
     *      summary="Delete user by ID",
     *      description="Returns successful delete information",
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
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
        return $this->view('User with ID ' . $id . ' was successfully deleted',Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *      path="/auth-user/delete",
     *      tags={"Auth"},
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
        return $this->view('User with ID ' . $id . ' was successfully deleted',Response::HTTP_OK);
    }


    /**
     * @OA\Put(
     *      path="/users/{id}",
     *      tags={"User"},
     *      summary="Get user by ID",
     *      description="Returns specific user by ID",
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *         description="Update user",
     *          required=true,
     *          @OA\JsonContent(
     *          @OA\Property(property="name", type="string"),
     *          @OA\Property(property="email", type="string"),
     *          @OA\Property(property="password", type="string"),
     *          ),
     *     ),
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
     * @param User $user
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateUserByID(User $user,Request $request)
    {
        //-- Validate input data
        $this->validate($request, [
            'name' => 'alpha_num',
            'email' => 'email|unique:users,email,'.$user->id,
            'password' => '',
        ],
            [
                'name.alpha_num' => 'Name should be alpha numeric value',
                'email.email' => 'Email field should be valid email',
                'email.unique' => 'Such email already registered',
            ]);
        $input = $request->all();
        if(!isset($input['password']) && !empty($input['password'])){
            $input['password'] = bcrypt($input['password']);
        }

        $user->update($input);
        return $this->view(new UserResource($user), Response::HTTP_OK);
    }
}
