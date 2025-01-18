<?php

namespace App\Http\Requests\app\ngo;

use Illuminate\Foundation\Http\FormRequest;

class NgoProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true; // Ensure this is set according to your authentication logic
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            // Default language validation (English)
            'name_en' => 'required|string|max:255',
            'vision_en' => 'required|string|max:2000',
            'mission_en' => 'required|string|max:2000',
            'general_objective_en' => 'required|string|max:2000',
            'objective_en' => 'required|string|max:2000',
            'introduction_en' => 'required|string|max:2000',

            // Pashto validation
            'name_ps' => 'required|string|max:255',
            'vision_ps' => 'required|string|max:2000',
            'mission_ps' => 'required|string|max:2000',
            'general_objective_ps' => 'required|string|max:2000',
            'objective_ps' => 'required|string|max:2000',
            'introduction_ps' => 'required|string|max:2000',

            // Farsi validation
            'name_fa' => 'required|string|max:255',
            'vision_fa' => 'required|string|max:2000',
            'mission_fa' => 'required|string|max:2000',
            'general_objective_fa' => 'required|string|max:2000',
            'objective_fa' => 'required|string|max:2000',
            'introduction_fa' => 'required|string|max:2000',

            // Add additional rules here, if applicable
        ];
    }

    /**
     * Get custom messages for validation errors.
     */

}
