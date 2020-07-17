<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MachineTest extends TestCase
{

    use RefreshDatabase;

    private $baseHeaders = [ 'Content-Type' => 'application/json' ];

    /**
     * Can we get inventory?
     *
     * @return void
     */
    public function testCanGetInventory()
    {
        $response = $this->get('/api/inventory');
        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    /**
     * Can we insert coins?
     *
     * @return void
     */
    public function testCanInsertCoin()
    {
        $response = $this->json('PUT', '/api', [
            'coin' => 1,
        ]);
        $response->assertStatus(204);
        $response->assertHeader('X-Coins', 1);

        $response = $this->json('PUT', '/api', [
            'coin' => 2,
        ]);
        $response->assertStatus(204);
        $response->assertHeader('X-Coins', 2);
    }


    /**
     * Make sure we can insert all of our coins
     *
     * @return void
     */
    public function testCanInsertMaxCoins()
    {
        $response = $this->get('api/user', $this->baseHeaders);
        $coins = $response->json('coins');

        $response = $this->json('PUT', '/api', [
            'coin' => $coins,
        ], $this->baseHeaders);

        $response->assertStatus(204);
        $response->assertHeader('X-Coins', $coins);
    }

    /**
     * Make sure we can't insert more coins than we have
     *
     * @return void
     */
    public function testCantInsertOverMaxCoins()
    {
        $response = $this->get('api/user', $this->baseHeaders);
        $coins = $response->json('coins');

        $response = $this->json('PUT', '/api', [
            'coin' => $coins + 1,
        ], $this->baseHeaders);

        $response->assertStatus(204);
        $response->assertHeader('X-Coins', $coins);
    }

    /**
     * Verify that we can refund coins and end with what we started with
     *
     * @return void
     */
    public function testCanRefundCoins()
    {
        //We have multiple coins to start with
        $response = $this->get('api/user', $this->baseHeaders);
        $response->assertStatus(200);
        $startingCoins = (int)$response->json('coins');
        assert($startingCoins >= 2, 'We have multiple coins');

        // Put a single coin in
        $response = $this->json('PUT', '/api', [
            'coin' => 1,
        ], $this->baseHeaders);
        $response->assertStatus(204);
        $response->assertHeader('X-Coins', 1);

        // We have one less coin than we started with
        $response = $this->get('api/user', $this->baseHeaders);
        $response->assertStatus(200);
        $coins = (int)$response->json('coins');
        assert($coins === ($startingCoins - 1), 'We have one less coin than we started with');

        // Do a refund
        $response = $this->delete('/api', $this->baseHeaders);
        $response->assertStatus(204);
        $response->assertHeader('X-Coins', 1);

        // We have the same number of coins that we started with
        $response = $this->get('api/user', $this->baseHeaders);
        $response->assertStatus(200);
        $coins = (int)$response->json('coins');
        assert($coins === $startingCoins, 'We have the same number of coins that we started with');
    }

    /**
     * Verify that we cannot refund without inserting
     *
     * @return void
     */
    public function testCannotRefundEmptyMachine()
    {
        // We have multiple coins to start with
        $response = $this->get('api/user', $this->baseHeaders);
        $response->assertStatus(200);
        $startingCoins = (int)$response->json('coins');
        assert($startingCoins >= 2, 'We have multiple coins');

        // Do a refund
        $response = $this->delete('/api', $this->baseHeaders);
        $response->assertStatus(204);
        $response->assertHeader('X-Coins', 0);

        // We have the same number of coins that we started with
        $response = $this->get('api/user', $this->baseHeaders);
        $response->assertStatus(200);
        $coins = (int)$response->json('coins');
        assert($coins === $startingCoins, 'We have the same number of coins that we started with');
    }

    /**
     * Verify that we cannot refund without inserting
     *
     * @return void
     */
    public function testCanPurchaseFirstItem()
    {
        // User has more coins than needed to buy an item
        $response = $this->get('api/user', $this->baseHeaders);
        $response->assertStatus(200);
        $startingCoins = (int)$response->json('coins');
        assert($startingCoins >= 3, 'We have more coins than we need');

        // Machine has more than 2 items in the first inventory slot
        $response = $this->get('/api/inventory');
        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $startingItems = (int)$response->json(0);
        assert($startingItems >= 2, 'We have multiple items in machine slot 0');

        // Put two coins in
        $response = $this->json('PUT', '/api', [
            'coin' => 2,
        ], $this->baseHeaders);
        $response->assertStatus(204);
        $response->assertHeader('X-Coins', 2);

        // Buy an item
        $response = $this->put('api/inventory/0', $this->baseHeaders);
        $response->assertStatus(200);
        $response->assertJson(['quantity' => 1]);

        // User has two coins less than started with
        $response = $this->get('api/user', $this->baseHeaders);
        $response->assertStatus(200);
        $coins = (int)$response->json('coins');
        assert($coins === ($startingCoins - 2), 'Purchased item cost us coins');

        // Machine has one less item than it started with
        $response = $this->get('/api/inventory');
        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $endingItems = (int)$response->json(0);
        assert($endingItems === ($startingItems - 1), 'We have multiple items in machine slot 0');
    }


    /**
     * Verify that we cannot refund without inserting
     *
     * @return void
     */
    public function testInsufficientFunds()
    {
        // User has more coins than needed to buy an item
        $response = $this->get('api/user', $this->baseHeaders);
        $response->assertStatus(200);
        $startingCoins = (int)$response->json('coins');
        assert($startingCoins >= 3, 'We have more coins than we need');

        // Machine has more than 2 items in the first inventory slot
        $response = $this->get('/api/inventory');
        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $startingItems = (int)$response->json(0);
        assert($startingItems >= 2, 'We have multiple items in machine slot 0');

        // Put one coin in
        $response = $this->json('PUT', '/api', [
            'coin' => 1,
        ], $this->baseHeaders);
        $response->assertStatus(204);
        $response->assertHeader('X-Coins', 1);

        // Buy an item
        $response = $this->put('api/inventory/0', $this->baseHeaders);
        $response->assertStatus(403);
    }

    /**
     * Verify can't buy from out of stock machine
     *
     * @return void
     */
    public function testOutOfStock()
    {
        // User has more coins than needed to buy an item
        $response = $this->get('api/user', $this->baseHeaders);
        $response->assertStatus(200);
        $startingCoins = (int)$response->json('coins');
        assert($startingCoins >= 3, 'We have more coins than we need');
        User::first()->update(['coins' => 100]);

        // User has enough coins to buy out shop
        $response = $this->get('api/user', $this->baseHeaders);
        $response->assertStatus(200);
        $startingCoins = (int)$response->json('coins');
        assert($startingCoins >= 80, 'We have more coins than we need');

        // We have several items in 2nd machine slot
        $response = $this->get('/api/inventory');
        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $startingItems = (int)$response->json(1);
        assert($startingItems >= 3, 'We have multiple items in machine slot 1');

        foreach(range(1, $startingItems) as $i) {
            // Put two coins in
            $response = $this->json('PUT', '/api', [
                'coin' => 2,
            ], $this->baseHeaders);
            $response->assertStatus(204);
            $response->assertHeader('X-Coins', 2);

            // Buy an item
            $response = $this->put('api/inventory/1', $this->baseHeaders);
            $response->assertStatus(200);
            $response->assertJson(['quantity' => 1]);
        }

        // Verify out of stock
        $response = $this->get('/api/inventory');
        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $endingItems = (int)$response->json(1);
        assert($endingItems === 0, 'We are out of items');

        // Put two coins in
        $response = $this->json('PUT', '/api', [
            'coin' => 2,
        ], $this->baseHeaders);
        $response->assertStatus(204);
        $response->assertHeader('X-Coins', 2);

        // Cant buy item
        $response = $this->put('api/inventory/1', $this->baseHeaders);
        $response->assertStatus(404);
    }

}
