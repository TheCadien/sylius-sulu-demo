import React, {useState} from 'react';
import ViewRenderer from 'sulu-headless-bundle/src/containers/ViewRenderer';
import CartButton from './CartButton';
import CartContext from '../contexts/CartContext';

export default () => {
    const [cart, setCart] = useState();

    return (
        <CartContext.Provider value={[cart, setCart]}>
            <CartButton/>
            <ViewRenderer/>
        </CartContext.Provider>
    );
};
