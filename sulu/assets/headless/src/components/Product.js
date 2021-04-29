import React, {useContext, useEffect, useState} from 'react';
import CartContext from '../contexts/CartContext';
import {addItemToCart, loadProduct} from '../services/cart';

export default ({
    code,
    image,
    name,
    description,
    price,
}) => {
    const [cart, setCart] = useContext(CartContext);

    const [product, setProduct] = useState();
    const [loading, setLoading] = useState(true);
    useEffect(() => {
        loadProduct(code)
            .then(setProduct)
            .then(() => setLoading(false));
    }, []);

    return (
        <div className="col-lg-4 col-md-6 mb-4">
            <div className="card h-100">
                <img className="card-img-top" src={image} alt=""/>
                <div className="card-body">
                    <h4 className="card-title">
                        {name}
                    </h4>
                    <h5>$ {price}</h5>
                    <p className="card-text">
                        {description}
                    </p>

                    <button
                        disabled={!cart || loading}
                        className="btn btn-primary"
                        onClick={() => addItemToCart(cart, product.code, product.firstVariant.code, 1).then(setCart)}
                    >
                        Put into cart
                    </button>
                </div>
            </div>
        </div>
    );
};
