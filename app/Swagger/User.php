<?php

namespace App\Swagger;

/**
 * @OA\Schema(schema="User", required={"email","password"})
 */
class User
{
    /**
     * @OA\Property(type="string")
     */
    public $email;
    /**
     * @OA\Property(type="string")
     */
    public $password;

}

/**
 *  @OA\Schema(
 *   schema="NewUser",
 *   type="object",
 *   allOf={
 *       @OA\Schema(ref="#/components/schemas/User"),
 *       @OA\Schema(
 *           required={"name","c_password"},
 *           @OA\Property(property="name", type="string"),
 *           @OA\Property(property="c_password",  type="string")
 *       )
 *   }
 * )
 */

/**
 *  @OA\Schema(
 *   schema="UserItem",
 *   type="object",
 *   allOf={
 *       @OA\Schema(ref="#/components/schemas/User"),
 *       @OA\Schema(
 *           @OA\Property(property="id", type="integer"),
 *           @OA\Property(property="name",  type="string"),
 *           @OA\Property(property="email",  type="string"),
 *           @OA\Property(property="created_at", type="string", format="date"),
 *           @OA\Property(property="updated_at", type="string", format="date"),
 *       )
 *   }
 * )
 */

/**
 *  @OA\Schema(
 *   schema="Login",
 *   type="object",
 *   allOf={
 *       @OA\Schema(
 *           required={"login","password"},
 *           @OA\Property(property="login", type="string"),
 *           @OA\Property(property="password",  type="string")
 *       )
 *   }
 * )
 */
