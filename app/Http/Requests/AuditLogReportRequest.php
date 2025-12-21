<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuditLogReportRequest extends FormRequest
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
                 'type' => 'required|in:daily,by_user,by_account',

                 'date' => 'required_if:type,daily|date',

                 'user_id' => 'required_if:type,by_user|exists:users,id',

                 'account_id' => 'required_if:type,by_account|exists:accounts,id',

             ];
    }
}
