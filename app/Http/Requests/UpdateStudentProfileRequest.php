<?php

/**
 * Validates a student's own profile update.
 */

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesPersonalDetails;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateStudentProfileRequest extends FormRequest
{
    use ValidatesPersonalDetails;

    public function authorize(): bool
    {
        return $this->user()?->isStudent() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'phone' => $this->phoneRules(),
            'date_of_birth' => $this->dateOfBirthRules(),
            'gender' => $this->genderRules(),
            'address' => ['nullable', 'string', 'max:1000'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'confirmed', Password::min(8), 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            ...$this->personalDetailMessages(),
            'current_password.required_with' => 'Enter your current password to set a new one.',
            'current_password.current_password' => 'Your current password is incorrect.',
        ];
    }
}
