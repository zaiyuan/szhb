<?php

namespace App\Http\Requests\admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddCurrencyRequest extends FormRequest
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
            'user_id'=>['required'],
            'currency_type'=>[
                'required',
                Rule::in(['btc','teh','usdt','aic'])
            ],
            'plus_or_minus'=>[
                'required',
                Rule::in(['plus','minus'])
            ],
            'addValue'=>[
                'required',
                'min:0'
            ]
        ];
    }

    public function messages()
    {
        return [
            'addValue.min'=>'操作金额必须大于0'
        ];
    }
}
