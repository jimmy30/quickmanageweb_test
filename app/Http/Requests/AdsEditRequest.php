<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdsEditRequest extends FormRequest
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
            'title' => 'required',
            'url' => 'required|url',
            'start_time' => 'required',
            'end_time' => 'required|date|after:start_time',
        ];
    }
}
