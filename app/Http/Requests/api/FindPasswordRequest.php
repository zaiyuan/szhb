<?php

namespace App\Http\Requests\api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FindPasswordRequest extends FormRequest
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
            'area_code'=>['requiredIf:login_type,mobile'],
            'email' => [
                'requiredIf:login_type,email',
            ],
            'mobile' => [
                'requiredIf:login_type,mobile',
            ],
            'code'=>'required',
            'password'=>['required','confirmed']
        ];
    }

    public function messages()
    {
        return [
            'account.required'=>'邮箱/电话不能为空',
            'code.required'=>'验证码不能为空',
            'password.required'=>'密码不能为空',
            'password.confirmed'=>'两次输入密码不同',
        ];
    }
}
