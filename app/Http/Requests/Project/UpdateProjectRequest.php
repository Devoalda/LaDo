<?php

namespace App\Http\Requests\Project;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'completed_at' => 'nullable'
        ];
    }

    public function validatedWithCompletedAt(): array
    {
        // Return safe data merged with completed_at to unix timestamp
        return array_merge(
            $this->validated(),
            [
                // Now or null
                'completed_at' => $this->completed_at ? Carbon::now()->timestamp : null,
            ]
        );
    }

    protected function passedValidation(): void
    {
        // Replace or add completed_at to the request, value is time now in unix format
        if ($this->has('completed_at')) {
            $this->request->add(['completed_at' => Carbon::now()->timestamp]);
        } else {
            $this->request->add(['completed_at' => null]);
        }
    }
}
