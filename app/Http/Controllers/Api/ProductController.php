<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Exceptions\ProductNotBelongsToUser;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth:api')->except('index','show');
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @OA\Get(
     *      path="/products",
     *      operationId="getProducts",
     *      tags={"Product"},
     *      summary="Get all products",
     *      description="Returns all product details",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Product"),
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function index()
    {
        return ProductCollection::collection(Product::paginate(20));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\ProductRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @OA\Post(
     *      path="/products",
     *      tags={"Product"},
     *     operationId="addProduct",
     *     description="Create a new product.",
     *     @OA\RequestBody(
     *         description="Create product",
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/NewProduct")
     *     ),
     *    @OA\Response(response=201, description="Null response"),
     *    @OA\Response(
     *        response="default",
     *        description="unexpected error",
     *        @OA\Schema(ref="#/components/schemas/Error")
     *    ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function store(ProductRequest $request)
    {
        $product = new Product;
        $product->name = $request->name;
        $product->user_id = Auth::id();
        $product->detail = $request->description;
        $product->stock = $request->stock;
        $product->price = $request->price;
        $product->discount = $request->discount;
        $product->save();
        return response([
            'data' => new ProductResource($product)
        ],Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     *
     * @return \App\Http\Resources\Product\ProductResource
     *
     * @OA\Get(
     *      path="/products/{id}",
     *      operationId="getProductById",
     *      tags={"Product"},
     *      summary="Get product details",
     *      description="Returns product details",
     *      @OA\Parameter(
     *          name="id",
     *          description="Product id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Product")
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     *
     * @return void
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \App\Models\Product       $product
     *
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\ProductNotBelongsToUser
     *
     * @OA\Put(
     *      path="/products/{id}",
     *      tags={"Product"},
     *     operationId="updateProduct",
     *     description="Update product.",
     *      @OA\Parameter(
     *          name="id",
     *          description="Product id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\RequestBody(
     *         description="Update product",
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *    @OA\Response(response=201, description="Null response"),
     *    @OA\Response(
     *        response="default",
     *        description="unexpected error",
     *        @OA\Schema(ref="#/components/schemas/Error")
     *    ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function update(Request $request, Product $product)
    {
        $this->ProductUserCheck($product);
        if( $request->description){
            $request['detail'] = $request->description;
        }

        unset($request['description']);
        $product->update($request->all());
        return response([
            'data' => new ProductResource($product)
        ],Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @throws ProductNotBelongsToUser
     * @OA\Delete(path="/products/{product}",
     *   tags={"Product"},
     *   summary="Delete product",
     *   description="This can only be done by the logged in user.",
     *   operationId="deleteProduct",
     *   @OA\Parameter(
     *     name="product",
     *     in="path",
     *     description="ID of product to be deleted",
     *     required=true,
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ),
     *   @OA\Response(response=400, description="Invalid ID supplied"),
     *   @OA\Response(response=404, description="Product not found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function destroy($id)
    {
        $product=Product::find($id);
        if($product!=null){
            $this->ProductUserCheck($product);
            $product->delete();
            return response(['data'=>"Product with id ".$id." was successfully deleted"],Response::HTTP_OK);
        }else{
            return response()
                ->json(['error' => 'Product with ID '.$id." was not found"]);
        }

    }
    public function ProductUserCheck($product)
    {
        if (Auth::id() !== $product->user_id) {
            throw new ProductNotBelongsToUser;
        }
    }
}
