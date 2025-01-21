<?php

namespace App\Http\Requests\app\news;

use Illuminate\Foundation\Http\FormRequest;

class NewsUpdateRequest extends FormRequest
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
            'visible' => 'required|boolean',
            'date' => 'required',
            'visibility_date' => 'nullable|date',
             'cover_pic' => 'required|file|mimes:png,jpeg,gif|max:2048',

            'type_name' => 'required ',
            'priority_name' => 'required ',

             'title_english' => 'required|unique:news_trans,title',
            'title_farsi' => 'required|unique:news_trans,title',
            'title_pashto' => 'required|unique:news_trans,title',
            'content_english' => 'required',
            'content_farsi' => 'required',
            'content_pashto' => 'required',
            'type' => 'required|integer|exists:news_types,id',
            'priority' => 'required|integer|exists:priorities,id',
            // For optimization
        
        ];
    }
}
