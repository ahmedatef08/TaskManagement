<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],

            'description' => ['sometimes', 'nullable', 'string'],

            'status' => [
                'sometimes',
                Rule::in(['pending', 'in_progress', 'done']),
            ],

            'priority' => [
                'sometimes',
                Rule::in(['low', 'medium', 'high']),
            ],

            'category_id' => [
                'sometimes',
                Rule::exists('categories', 'id')
                    ->where('user_id', $this->user()->id),
            ],
        ];
    }
}
