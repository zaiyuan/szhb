<?php

namespace App\Http\Requests\admin;

use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest
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
            'parent_id'=>'exclude_if:cate,1|required',
            'title'=>'required|max:30',
            'description'=>'max:50',
            'sort'=>'integer',
            'icon'=>'max:20',
            'path'=>'required|max:255',
            'cate'=>'required|in:1,2',
            'name'=>'exclude_if:cate,1|max:50|required',
            'component'=>'exclude_if:cate,1|max:50|required'
        ];
    }

    public function messages()
    {
        return [
            'parent_id.required'=>'父级菜单不能为空',
            'title.required'=>'菜单名称不能为空',
            'title.max'=>'菜单名称长度不能超过30',
            'description.max'=>'菜单描述长度不能超过50',
            'sort.integer'=>'排序必须是整数',
            'icon.max'=>'图标长度不能超过20',
            'path.required'=>'路由不能为空',
            'path.max'=>'路由长度不能超过255',
            'cate.required'=>'类型不能为空',
            'cate.in'=>'类型取值必须是1或者2',
            'name.required'=>'组件名不能为空',
            'name.max'=>'组件名长度不能超过50',
            'component.required'=>'组件路径不能为空',
            'component.max'=>'组件路径长度不能超过50',
        ];
    }
}
