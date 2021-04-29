import shopRequester from './shopRequester';

const createCart = () => {
    return shopRequester.post('/api/v2/shop/orders', { locale: 'en_US' });
};

const addItemToCart = (cart, code, variantCode, quantity) => {
    return shopRequester.patch('/api/v2/shop/orders/' + cart.tokenValue + '/items', {
        productCode: code,
        productVariantCode: variantCode,
        quantity: quantity,
    });
};

const loadProduct = (code) => {
    shopRequester.get('/api/v2/shop/products/' + code).then((data) => {
        return shopRequester.get(data.variants[0])
    });
};
export {
    createCart,
    addItemToCart,
    loadProduct,
};
