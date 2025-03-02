<?php

namespace App\Http\Requests\app\representor;

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;

class StoreRepresentorRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     *
     * This method is called before the validation rules are applied.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Get the raw contents field from the request (this is your JSON string)
        $jsonData = $this->input('content');

        // Decode the JSON string into an array
        if ($jsonData) {
            $decodedData = json_decode($jsonData, true);

            // If the JSON is valid, merge the decoded data into the request
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->merge($decodedData);
            } else {
                // Log or handle error if JSON is invalid
                Log::error('Invalid JSON data received', ['data' => $jsonData]);
            }
        }
    }
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
            "ngo_id" => "required",
            "repre_name_english" => "required|min:3|max:60",
            "repre_name_farsi" => "required|min:3|max:60",
            "repre_name_pashto" => "required|min:3|max:60",
            "letter_of_intro" => "required",
        ];
    }
}
