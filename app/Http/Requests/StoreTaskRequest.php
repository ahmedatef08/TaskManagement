<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],

            'description' => ['nullable', 'string'],

            'status' => [
                'required',
                Rule::in(['pending', 'in_progress', 'done']),
            ],

            'priority' => [
                'required',
                Rule::in(['low', 'medium', 'high']),
            ],

            'category_id' => [
                'required',
                Rule::exists('categories', 'id')
                    ->where('user_id', $this->user()->id),
            ],
        ];
    }
}
