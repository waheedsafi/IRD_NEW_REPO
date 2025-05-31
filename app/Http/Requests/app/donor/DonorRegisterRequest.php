<?php

namespace App\Http\Requests\app\donor;

use Illuminate\Foundation\Http\FormRequest;

class DonorRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Assuming authorization is needed, return true for now to allow request
        return true; // Or add your specific condition if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:emails,value',
            'contact' => 'required',
            'abbr' => 'required|string|unique:donors,abbr',
            'name_english' => 'required|string|max:255',
            'name_pashto' => 'required|string|max:255',
            'name_farsi' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:donors,username',
            'password' => 'required|string|min:8|max:25',
            'area_english' => 'string|max:255',
            'area_pashto' => 'string|max:255',
            'area_farsi' => 'string|max:255',
            'district_id' => 'integer|required|exists:districts,id',
            'province_id' => 'integer|required|exists:provinces,id'
        ];
    }
}
