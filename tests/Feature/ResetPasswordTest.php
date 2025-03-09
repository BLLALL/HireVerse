<?php

use App\Models\Applicant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

it('returns validation error when email is not provided for password reset', function (): void {
    $this->postJson('api/password/forgot', [])
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['email']);
});

it('returns success response for valid email on reset password request', function (): void {
    $applicant = Applicant::factory()->create();
    $this->postJson('api/password/forgot', [$applicant->email()])
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(['message' => 'Password reset link sent']);
});

it('returns error on non-existent email on password reset request', function (): void {
    $this->postJson('api/password/forgot', [
        'email' => 'nonexistentemail@test.com',
    ])->assertStatus(Response::HTTP_BAD_REQUEST)
        ->assertJson(['message' => 'User not found']);
});

it('validates reset password request data', function (): void {
    $this->postJson('api/password/reset', [])
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['password', 'token']);
});

it('rejects invalid reset tokens', function (): void {
    $this->postJson('api/password/reset', [
        'password' => 'newPassword',
        'token' => 'invalid-token',
    ])->assertStatus(Response::HTTP_BAD_REQUEST)
        ->assertJson(['message' => 'Invalid token']);

});

it('resets password with valid token', function (): void {
    $applicant = Applicant::factory()->create();
    $token = Password::broker()->createToken($applicant);
    $this->postJson('api/password/reset', [
        'password' => 'newPassword',
        'token' => $token,
    ])->assertStatus(Response::HTTP_OK)
        ->assertJson(['message' => 'Password reset successfully']);

    $updatedApplicant = Applicant::find($applicant->id);
    expect(Hash::check('newPassword', $updatedApplicant->password))->toBeTrue();
});
