<?php

namespace App\Http\Requests;

use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;

class LoginRequest extends FortifyLoginRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['nullable', 'string', 'email', 'required_without:phone'],
            'phone' => ['nullable', 'string', 'required_without:email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'email.required_without' => 'Необходимо указать email или номер телефона.',
            'email.email' => 'Укажите корректный email адрес.',
            'phone.required_without' => 'Необходимо указать номер телефона или email.',
            'password.required' => 'Поле пароль обязательно для заполнения.',
        ];
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Ensure at least one identifier (email or phone) is provided
        if (! $this->filled('email') && ! $this->filled('phone')) {
            throw ValidationException::withMessages([
                'email' => ['Необходимо указать email или номер телефона.'],
            ]);
        }
    }
}
