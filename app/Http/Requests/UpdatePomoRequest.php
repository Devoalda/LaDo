<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePomoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'pomo_start' => 'required|date_format:Y-m-d\TH:i',
            'pomo_end' => 'required|date_format:Y-m-d\TH:i|after:pomo_start',
            'notes' => 'nullable|string',
        ];
    }
}
