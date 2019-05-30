<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ReviewNotBelongsToProduct;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Http\Resources\Review\ReviewCollection;
use App\Http\Resources\Review\ReviewResource;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends BaseApiController
{

    public function __construct()
    {
        //$this->middleware('auth:api')->except('index','show');
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\Product $product
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Product $product)
    {
        return ReviewCollection::collection(Review::where('product_id',$product->id)->paginate(20));
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
     * @param \App\Http\Requests\ReviewRequest $request
     * @param \App\Models\Product              $product
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ReviewRequest $request,Product $product)
    {
        $request['review'] = $request->text;
        $request['product_id'] = $product->id;
        unset($request['text']);
        $review = new Review($request->all());
        $product->reviews()->save($review);
        return response([
            'data' => new ReviewResource($review)
        ],Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @param \App\Models\Review  $review
     *
     * @return \App\Http\Resources\Review\ReviewResource
     */
    public function show(Product $product,Review $review)
    {
        return new ReviewResource($review);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Review $review
     *
     * @return void
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \App\Models\Product       $product
     * @param \App\Models\Review        $review
     *
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\ReviewNotBelongsToProduct
     */
    public function update(Request $request,Product $product, Review $review)
    {
        $this->ReviewProductCheck($product,$review);
        $request['review'] = $request->text;
        unset($request['text']);
        $review->update($request->all());
        return response([
            'data' => new ReviewResource($review)
        ],Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @param \App\Models\Review  $review
     *
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\ReviewNotBelongsToProduct
     */
    public function destroy(Product $product,Review $review)
    {
        $this->ReviewProductCheck($product,$review);
        $review->delete();
        return response(['data'=>"Review with id ".$review->id." was successfully deleted"],Response::HTTP_OK);
    }
    public function ReviewProductCheck($product,$review)
    {
        if ($product->id !== $review->product_id) {
            throw new ReviewNotBelongsToProduct;
        }
    }
}
