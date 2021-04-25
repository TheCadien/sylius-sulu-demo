import React, {useContext, useState} from 'react';
import Modal from 'react-bootstrap/Modal';
import CartContext from '../contexts/CartContext';

export default () => {
    const [cart, setCart] = useContext(CartContext)
    const [cartOpen, setCartOpen] = useState(false);

    let itemsQuantity = 0;
    if (cart) {
        itemsQuantity = cart.items.reduce((sum, item) => {
            return sum + item.quantity;
        }, 0);
    }

    return (
        <>
            <button className="btn text-dark btn-link p-0" onClick={() => setCartOpen(true)}>
                Cart with {itemsQuantity} Items
            </button>

            {cart &&
                <Modal
                    show={cartOpen}
                    onHide={() => setCartOpen(false)}
                    backdrop="static"
                >
                    <Modal.Header closeButton>
                        <Modal.Title>Cart</Modal.Title>
                    </Modal.Header>

                    <Modal.Body>
                        <table className="table">
                            {cart.items.map((item, i) => (
                                <tr>
                                    <td>{i + 1}</td>
                                    <td>{item.productName}</td>
                                    <td>{item.quantity}</td>
                                    <td>$ {item.total / 100}</td>
                                </tr>
                            ))}
                            <tr>
                                <td colSpan="2"/>
                                <td>{itemsQuantity}</td>
                                <td>$ {cart.total / 100}</td>
                            </tr>
                        </table>
                    </Modal.Body>
                </Modal>
            }
        </>
    );
}
