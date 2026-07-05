<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FavoriteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'law_id' => ['required', 'exists:laws,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'law_id.required' => 'Vui lòng chọn văn bản.',
            'law_id.exists' => 'Văn bản không tồn tại.',
        ];
    }
}
