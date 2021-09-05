<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlexEventRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payload' => ['required', 'json'],
            'thumb' => ['file', 'required_if:payload,library.new'],
        ];
    }
}
