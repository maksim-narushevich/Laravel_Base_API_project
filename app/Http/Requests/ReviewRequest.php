<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
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
            'star' => 'integer|between:0,5',
            'text' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'text.required' => 'Text field is required',
            'star.between' => 'Star field value must be between 0 and 5',
            'star.integer' => 'Star field value must an integer value',
        ];
    }
}
