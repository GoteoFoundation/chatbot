<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\User;
use Illuminate\Support\Facades\Hash;

class RegisterAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'register:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register admin user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $info = $this->askInfo();
        if($user = $this->createAdmin($info)) {
            $this->displayUser($user);
        } else {
            $this->displayError();
        }
    }

    /**
     * Display created user.
     *
     * @param array $user
     * @return void
     */
    private function displayUser($user)
    {
        $headers = ['Name', 'Email'];

        $fields = [
            'name' => $user->name,
            'email' => $user->email
        ];

        $this->info('Admin created!');
        $this->table($headers, [$fields]);
    }

    /**
     * Display error.
     *
     * @return void
     */
    private function displayError()
    {
        $this->error('Admin could not be created!');
    }

    /**
     * Creates the user with given information.
     *
     * @param array $info
     * @return User|bool
     */
    private function createAdmin($info)
    {
        $user = new User([
            'name' => $info['name'],
            'email' => $info['email'],
            'password' => Hash::make($info['password']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $user->save() ? $user : false;
    }

    /**
     * Ask for user info.
     *
     * @return array
     */
    private function askInfo()
    {
        $details['name'] = $this->ask('Name');
        $details['email'] = $this->ask('Email');

        while (!filter_var($details['email'], FILTER_VALIDATE_EMAIL)) {

            $details['email'] = $this->ask('Email');
        }

        $details['password'] = $this->secret('Password');
        $details['confirm_password'] = $this->secret('Confirm password');

        while (!$this->isMatch($details['password'], $details['confirm_password'])) {

            $details['password'] = $this->secret('Password');
            $details['confirm_password'] = $this->secret('Confirm password');
        }

        return $details;
    }

    /**
     * Check if password and confirm password matches.
     *
     * @param string $password
     * @param string $confirmPassword
     * @return bool
     */
    private function isMatch($password, $confirmPassword)
    {
        return $password === $confirmPassword;
    }
}
