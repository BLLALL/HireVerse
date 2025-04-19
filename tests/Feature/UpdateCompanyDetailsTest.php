<?php
//UpdateCompanyDetailsTest
use App\Models\Company;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\patchJson;

beforeEach(function () {
    // Create a company for testing
    $this->company = Company::factory()->create([
        'name' => 'Original Company Name',
        'ceo' => 'Original CEO',
        'email' => 'original@example.com',
        'location' => 'Original Location',
        'employee_no' => 50,
        'website_url' => 'https://original.com',
        'description' => 'Original description',
        'insights' => 'Original insights',
        'industry' => 'Original Industry',
    ]);

    // Authenticate as this company
    Sanctum::actingAs($this->company, ['*'], 'companies');
});

it('updates company details successfully', function () {
    $updatedData = [
        'name' => 'Updated Company Name',
        'ceo' => 'Updated CEO',
        'location' => 'Updated Location',
        'employee_no' => 100,
        'website_url' => 'https://updated.com',
        'description' => 'Updated description',
        'insights' => 'Updated insights',
        'industry' => 'Updated Industry',
    ];

    $response = patchJson("/api/company/{$this->company->id}", $updatedData);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Company details updated successfully',
            'data' => $updatedData
        ]);

    $this->assertDatabaseHas('companies', [
        'id' => $this->company->id,
        'name' => 'Updated Company Name',
        'ceo' => 'Updated CEO',
        'location' => 'Updated Location',
        'employee_no' => 100,
        'website_url' => 'https://updated.com',
        'description' => 'Updated description',
        'insights' => 'Updated insights',
        'industry' => 'Updated Industry',
    ]);
});

it('prevents updating email through this endpoint', function () {
    $updatedData = [
        'name' => 'Valid Name',
        'email' => 'newemail@example.com', // This shouldn't change
    ];

    $response = patchJson("/api/company/{$this->company->id}", $updatedData);

    $response->assertStatus(200);
    
    // Check that name was updated but email wasn't
    $this->assertDatabaseHas('companies', [
        'id' => $this->company->id,
        'name' => 'Valid Name',
        'email' => 'original@example.com', // Original email remains
    ]);
});

it('validates required fields', function () {
    $response = patchJson("/api/company/{$this->company->id}", [
        'name' => '',
        'ceo' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'ceo']);
});

it('prevents unauthorized companies from updating others', function () {
    // Create another company
    $anotherCompany = Company::factory()->create();
    
    // Authenticate as this other company
    Sanctum::actingAs($anotherCompany, ['*'], 'companies');
    
    $response = patchJson("/api/company/{$this->company->id}", [
        'name' => 'Unauthorized Update',
    ]);

    $response->assertStatus(403);
    
    // Ensure data wasn't changed
    $this->assertDatabaseHas('companies', [
        'id' => $this->company->id,
        'name' => 'Original Company Name',
    ]);
});

it('requires authentication', function () {
    // No authentication
    auth()->guard('sanctum')->logout();
    
    $response = patchJson("/api/company/{$this->company->id}", [
        'name' => 'Unauthenticated Update',
    ]);

    $response->assertStatus(401);
});

it('validates number fields', function () {
    $response = patchJson("/api/company/{$this->company->id}", [
        'employee_no' => 'not-a-number',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['employee_no']);
});

it('validates URL format', function () {
    $response = patchJson("/api/company/{$this->company->id}", [
        'website_url' => 'not-a-valid-url',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['website_url']);
});

it('allows partial updates', function () {
    $response = patchJson("/api/company/{$this->company->id}", [
        'name' => 'Only Name Updated',
    ]);

    $response->assertStatus(200);
    
    $this->assertDatabaseHas('companies', [
        'id' => $this->company->id,
        'name' => 'Only Name Updated',
        'ceo' => 'Original CEO', // Other fields remain unchanged
        'location' => 'Original Location',
    ]);
});

it('handles non-existent company gracefully', function () {
    $nonExistentId = Company::max('id') + 1;
    
    $response = patchJson("/api/company/{$nonExistentId}", [
        'name' => 'Updated Name',
    ]);

    $response->assertStatus(404);
});