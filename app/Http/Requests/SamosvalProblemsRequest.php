<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SamosvalProblemsRequest extends FormRequest
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
            'title' => 'required|string|min:3|max:200',
            'description' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Укажите название поломки.',
            'title.string' => 'Название должно быть строкой.',
            'title.min' => 'Название должно содержать минимум :min символа(ов).',
            'title.max' => 'Название не должно превышать :max символов.',

            'description.required' => 'Укажите описание поломки.',
            'description.string' => 'Описание должно быть строкой.',
            'description.max' => 'Описание не должно превышать :max символов.',
        ];
    }
}
