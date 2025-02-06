<?php

namespace App\Http\Requests\app\ngo\director;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDirectorRequest extends FormRequest
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
            'name_english' => 'required|string|max:128',
            'name_pashto' => 'required|string|max:128',
            'name_farsi' => 'required|string|max:128',
            'last_name_english' => 'required|string|max:128',
            'last_name_pashto' => 'required|string|max:128',
            'last_name_farsi' => 'required|string|max:128',
            'contact' => 'required|string|max:20|unique:contacts,value,' . $this->route('id'),
            'email' => 'required|email|max:128|unique:emails,value,' . $this->route('id'),
            'gender.id' => 'required|exists:genders,id',
            'nid_no' => 'required|string|max:50',
            'nid_type.nid_id' => 'required|exists:nid_types,id',
            'country.id' => 'required|exists:countries,id',
            'province.id' => 'required|exists:provinces,id',
            'district.id' => 'required|exists:districts,id',
            'area_english' => 'required|string|max:255',
            'area_pashto' => 'required|string|max:255',
            'area_farsi' => 'required|string|max:255',
        ];
    }
}
