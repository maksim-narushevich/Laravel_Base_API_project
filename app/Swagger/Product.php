<?php

namespace App\Swagger;

/**
 * @OA\Schema(schema="Product")
 */
class Product
{
    /**
     * @OA\Property(type="string")
     */
    public $name;

    /**
     * @OA\Property(type="string")
     */
    public $description;

    /**
     * @OA\Property(type="integer")
     */
    public $stock;

    /**
     * @OA\Property(type="integer")
     */
    public $price;

    /**
     * @OA\Property(type="integer")
     */
    public $discount;

}
/**
 *  @OA\Schema(
 *   schema="NewProduct",
 *   type="object",
 *   allOf={
 *       @OA\Schema(ref="#/components/schemas/Product"),
 *       @OA\Schema(
 *           required={"name","description","stock","price","discount"}
 *       )
 *   }
 * )
 */