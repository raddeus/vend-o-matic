import React from 'react';

const CoinMachine = () => {
    return (
        <>
            <div className="coin-machine">
                <div className="coin-machine-header">
                    <div className="coin-machine-title">
                        Vend-O-Matic
                    </div>

                </div>
                <div className="coin-machine-item-list">
                    {[5,5,5].map((amt, index) => (
                        <div key={index} className="coin-machine-item">
                            <div className="coin-machine-item-title">
                                Item #{index}
                            </div>
                            <div className="coin-machine-item-cost">
                                Cost: 2 coins
                            </div>
                            <div className="coin-machine-item-amount">
                                Remaining: {amt}
                            </div>
                            <button>Buy</button>
                        </div>
                    ))}
                </div>
                <div className="coin-machine-coins">
                    Coins: 3
                </div>
            </div>
            <hr/>
            <div className="user-controls">
                <div>
                    Your Balance: 5 coins
                </div>
                <div>
                    <button>Insert Coin</button>
                    <button>Refund Coins</button>
                </div>
            </div>
        </>

    )
};

export default CoinMachine;