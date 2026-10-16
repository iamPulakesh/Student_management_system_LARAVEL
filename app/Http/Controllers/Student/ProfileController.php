<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStudentProfileRequest;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function update(UpdateStudentProfileRequest $request): RedirectResponse
    {
        $student = $this->resolveAuthorizedStudent($request);
        $validated = $request->validated();

        $student->update([
            'phone' => $validated['phone'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        if (! empty($validated['password'])) {
            $student->user->update([
                'password' => $validated['password'],
            ]);
        }

        return redirect()
            ->route('student.dashboard')
            ->with('success', 'Your profile details have been updated successfully.');
    }

    private function resolveAuthorizedStudent(Request $request): Student
    {
        return $request->user()->student()->with(['department', 'user'])->firstOrFail();
    }
}
