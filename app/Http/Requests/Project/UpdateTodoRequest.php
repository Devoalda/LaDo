<?php

namespace App\Http\Requests\Project;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTodoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Prepare the data for validation.
     */
    public function prepareForValidation(): void
    {
        $this->merge([
            'completed_at' => $this->completed_at ? strtotime(Carbon::parse('now')) : null,
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
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'due_start' => 'nullable',
            'due_end' => 'nullable',
            'completed_at' => 'nullable',
        ];
    }
}
