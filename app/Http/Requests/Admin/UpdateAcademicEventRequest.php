<?php

namespace App\Http\Requests\Admin;

use App\Models\AcademicEvent;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAcademicEventRequest extends FormRequest
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
        $eventId = $this->route('academic_event')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', "unique:academic_events,slug,{$eventId}"],
            'description' => ['nullable', 'string'],
            'event_type' => ['required', 'in:' . implode(',', AcademicEvent::EVENT_TYPES)],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'location' => ['nullable', 'string', 'max:255'],
            'google_event_url' => ['nullable', 'url', 'max:2048'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }
}
