<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlexEventRequest extends FormRequest
{
    /** @return array<string, array<mixed>> */
    public function rules(): array
    {
        return [
            'payload' => ['required', 'json'],
            'thumb' => ['file', 'required_if:payload,library.new'],
        ];
    }
}
