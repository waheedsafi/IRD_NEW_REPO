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
            'contact' => 'required|regex:/^[0-9]{10}$/|unique:contacts,value',
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:8|max:25|confirmed',
        ];
    }
}
