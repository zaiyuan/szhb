<?php

namespace App\Http\Requests\admin;

use Illuminate\Foundation\Http\FormRequest;

class WalletRequest extends FormRequest
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
            'name'=>'required|max:50',
            'btc_address'=>'required|max:255',
            'teh_address'=>'required|max:255',
            'usdt_address'=>'required|max:255',
            'bxt_address'=>'required|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required'=>'钱包名称不能为空',
            'btc_address.required'=>'BTC币种地址不能为空',
            'teh_address.required'=>'TEH币种地址不能为空',
            'usdt_address.required'=>'USDT币种地址不能为空',
            'bxt_address.required'=>'本系统币种地址不能为空',
        ];
    }
}
