<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'location' => 'required|string',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'roomCount' => 'required|integer|min:1',
        ];
    }
}

