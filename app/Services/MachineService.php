<?php

namespace App\Services;

use App\Data\MachinePurchaseResult;
use App\Exceptions\InsufficientFundsException;
use App\Exceptions\ItemNotFoundException;
use App\Exceptions\ItemOutOfStockException;
use App\Machine;
use App\User;
use Illuminate\Support\Facades\DB;

class MachineService
{
    /**
     * Allow a user to insert coins into a machine
     *
     * @param Machine $machine
     * @param User $user
     * @param int $amount The requested amount of coins to deposit
     * @return int The number of coins accepted
     * @throws \Exception
     */
    public function insertCoins(Machine $machine, User $user, int $amount): int
    {
        // If we get a non-positive amount. Don't deposit anything.
        if ($amount < 1) {
            // @TODO - should we throw exception here?
            return 0;
        }

        // If user attempt to deposit more coins than they own, deposit all of their coins
        if ($amount > $user->coins) {
            // @TODO - should we throw exception here?
            $amount = $user->coins;
        }

        $machine->coins += $amount;
        $user->coins -= $amount;
        try {
            DB::beginTransaction();
            $machine->save();
            $user->save();
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }

        return $amount;
    }

    /**
     * @param Machine $machine
     * @param User $user
     * @return int The number of coins refunded
     * @throws \Exception
     */
    public function refundCoins(Machine $machine, User $user): int
    {
        $refundAmount = $machine->coins;
        $user->coins += $refundAmount;
        $machine->coins = 0;
        try {
            DB::beginTransaction();
            $machine->save();
            $user->save();
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
        return $refundAmount;
    }

    /**
     * @param Machine $machine
     * @param User $user
     * @param int $itemIndex
     * @return MachinePurchaseResult
     * @throws InsufficientFundsException
     * @throws ItemNotFoundException
     * @throws ItemOutOfStockException
     * @throws \Exception
     */
    public function purchaseItem(Machine $machine, User $user, int $itemIndex)
    {
        $machineItems = $machine->items;
        if (!array_key_exists($itemIndex, $machineItems->toArray())) {
            throw new ItemNotFoundException();
        }

        $item = $machineItems->get($itemIndex);
        if ($item->count < 1) {
            throw new ItemOutOfStockException();
        }

        // This should probably be a configurable "cost" property on the item itself.
        $cost = 2;
        if ($machine->coins < $cost) {
            throw new InsufficientFundsException();
        }

        $returnedCoins = $machine->coins - $cost;
        $item->count -= 1;
        $user->coins += $returnedCoins;
        $machine->coins = 0;
        try {
            DB::beginTransaction();
            $item->save();
            $machine->save();
            $user->save();
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }

        $response = new MachinePurchaseResult();
        $response->itemsPurchased = 1;
        $response->itemsRemaining = $item->count;
        $response->returnedCoins = $returnedCoins;
        return $response;
    }

}