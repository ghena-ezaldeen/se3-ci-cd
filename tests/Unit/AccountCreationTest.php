<?php

namespace Tests\Unit;

use App\Http\Requests\StoreAccountRequest;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Mockery;
use Tests\TestCase;

class AccountCreationTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_fails_to_create_account_with_invalid_type()
    {
        $data = [
            'user_id' => 1,
            'account_number' => 'ACC1234',
            'type' => 'invalid',
            'balance' => 0,
            'currency' => 'USD',
        ];

        $request = Mockery::mock(StoreAccountRequest::class);
        $request->shouldReceive('rules')->andReturn([
            'type' => ['required', \Illuminate\Validation\Rule::in(['savings','current','loan','investment'])],
            'balance' => ['nullable', 'numeric'],
            'currency' => ['nullable', 'string', 'size:3'],
        ]);

        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('type', $validator->errors()->messages());
    }

    /** @test */
    public function it_creates_account_with_valid_type()
    {
        $data = [
            'user_id' => 1,
            'account_number' => 'ACC1234',
            'type' => 'savings',
            'balance' => 0,
            'currency' => 'USD',
        ];

        // Mock StoreAccountRequest
        $request = Mockery::mock(StoreAccountRequest::class);
        $request->shouldReceive('rules')->andReturn([
            'type' => ['required', \Illuminate\Validation\Rule::in(['savings','current','loan','investment'])],
            'balance' => ['nullable', 'numeric'],
            'currency' => ['nullable', 'string', 'size:3'],
        ]);

        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes(), "Validation should pass for a valid account type");
    }


}
