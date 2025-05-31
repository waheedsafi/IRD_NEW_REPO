<?php

namespace App\Http\Requests\app\donor;

use Illuminate\Foundation\Http\FormRequest;

class DonorUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Get the raw contents field from the request (this is your JSON string)
        $jsonData = $this->input('contents');

        // Decode the JSON string into an array
        if ($jsonData) {
            $decodedData = json_decode($jsonData, true);

            // If the JSON is valid, merge the decoded data into the request
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->merge($decodedData);
            } else {
                // Log or handle error if JSON is invalid
                // Log::error('Invalid JSON data received', ['data' => $jsonData]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'contact' => 'required',
            'abbr' => 'required|string',
            'name_english' => 'required|string|max:255',
            'name_pashto' => 'required|string|max:255',
            'name_farsi' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'area_english' => 'string|max:255',
            'area_pashto' => 'string|max:255',
            'area_farsi' => 'string|max:255',
            'district.id' => 'required|integer',
            'province.id' => 'required|integer'
        ];
    }
}
