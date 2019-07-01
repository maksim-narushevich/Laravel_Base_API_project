<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => 'required|alpha_num',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name field is required',
            'name.alpha_num' => 'Name should be alpha numeric value',
            'email.required' => 'Email field is required',
            'password.required' => 'Password field is required',
            'c_password.required' => 'c_password field is required',
            'c_password.same' => 'Confirmation password must be the same',
        ];
    }
}
