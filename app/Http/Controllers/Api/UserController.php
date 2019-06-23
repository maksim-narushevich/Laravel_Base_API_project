<?php
namespace App\Http\Controllers\Api;

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
    public function getUser()
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
        return $this->view('User with ID ' . $id . ' was successfully deleted',Response::HTTP_OK);
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
        return $this->view('User with ID ' . $id . ' was successfully deleted',Response::HTTP_OK);
    }
}
