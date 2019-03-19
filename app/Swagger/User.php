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