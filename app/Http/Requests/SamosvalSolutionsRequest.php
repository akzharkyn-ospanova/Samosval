<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SamosvalSolutionsRequest extends FormRequest
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
            'problem_id' => 'required|exists:samosval_problems,id',
            'title' => 'required|string|max:200',
        ];
    }

    public function messages(): array
    {
        return [
            'problem_id.required' => 'Поле "Поломка" обязательно для заполнения.',
            'problem_id.exists' => 'Выбранная поломка не найдена.',
            'title.required' => 'Поле "Название решения" обязательно для заполнения.',
            'title.string' => 'Поле "Название решения" должно быть строкой.',
            'title.max' => 'Поле "Название решения" не должно превышать :max символов.',
        ];
    }


}
