<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;
        return [
            'username' => 'required|string|min:3|max:50|unique:users,username,' . $userId,
            'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
        ];
    }
}

