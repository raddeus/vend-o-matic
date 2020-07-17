<?php

namespace App\Http\Controllers\API;

use App\Exceptions\InsufficientFundsException;
use App\Exceptions\ItemOutOfStockException;
use App\Machine;
use App\Services\MachineService;
use App\User;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MachineController extends Controller
{

    private $machineService;

    public function __construct(MachineService $machineService)
    {
        $this->machineService = $machineService;
    }

    /**
     * Put coins into the machine
     *
     * @param Request $request
     * @param User $user
     * @param Machine $machine
     * @return \Illuminate\Http\Response
     */
    public function insertCoins(Request $request, User $user, Machine $machine)
    {
        $requestedAmount = (int)$request->get('coin');
        try {
            $acceptedAmount = $this->machineService->insertCoins($machine, $user, $requestedAmount);
            return response()->noContent(204, [
                'X-Coins' => $acceptedAmount,
            ]);
        } catch (\Exception $ex) {
            // @TODO - log db exception
            return response()->noContent(500);
        }
    }

    /**
     * Refund all of the user's coins to their inventory
     *
     * @param User $user
     * @param Machine $machine
     * @return \Illuminate\Http\Response
     */
    public function refundCoins(User $user, Machine $machine)
    {
        try {
            $amountRefunded = $this->machineService->refundCoins($machine, $user);
            return response()->noContent(204, [
                'X-Coins' => $amountRefunded,
            ]);
        } catch (\Exception $ex) {
            // @TODO - log db exception
            return response()->noContent(500);
        }
    }

    /**
     * Get the entire contents of the machine's inventory
     *
     * @param Machine $machine
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInventory(Machine $machine)
    {
        try {
            return response()->json($machine->inventory);
        } catch (\Exception $ex) {
            // @TODO - log db exception
            return response()->noContent(500);
        }
    }

    /**
     * Attempt to purchase an item from the machine using inserted coins
     *
     * @param Machine $machine
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws Exception
     */
    public function purchaseItem(Machine $machine, User $user, int $id)
    {
        try {
            $response = $this->machineService->purchaseItem($machine, $user, $id);
        } catch (ItemOutOfStockException $ex) {
            return response()->noContent(404, [
                'X-Coins' => 0,
            ]);
        } catch (InsufficientFundsException $ex) {
            return response()->noContent(403, [
                'X-Coins' => 0,
            ]);
        }

        return response()->json([
            'quantity' => $response->itemsPurchased,
        ], 200, [
            'X-Coins' => $response->returnedCoins,
            'X-Inventory-Remaining' => $response->itemsRemaining,
        ]);
    }

}
