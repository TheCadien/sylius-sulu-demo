import shopRequester from './shopRequester';

const createCart = () => {
    return shopRequester.post('/api/v2/shop/orders', { locale: 'en_US' });
};

const addItemToCart = (token, code, variantCode, quantity) => {
    return shopRequester.patch('/api/v2/shop/orders/' + token + '/items', {
        productCode: code,
        productVariantCode: variantCode,
        quantity: quantity,
    });
};

export {
    createCart,
    addItemToCart,
};
