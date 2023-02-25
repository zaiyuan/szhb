<?php

namespace App\Http\Requests\admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
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
            'name'=>[
                'required',
                'max:30',
                Rule::unique('system_role')->ignore($this->id)
            ],
            'desc'=>'max:50',
        ];
    }

    public function messages()
    {
        return [
            'name.required'=>'角色名称不能为空',
            'name.max'=>'角色名称长度不能超过30',
            'desc.max'=>'角色描述长度不能超过50'
        ];
    }
}
