<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdminCommand extends Command
{
    protected $signature = 'app:create-admin {--force : Explicitly allow creating an additional administrator with confirmation}';

    protected $description = 'Securely create the initial administrator account interactively';

    public function handle(): int
    {
        $adminExists = User::query()->where('role', User::ROLE_ADMIN)->exists();

        if ($adminExists) {
            if (! $this->option('force')) {
                $this->error('An administrator account already exists. Refusing to create a second administrator.');
                $this->line('If you intentionally need another admin account, re-run with --force and confirm interactively.');

                return self::FAILURE;
            }

            if (! $this->confirm('An administrator already exists. Are you sure you want to create an additional administrator?', false)) {
                $this->warn('Operation cancelled.');

                return self::FAILURE;
            }
        }

        $name = (string) $this->ask('Administrator full name');
        $email = (string) $this->ask('Administrator email address');
        $password = (string) $this->secret('Administrator password (minimum 8 characters)');
        $passwordConfirmation = (string) $this->secret('Confirm administrator password');

        $validator = Validator::make(
            [
                'name' => trim($name),
                'email' => strtolower(trim($email)),
                'password' => $password,
                'password_confirmation' => $passwordConfirmation,
            ],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'confirmed', Password::min(8)],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $validated = $validator->validated();

        $admin = User::createWithRole([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ], User::ROLE_ADMIN);

        $this->info("Administrator account created successfully for {$admin->email}.");

        return self::SUCCESS;
    }
}
