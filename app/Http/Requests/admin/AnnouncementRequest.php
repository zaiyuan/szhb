<?php

namespace App\Http\Requests\admin;

use Illuminate\Foundation\Http\FormRequest;

class AnnouncementRequest extends FormRequest
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
            'title'=>'required|max:50',
            'content'=>'required',
            'sort'=>'integer',
            'is_online'=>'integer|in:1,2',
        ];
    }

    public function messages()
    {
        return [
            'title.required'=>'标题不能为空',
            'content.required'=>'内容不能为空',
            'sort.integer'=>'排序必须是整数',
            'is_online.integer'=>'状态必须是整数',
            'is_online.in'=>'状态取值必须是1,2',
        ];
    }
}
