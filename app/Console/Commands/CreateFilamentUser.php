<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateFilamentUser extends Command
{
    protected $signature = 'gm:create-admin {--name=Admin} {--email=} {--password=}';

    protected $description = 'Create an admin user for Filament (G&M Autospares)';

    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Name', 'Admin');
        $email = $this->option('email') ?: $this->ask('Email (for login)');
        $password = $this->option('password') ?: $this->secret('Password');

        if (!$email || !$password) {
            $this->error('Email and password are required.');
            return 1;
        }

        if (User::where('email', $email)->exists()) {
            $this->error('A user with that email already exists.');
            return 1;
        }

        // Hashed here rather than left to User's `hashed` cast: Laravel 10 never
        // applies a model's casts() method, so the password was stored as typed
        // and could not log in. On newer Laravel the cast sees the value is
        // already hashed and leaves it alone, so this never double-hashes.
        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->info('Admin user created. Log in at /admin with: ' . $email);
        return 0;
    }
}
