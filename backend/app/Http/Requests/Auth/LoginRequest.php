<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Login Form Request (SRS_LOGIN Section 2.3 & 3.1)
 *
 * Validates login credentials supporting either Email or Username.
 */
class LoginRequest extends FormRequest
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
            // Accepts either 'login' (username or email) or backwards compatible 'email'
            'login' => ['required_without:email', 'string', 'max:255'],
            'email' => ['required_without:login', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get the login identifier (either 'login' or 'email').
     */
    public function getLoginIdentifier(): string
    {
        return (string) ($this->input('login') ?? $this->input('email'));
    }

    /**
     * Custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'login.required_without' => 'Please provide your email address or username.',
            'email.required_without' => 'Please provide your email address or username.',
            'password.required' => 'Password is required.',
        ];
    }
}
