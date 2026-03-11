<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

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

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        $this->info('Admin user created. Log in at /admin with: ' . $email);
        return 0;
    }
}
