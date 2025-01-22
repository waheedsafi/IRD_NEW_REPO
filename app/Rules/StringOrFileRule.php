<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class StringOrFileRule implements ValidationRule
{
    protected $allowedExtensions;

    // Constructor to accept allowed extensions
    public function __construct(array $allowedExtensions)
    {
        $this->allowedExtensions = $allowedExtensions;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // If the value is a string, validation passes automatically
        if (is_string($value)) {
            return;
        }

        // If the value is a file, validate that it's an uploaded file with the correct extensions and size
        if ($value instanceof UploadedFile) {
            // Check if the file extension is one of the allowed types
            if (!in_array($value->getClientOriginalExtension(), $this->allowedExtensions)) {
                $fail('The ' . $attribute . ' must be an image file of type: ' . implode(', ', $this->allowedExtensions));
                return;
            }

            // Check the file size (max 2MB)
            if ($value->getSize() > 2048 * 1024) {
                $fail('The ' . $attribute . ' must not exceed 2MB in size.');
                return;
            }
        } else {
            // If the value is neither a string nor a valid file, the validation fails
            $fail('The ' . $attribute . ' must be either a valid string or a valid image file.');
        }
    }
}
