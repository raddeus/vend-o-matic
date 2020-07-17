<?php

namespace App\Data;

class MachinePurchaseResult
{

    /**
     * @var int The amount of items purchased
     */
    public $itemsPurchased;

    /**
     * @var int The amount of coins returned
     */
    public $returnedCoins;

    /**
     * @var int The amount of items remaining after purchase
     */
    public $itemsRemaining;

}