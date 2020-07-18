import React, { useState, useEffect } from 'react';
import {
    fetchMachineInventory, fetchUser, fetchInsertCoin, fetchRefundCoins, fetchPurchaseItem,
    fetchResetAll
} from "../api";

const CoinMachine = () => {
    const [inventoryLoading, setInventoryLoading] = useState(false);
    const [inventory, setInventory] = useState([0,0,0]);
    const fetchMachineInventoryFromAPI = async () => {
        setInventoryLoading(true);
        const inventory = await fetchMachineInventory();
        setInventoryLoading(false);
        setInventory(inventory);
    };

    const [userLoading, setUserLoading] = useState(false);
    const [user, setUser] = useState(0);
    const fetchUserFromAPI = async () => {
        setUserLoading(true);
        const user = await fetchUser();
        setUserLoading(false);
        setUser(user);
    };

    const [insertCoinLoading, setInsertCoinLoading] = useState(false);
    const fetchInsertCoinFromAPI = async () => {
        setInsertCoinLoading(true);
        const coins = await fetchInsertCoin();
        await fetchUserFromAPI();
        setInsertCoinLoading(false);
    };

    const [resetAllLoading, setResetAllLoading] = useState(false);
    const fetchResetAllFromAPI = async () => {
        setResetAllLoading(true);
        await fetchResetAll();
        await fetchUserFromAPI();
        await fetchMachineInventoryFromAPI();
        setResetAllLoading(false);
    };

    const [refundCoinsLoading, setRefundCoinsLoading] = useState(false);
    const fetchRefundCoinsFromAPI = async () => {
        setRefundCoinsLoading(true);
        await fetchRefundCoins();
        await fetchUserFromAPI();
        setRefundCoinsLoading(false);
    };

    const [purchaseItemLoading, setPurchaseItemLoading] = useState(false);
    const [purchaseItemIndex, setPurchaseItemIndex] = useState(0);
    const fetchPurchaseItemFromAPI = async (index) => {
        setPurchaseItemLoading(true);
        setPurchaseItemIndex(index);
        await fetchPurchaseItem(index);
        await fetchMachineInventoryFromAPI();
        await fetchUserFromAPI();
        setPurchaseItemLoading(false);
    };

    useEffect(() => {
        fetchMachineInventoryFromAPI();
        fetchUserFromAPI();
    }, []);


    return (
        <>
            <div className="coin-machine card">
                <div className="coin-machine-header">
                    <div className="coin-machine-title">
                        Vend-O-Matic
                    </div>

                </div>
                <div className="coin-machine-item-list">
                    {inventory && inventory.map((amt, index) => {
                        const hasSufficientFunds = user && (user['machine']['coins'] >= 2);
                        return (
                            <div key={index} className="coin-machine-item card">
                                <div className="coin-machine-item-title">
                                    Item #{index}
                                </div>
                                <div className="coin-machine-item-cost">
                                    Cost: 2 coins
                                </div>
                                <div className="coin-machine-item-amount">
                                    Remaining: {amt}
                                </div>
                                <button
                                    className="btn btn-primary"
                                    disabled={purchaseItemLoading || inventoryLoading || !hasSufficientFunds}
                                    onClick={() => fetchPurchaseItemFromAPI(index)}
                                >{purchaseItemLoading && purchaseItemIndex === index ? 'Buying...' : 'Buy'}</button>
                            </div>
                        )
                    })}
                </div>
                <div className="coin-machine-coins">
                    {user ? (<div>
                        Coins: {user['machine']['coins']}
                    </div>) : ''}
                </div>
            </div>
            <hr/>
            <div className="user-controls">
                {user ? (<div>
                    Your Balance: {user['coins']} coins
                </div>) : ''}
                <div>
                    <button
                        className="btn btn-primary mr-2"
                        disabled={insertCoinLoading}
                        onClick={() => fetchInsertCoinFromAPI()}>{insertCoinLoading ? 'Inserting...' : 'Insert Coin'}</button>

                    <button
                        className="btn btn-secondary"
                        disabled={refundCoinsLoading}
                        onClick={() => fetchRefundCoinsFromAPI()}>{refundCoinsLoading ? 'Refunding...' : 'Refund Coins'}</button>
                </div>
            </div>
            <hr/>
            <div className="user-controls">
                <button
                    className="btn btn-danger"
                    disabled={resetAllLoading}
                    onClick={() => fetchResetAllFromAPI()}>{resetAllLoading ? 'Resetting...' : 'RESET ALL'}</button>

            </div>
        </>

    )
};

export default CoinMachine;