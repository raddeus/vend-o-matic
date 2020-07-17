<?php

namespace Tests\Unit;

use App\Exceptions\InsufficientFundsException;
use App\Exceptions\ItemNotFoundException;
use App\Exceptions\ItemOutOfStockException;
use App\Machine;
use App\Services\MachineService;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MachineServiceTest extends TestCase
{

    use RefreshDatabase;

    /** @var User */
    private $user;

    /** @var Machine */
    private $machine;

    /** @var MachineService */
    private $machineService;

    public function setUp(): void
    {
        parent::setUp();
        $this->machineService = new MachineService();

        $this->user = User::create([
            'username' => 'test_user',
            'coins' => 5,
        ]);

        $this->machine = Machine::create([
            'name' => 'default_machine',
            'coins' => 0,
            'user_id' => $this->user->id,
        ]);
        for ($i = 0; $i < 3; $i++) {
            $this->machine->items()->create([
                'count' => 5,
            ]);
        }
    }

    public function testInsertZeroCoins()
    {
        $inserted = $this->machineService->insertCoins($this->machine, $this->user, 0);
        $this->assertTrue($inserted === 0);
    }

    public function testInsertNegativeCoins()
    {
        $inserted = $this->machineService->insertCoins($this->machine, $this->user, -100);
        $this->assertTrue($inserted === 0);
    }

    public function testInsertOverMaxCoins()
    {
        $maxCoins = $this->user->coins;
        $inserted = $this->machineService->insertCoins($this->machine, $this->user, $maxCoins + 1);
        $this->assertTrue($inserted === $maxCoins);
    }

    public function testRefundZeroCoins()
    {
        $this->user->coins = 5;
        $this->user->save();
        $this->machineService->insertCoins($this->machine, $this->user, 0);
        $refundedAmount = $this->machineService->refundCoins($this->machine, $this->user);
        $this->assertTrue($refundedAmount === 0);
    }

    public function testRefundMaxCoins()
    {
        $this->user->coins = 5;
        $this->user->save();
        $this->machineService->insertCoins($this->machine, $this->user, 5);
        $refundedAmount = $this->machineService->refundCoins($this->machine, $this->user);
        $this->assertTrue($refundedAmount === 5);
    }

    public function testRefundOverMaxCoins()
    {
        $this->user->coins = 5;
        $this->user->save();
        $this->machineService->insertCoins($this->machine, $this->user, 6);
        $refundedAmount = $this->machineService->refundCoins($this->machine, $this->user);
        $this->assertTrue($refundedAmount === 5);
    }


    public function testCanPurchase()
    {
        $this->machineService->insertCoins($this->machine, $this->user, 5);
        $result = $this->machineService->purchaseItem($this->machine, $this->user, 0);
        $this->assertEquals($result->itemsRemaining, 4);
        $this->assertEquals($result->itemsPurchased, 1);
        $this->assertEquals($result->returnedCoins, 3);
    }

    public function testItemOutOfStock()
    {
        $this->expectException(ItemOutOfStockException::class);
        $this->machine->items()->first()->update(['count' => 0]);
        $this->machineService->insertCoins($this->machine, $this->user, 5);
        $this->machineService->purchaseItem($this->machine, $this->user, 0);
    }

    public function testItemNotFound()
    {
        $this->expectException(ItemNotFoundException::class);
        $this->machineService->insertCoins($this->machine, $this->user, 5);
        $this->machineService->purchaseItem($this->machine, $this->user, 3);
    }

    public function testInsufficientFunds()
    {
        $this->expectException(InsufficientFundsException::class);
        $this->machineService->insertCoins($this->machine, $this->user, 1);
        $this->machineService->purchaseItem($this->machine, $this->user, 1);
    }


}
