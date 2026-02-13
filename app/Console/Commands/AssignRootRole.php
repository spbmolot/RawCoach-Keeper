<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class AssignRootRole extends Command
{
    protected $signature = 'user:assign-root {email}';
    protected $description = 'Assign root role to a user by email';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found");
            return 1;
        }

        $user->assignRole('root');
        $this->info("Role 'root' assigned to {$user->name} ({$user->email})");
        
        return 0;
    }
}
