<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use App\Models\Customer;
use App\Models\Operation;

class CustomerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_customers()
    {
        Customer::factory()->count(2)->create();
        $response = $this->getJson('/api/customers');
        $response->assertOk()->assertJsonStructure([
            'data' => [
                ['id', 'name', 'email']
            ]
        ]);
    }

    public function test_store_creates_customer()
    {
        $data = ['name' => 'Test', 'email' => 'test@example.com'];
        $response = $this->postJson('/api/customers', $data);
        $response->assertCreated()->assertJsonFragment(['name' => 'Test']);
        $this->assertDatabaseHas('customers', $data);
    }

    public function test_show_returns_customer()
    {
        $customer = Customer::factory()->create();
        $response = $this->getJson('/api/customers/' . $customer->id);
        $response->assertOk()->assertJsonFragment(['id' => $customer->id]);
    }

    public function test_update_modifies_customer()
    {
        $customer = Customer::factory()->create();
        $response = $this->putJson('/api/customers/' . $customer->id, ['name' => 'Updated']);
        $response->assertOk()->assertJsonFragment(['name' => 'Updated']);
    }

    public function test_destroy_deletes_customer()
    {
        $customer = Customer::factory()->create();
        $response = $this->deleteJson('/api/customers/' . $customer->id);
        $response->assertNoContent();
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }
}

class OperationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_operations()
    {
        Operation::factory()->count(2)->create();
        $response = $this->getJson('/api/operations');
        $response->assertOk()->assertJsonStructure([['id', 'customer_id', 'type', 'amount']]);
    }

    public function test_store_creates_operation()
    {
        $customer = Customer::factory()->create();
        $data = ['customer_id' => $customer->id, 'type' => 'deposit', 'amount' => 100];
        $response = $this->postJson('/api/operations', $data);
        $response->assertCreated()->assertJsonFragment(['type' => 'deposit']);
        $this->assertDatabaseHas('operations', $data);
    }

    public function test_show_returns_operation()
    {
        $operation = Operation::factory()->create();
        $response = $this->getJson('/api/operations/' . $operation->id);
        $response->assertOk()->assertJsonFragment(['id' => $operation->id]);
    }

    public function test_apply_operation_dispatches_job()
    {
        Queue::fake();
        $operation = Operation::factory()->create();
        $response = $this->postJson('/api/operations/' . $operation->id . '/apply');
        $response->assertOk();
        // Exemplo: Queue::assertPushed(ApplyOperationJob::class);
    }
}
