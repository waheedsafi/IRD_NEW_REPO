<?php

namespace App\Http\Requests\template\about;

use Illuminate\Foundation\Http\FormRequest;

class AboutUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'id' =>'required|integer',
             'address_englis' =>"required|string",
            'address_pashto' =>"required|string",
            'address_farsi' =>"required|string",
            'contact' =>"required|string",
            'email' =>"required|email",
        ];
    }
}
