<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\NotBelongsToUser;
use App\Exceptions\ReviewNotBelongsToProduct;
use App\Http\Requests\ReviewRequest;
use App\Http\Resources\Review\ReviewCollection;
use App\Http\Resources\Review\ReviewResource;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends BaseApiController
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
     *      path="/products/{id}/reviews",
     *      operationId="getProductReviews",
     *      tags={"Review"},
     *      summary="Get all product's reviews",
     *      description="Returns all product's reviews details",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Review"),
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Product $product, Request $request)
    {
        $reviewPg = $this->getSortedCollectionData($request, 'review', ['product_id' => $product->id]);
        if (!empty($reviewPg)) {
            if ($reviewPg->currentPage() <= $reviewPg->lastPage()) {
                return ReviewCollection::collection($reviewPg);
            } else {
                return $this->errorView('page_not_found', Response::HTTP_NOT_FOUND);
            }
        } else {
            return $this->errorView('reviews_not_found', Response::HTTP_NOT_FOUND);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ReviewRequest $request
     *
     * @param Product $product
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @OA\Post(
     *      path="/products/{id}/reviews",
     *      tags={"Review"},
     *     operationId="addReview",
     *     description="Create a new review.",
     *     @OA\RequestBody(
     *         description="Create review",
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/NewReview")
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
    public function store(ReviewRequest $request, Product $product)
    {
        $request['review'] = $request->text;
        $request['product_id'] = $product->id;
        $request['star'] = $request->star ?? 0;
        $request['user_id'] = Auth::id();
        unset($request['text']);
        $review = new Review($request->all());
        $product->reviews()->save($review);
        return $this->view(new ReviewResource($review), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @param Review $review
     *
     * @OA\Get(
     *      path="/products/{id}/reviews/{review}",
     *      operationId="getReviewById",
     *      tags={"Review"},
     *      summary="Get review details",
     *      description="Returns review details",
     *      @OA\Parameter(
     *          name="id",
     *          description="Product id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *     name="review",
     *     in="path",
     *     description="ID of review to show",
     *     required=true,
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ),
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
    public function show(Product $product, Review $review)
    {
        return $this->view(new ReviewResource($review), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     *
     * @param Review $review
     * @return \Illuminate\Http\JsonResponse
     * @throws NotBelongsToUser
     * @throws ReviewNotBelongsToProduct
     * @OA\Put(
     *      path="/products/{id}/reviews/{review}",
     *      tags={"Review"},
     *     operationId="updateReview",
     *     description="Update review.",
     *      @OA\Parameter(
     *          name="id",
     *          description="Product id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Parameter(
     *     name="review",
     *     in="path",
     *     description="ID of review to be updated",
     *     required=true,
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ),
     *     @OA\RequestBody(
     *         description="Update review",
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/NewReview")
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
    public function update(Request $request, Product $product, Review $review)
    {
        $this->ReviewCheck($product, $review);
        if(!is_null($request->text)){
            $request['review'] = $request->text;
            unset($request['text']);
        }
        $review->update($request->all());
        return $this->view(new ReviewResource($review), Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @param Review $review
     * @return \Illuminate\Http\JsonResponse
     * @throws ReviewNotBelongsToProduct
     * @throws NotBelongsToUser
     * @OA\Delete(path="/products/{id}/reviews/{review}",
     *   tags={"Review"},
     *   summary="Delete review",
     *   description="This can only be done by the logged in user.",
     *   operationId="deleteReview",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of product",
     *     required=true,
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ),
     *      @OA\Parameter(
     *     name="review",
     *     in="path",
     *     description="ID of review to be deleted",
     *     required=true,
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ),
     *   @OA\Response(response=400, description="Invalid ID supplied"),
     *   @OA\Response(response=404, description="Review not found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function destroy(Product $product, Review $review)
    {
        $this->ReviewCheck($product, $review);
        $review->delete();
        return $this->view("Review with id " . $review->id . " was successfully deleted", Response::HTTP_OK);
    }


    /**
     * @param $product
     * @param $review
     * @throws NotBelongsToUser
     * @throws ReviewNotBelongsToProduct
     */
    public function ReviewCheck($product, $review)
    {
        if ($product->id !== $review->product_id) {
            throw new ReviewNotBelongsToProduct("review_not_belongs");
        }

        if (Auth::id() !== $review->user_id) {
            throw new NotBelongsToUser("Review does not belong to currently authorized user");
        }
    }
}
