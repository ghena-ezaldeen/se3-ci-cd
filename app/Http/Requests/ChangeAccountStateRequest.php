<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeAccountStateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'action' => ['required', 'in:activate,freeze,suspend,close'],
        ];
    }

}
