<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
            "wallet_id"=> "required|string|exists:wallets,id",
            "amount"=> "required|numeric|min:0",
            "type"=> "required|string|in:deposit,withdraw,transfer",
            "wallet_id_destination"=> "required|string|exists:wallets,id",
            "description"=> "nullable|string|max:255",
        ];
    }

    public function messages(): array
    {
        return [
            'wallet_id.required' => 'O campo wallet_id é obrigatório.',
            'wallet_id.exists' => 'O wallet_id fornecido não existe.',
            'amount.required' => 'O campo amount é obrigatório.',
            'amount.numeric' => 'O campo amount deve ser um número.',
            'amount.min' => 'O campo amount deve ser no mínimo 0.',
            'type.required'=> 'O campo type é obrigatório.',
            'type.in' => 'O campo type deve ser "sending" ou "receiving".',
            'wallet_id_destination.required' => 'O campo wallet_id_destination é obrigatório.',
            'wallet_id_destination.exists' => 'O wallet_id_destination fornecido não existe.',
            'description.max' => 'A descrição não pode exceder 255 caracteres.',
        ];
    }
}
