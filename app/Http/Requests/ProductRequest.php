<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:255|unique:products',
            'description' => 'required',
            'price' => 'required|max:10',
            'stock' => 'required|max:6',
            'discount' => 'required|max:2',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name field is required',
            'name.max' => 'Name field max characters is 255',
            'description.required' => 'Description field is required',
            'price.required' => 'Price field is required',
            'price.max' => 'Price field max value is 10',
            'stock.required' => 'Stock field is required',
            'stock.max' => 'Stock field max value is 6',
            'discount.required' => 'Discount field is required',
            'discount.max' => 'Discount field max value is 2',
            'star.between' => 'Star field value must be between 0 and 5',
            'star.integer' => 'Star field value must an integer value',
        ];
    }
}
