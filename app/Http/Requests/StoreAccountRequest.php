<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAccountRequest extends FormRequest
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
                 'parent_id' => ['nullable', Rule::exists('accounts', 'id')
                     ->where(fn ($q) => $q->where('user_id', auth()->id()))],
                 'type' => ['required', 'string'],
                 'currency' => ['nullable', 'string', 'size:3'],
                 'balance' => ['nullable', 'numeric'],
                 'interest_rate' => ['nullable', 'numeric','min:0'],
        ];
    }
}
