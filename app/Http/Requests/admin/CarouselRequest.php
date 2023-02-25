<?php

namespace App\Http\Requests\admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CarouselRequest extends FormRequest
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
            'title'=>[
                'required','max:50'
            ],
            'image'=>['required'],
            'is_online'=>['required',Rule::in([1,2])],
            'sort'=>['required','integer'],
            'link'=>'max:255'
        ];
    }

    public function messages()
    {
        return [
            'title.required'=>'标题不能为空',
            'title.max'=>'标题长度不能超过50',
            'image.required'=>'图片不能为空',
            'is_online.required'=>'状态不能为空',
            'is_online.in'=>'状态取值为1或2',
            'sort.required'=>'排序不能为空',
            'sort.integer'=>'排序只能是整数',
            'link.max'=>'链接长度不能超过255'
        ];
    }
}
