import React, {useState, useEffect} from 'react';
import ViewRenderer from 'sulu-headless-bundle/src/containers/ViewRenderer';
import CartButton from './CartButton';
import CartContext from '../contexts/CartContext';
import {createCart} from '../services/cart';

export default () => {
    const [cart, setCart] = useState();
    useEffect(() => {
        createCart().then(setCart);
    }, []);

    return (
        <CartContext.Provider value={[cart, setCart]}>
            <CartButton/>
            <ViewRenderer/>
        </CartContext.Provider>
    );
};
