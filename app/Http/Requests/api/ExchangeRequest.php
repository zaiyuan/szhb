<?php

namespace App\Http\Requests\api;

use App\Models\Wallet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExchangeRequest extends FormRequest
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
            'from_currency_type'=>[
                'required',
                Rule::in(Wallet::getCurrencyTypes())
            ],
            'money'=>'required|numeric'
        ];
    }
}
