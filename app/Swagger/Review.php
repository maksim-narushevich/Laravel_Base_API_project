<?php

namespace App\Swagger;


/**
 * @OA\Schema(schema="Review")
 */
class Review
{
    /**
     * @OA\Property(type="integer")
     */
    public $id;

    /**
     * @OA\Property(type="string")
     */
    public $product_name;

    /**
     * @OA\Property(type="integer")
     */
    public $user_id;

    /**
     * @OA\Property(type="string")
     */
    public $username;

    /**
     * @OA\Property(type="string")
     */
    public $text;

    /**
     * @OA\Property(type="integer")
     */
    public $star;

}
/**
 *  @OA\Schema(
 *   schema="NewReview",
 *   type="object",
 *   allOf={
 *       @OA\Schema(
 *           required={"star","text"},
 *           @OA\Property(property="text", type="string"),
 *           @OA\Property(property="star",  type="integer")
 *       )
 *   }
 * )
 */