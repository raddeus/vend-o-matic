export const fetchMachineInventory = async () => {
    const response = await fetch('api/inventory', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    });
    return response.json();
};

export const fetchUser = async () => {
    const response = await fetch('api/user', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    });
    const data = await response.json();
    console.log(data);
    return data;
};

export const fetchInsertCoin = async () => {
    const response = await fetch('api', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            coin: 1,
        }),
    });

    return response;
};

export const fetchRefundCoins = async () => {
    const response = await fetch('api', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
        },
    });

    return response;
};

export const fetchPurchaseItem = async (index) => {
    const response = await fetch('api/inventory/' + index, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
    });

    return response;
};

export const fetchResetAll = async (index) => {
    const response = await fetch('api/reset', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
        },
    });

    return response;
};