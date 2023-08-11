<?php

namespace App\Http\Requests\Project;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class StoreTodoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function prepareForValidation()
    {
        $this->merge([
            'due_start' => $this->due_start ? strtotime(Carbon::parse($this->due_start)) : null,
            'due_end' => $this->due_end ? strtotime(Carbon::parse($this->due_end)) :
                ($this->due_start ? strtotime(Carbon::parse($this->due_start)) : null),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'due_start' => 'nullable',
            'due_end' => 'nullable',
        ];
    }
}
