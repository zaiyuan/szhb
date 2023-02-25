<?php

namespace App\Http\Requests\admin;

use App\Rules\Mobile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminRequest extends FormRequest
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
            'role_id'=>'required',
            'username'=>[
                'required',
                Rule::unique('system_admin')->ignore($this->id),
                'max:100',
                'alpha_num'
            ],
            'realname'=>'required|max:50',
            'password'=>"required_if:id,0|confirmed",
//            'mobile'=>[
//                'required',
//                new Mobile()
//            ]
        ];
    }

    public function messages()
    {
        return [
            'role_id.required'=>'角色不能为空',
            'username.required'=>'用户名不能为空',
            'username.unique'=>'用户名已存在',
            'username.max'=>'用户名长度不能超过100',
            'username.alpha_num'=>'用户名只能由字母和数字组成',
            'password.required_if'=>'密码不能为空',
            'password.confirmed'=>'确认密码必须和密码相同',
//            'mobile.required'=>'电话号码不能为空',
//            'mobile.regex'=>'电话号码格式错误',
            'realname.required'=>'管理员姓名不能为空',
            'realname.max'=>'管理员姓名长度不能超过50'
        ];
    }
}
