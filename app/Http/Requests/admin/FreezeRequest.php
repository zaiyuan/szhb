<?php

namespace App\Http\Requests\admin;

use App\Models\Wallet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FreezeRequest extends FormRequest
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
                Rule::in(Wallet::getCurrencyTypes())
            ],
            'plus_or_minus'=>[
                'required',
                Rule::in(['thaw','frozen'])
            ],
            'addValue'=>[
                'required',
                'min:0'
            ]
        ];
    }
}
