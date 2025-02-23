<?php

namespace App\Http\Requests\app\checklist;

use Illuminate\Foundation\Http\FormRequest;

class StoreCheckListRequest extends FormRequest
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
            "name_english" => "required",
            "name_farsi" => "required",
            "name_pashto" => "required",
            "file_size" => "required|numeric",
            "type" => "required",
            "status" => "required|boolean",
            "file_type" => "required",
        ];
    }
}
