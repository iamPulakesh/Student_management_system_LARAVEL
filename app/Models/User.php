<?php

/**
 * Model for a login account and its role.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_TEACHER = 'teacher';

    public const ROLE_STUDENT = 'student';

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_TEACHER,
        self::ROLE_STUDENT,
    ];

    /**
     * Mass-assignable attributes.
     * Note: 'role' is intentionally excluded so it cannot be mass-assigned from untrusted input.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Hidden attributes for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Create a user account with an explicitly assigned server-side role.
     *
     * @param  array{name: string, email: string, password: string}  $attributes
     */
    public static function createWithRole(array $attributes, string $role): self
    {
        if (! in_array($role, self::ROLES, true)) {
            throw new \InvalidArgumentException("Invalid user role [{$role}].");
        }

        $user = new self([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => $attributes['password'],
        ]);
        $user->role = $role;
        $user->save();

        return $user;
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function recordedAttendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'recorded_by_user_id');
    }

    public function recordedAssessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'recorded_by_user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isTeacher(): bool
    {
        return $this->role === self::ROLE_TEACHER;
    }

    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'admin.dashboard',
            self::ROLE_TEACHER => 'teacher.dashboard',
            default => 'student.dashboard',
        };
    }
}
