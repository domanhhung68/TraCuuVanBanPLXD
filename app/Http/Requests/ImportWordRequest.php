<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportWordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'document' => ['required', 'file', 'mimes:docx', 'max:102400'],
        ];
    }

    public function messages(): array
    {
        return [
            'document.required' => 'Vui lòng chọn file Word để import.',
            'document.file' => 'Tệp tải lên không hợp lệ.',
            'document.mimes' => 'Chỉ hỗ trợ file .docx.',
            'document.max' => 'Kích thước file tối đa là 100MB.',
        ];
    }
}
