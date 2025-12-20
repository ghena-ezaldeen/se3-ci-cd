<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountRequest extends FormRequest
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
            'type' => ['nullable', 'string','in:savings,investment,loan,checking'],
            'state' => ['nullable', 'string'],
            'currency' => ['nullable', 'string', 'size:3'],
            'balance' => ['nullable', 'numeric'],
            'interest_rate' => ['nullable', 'numeric'],
        ];
    }
}
