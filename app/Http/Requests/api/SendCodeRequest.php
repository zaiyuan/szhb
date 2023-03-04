<?php

namespace App\Http\Requests\api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendCodeRequest extends FormRequest
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
            'login_type'=>[
                'required',
                Rule::in(['email','mobile'])
            ],
            'email' => [
                'requiredIf:login_type,email',
            ],
            'mobile' => [
                'requiredIf:login_type,mobile',
            ],
            'area_code'=>[
                'requiredIf:login_type,mobile',
            ],
        ];
    }
}
