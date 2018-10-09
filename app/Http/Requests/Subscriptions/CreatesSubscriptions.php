<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatesSubscriptions extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'auto_renew'    => ['required', 'boolean'],
            'resource'      => ['required', 'string', 'max:191', 'in:users,groups'],
            'change_type'   => ['required', 'string', 'max:191'],
        ];
    }
}
