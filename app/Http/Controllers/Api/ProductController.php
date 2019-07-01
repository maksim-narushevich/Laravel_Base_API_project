<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\NotBelongsToUser;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Resources\Product\ProductResource;
use App\Jobs\TestJob;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends BaseApiController
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
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
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {

//        $product=Product::findOrFail(9);
//
//        //dispatch test job
//        TestJob::dispatch($product);

        $productsPg = $this->getSortedCollectionData($request, 'product');
        if (!empty($productsPg)) {
            if ($productsPg->currentPage() <= $productsPg->lastPage()) {
                return ProductCollection::collection($productsPg);
            } else {
                return $this->errorView('page_not_found', Response::HTTP_NOT_FOUND);
            }
        }else{
            return $this->errorView('products_not_found', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\ProductRequest $request
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
     * @return \Illuminate\Http\JsonResponse
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
        return $this->view( new ProductResource($product),Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     *
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Product $product)
    {
        return $this->view(new ProductResource($product), Response::HTTP_OK);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws NotBelongsToUser
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
        if ($request->description) {
            $request['detail'] = $request->description;
        }

        unset($request['description']);
        $product->update($request->all());
        return $this->view(new ProductResource($product), Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws NotBelongsToUser
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
        $product = Product::find($id);
        if ($product != null) {
            $this->ProductUserCheck($product);
            $product->delete();
            return $this->view("Product with id " . $id . " was successfully deleted", Response::HTTP_OK);
        } else {
            return $this->errorView('page_not_found', Response::HTTP_NOT_FOUND);
        }

    }

    /**
     * @param $product
     * @throws NotBelongsToUser
     */
    public function ProductUserCheck($product)
    {
        if (Auth::id() !== $product->user_id) {
            throw new NotBelongsToUser("Product does not belong to currently authorized user");
        }
    }
}
