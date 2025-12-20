<?php
namespace App\Services;

use App\Contracts\AuthServiceInterface;
use App\Contracts\UserRepositoryInterface;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService implements AuthServiceInterface
{
    private UserRepositoryInterface $users;

    public function __construct(UserRepositoryInterface $users)
    {
        $this->users = $users;
    }

    public function register(array $data)
    {

        return DB::transaction(function () use ($data) {

            $roleName = $data['role'] ;

            $data['password'] = Hash::make($data['password']);

            $user = $this->users->create($data);

            $role = Role::where('name', $roleName)->firstOrFail();

            $user->roles()->attach($role->id);

            return $user;
        });

    }

    public function login(array $data)
    {
        $user = $this->users->findByEmail($data['email']);

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return  [
            'token' => $token,
            'user'  => $user,
        ];
    }

}
