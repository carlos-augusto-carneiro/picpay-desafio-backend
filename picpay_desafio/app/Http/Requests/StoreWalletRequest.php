<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWalletRequest extends FormRequest
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
            "user_id"=> "required|string|exists:users,id",
            "balance"=> "required|numeric|min:0",
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'O campo user_id é obrigatório.',
            'user_id.exists' => 'O user_id fornecido não existe.',
            'balance.required' => 'O campo balance é obrigatório.',
            'balance.numeric' => 'O campo balance deve ser um número.',
            'balance.min' => 'O campo balance deve ser no mínimo 0.',
        ];
    }
}
