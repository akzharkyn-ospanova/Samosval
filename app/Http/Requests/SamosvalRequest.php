<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SamosvalRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|integer|in:0,1,2',
            'system_id' => [
                'required',
                'string',
                'size:8',
                'regex:/^[RCL]{3}[0-9]{5}$/u',
            ],
            'type' => 'required|string|min:2|max:50',
            'address' => 'required|string|min:5|max:100',
            'serial_number' => [
                'required',
                'string',
                'size:10',
                'regex:/^S[0-9]{9}$/u',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Необходимо выбрать статус техники: "Активен/Неактивен/В ремонте".',
            'status.in' => 'Допустимые статусы техники: "Активен", "Неактивен", "В ремонте".',

            'system_id.required' => 'Заполните поле "Системный номер".',
            'system_id.size' => 'Системный номер должен содержать ровно 8 символов.',
            'system_id.regex' => 'Формат системного номера: 3 латинские буквы (RCL) + 5 цифр (например, RCL12345).',

            'type.required' => 'Заполните поле "Тип техники".',
            'type.min' => 'Тип техники должен содержать не менее 2 символов.',
            'type.max' => 'Тип техники должен содержать не более 50 символов.',

            'address.required' => 'Заполните поле "Адрес / объект техники".',
            'address.min' => 'Адрес должен содержать не менее 5 символов.',
            'address.max' => 'Адрес должен содержать не более 100 символов.',

            'serial_number.required' => 'Заполните поле "Серийный номер".',
            'serial_number.size' => 'Серийный номер должен содержать ровно 10 символов.',
            'serial_number.regex' => 'Формат серийного номера: латинская S + 9 цифр (например, S123456789).',
        ];
    }
}
