<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('id');

        return [
            'name' => 'nullable|string|min:3|max:255',
            'email' => [
                'nullable',
                'email',
                // Garante que o email seja único, mas ignora o dono atual desse ID
                Rule::unique('users', 'email')->ignore($userId),
            ],
            // A senha é nullable (opcional) na atualização
            'password' => 'nullable|min:8|max:16',
        ];
    }
}
