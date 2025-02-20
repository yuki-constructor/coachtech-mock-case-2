<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Employee;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;


class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:admins,email',
                'unique:employees,email'
            ],
            'password' =>  ['required', 'string', $this->passwordRules(), 'confirmed'],
        ])->validate();

        if (request()->is('admin/*')) {
            return Admin::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
        } else {
            $employee = Employee::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // メール認証メールを送信
            $employee->sendEmailVerificationNotification();

            return $employee;
        }
    }
}
