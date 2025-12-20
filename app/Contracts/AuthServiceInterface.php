<?php
namespace App\Contracts;

interface AuthServiceInterface
{
    public function register(array $data);

    public function login(array $credentials);
}
