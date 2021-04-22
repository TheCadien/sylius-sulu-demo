import React, {useContext} from 'react';
import CartContext from '../contexts/CartContext';

export default ({
    image,
    name,
    description,
    price,
}) => {
    const [cart, setCart] = useContext(CartContext);

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
                        onClick={() => null}
                    >
                        Put into cart
                    </button>
                </div>
            </div>
        </div>
    );
};
