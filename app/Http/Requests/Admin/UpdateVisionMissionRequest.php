<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVisionMissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && in_array($user->role, ['admin', 'super_admin'], true);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'vision_title' => ['required', 'string', 'max:255'],
            'vision_text' => ['required', 'string'],
            'mission_title' => ['required', 'string', 'max:255'],
            'mission_text' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
