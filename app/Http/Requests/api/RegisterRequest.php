<?php

namespace App\Http\Requests\api;

use App\Rules\Mobile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
            'area_code'=>['required'],
            'account' => [
                'required',
            ],
            'code'=>'required',
            'password'=>['required','confirmed']
        ];
    }

    public function messages()
    {
        return [
            'area_code.required' => '地区代码不能为空',
            'account.required'=>'账号不能为空',
            'code.required'=>'验证码不能为空',
            'password.required'=>'密码不能为空不能为空',
            'password.confirmed'=>'两次输入密码不同',
        ];
    }
}
