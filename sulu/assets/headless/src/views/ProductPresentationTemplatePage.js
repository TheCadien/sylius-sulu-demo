import React from 'react';
import Product from '../components/Product';

export default ({
    data: {
        content: {
            title,
            text,
            image: {
                formatUri = '',
            },
            products,
        },
    },
}) => (
    <>
        <div
            className="p-5 text-center bg-image mb-4"
            style={{
                backgroundImage: 'url(' + formatUri.replace('{format}', '1280x720') + ')',
            }}
        >
            <div className="mask" style={{backgroundColor: 'rgba(0, 0, 0, 0.6)'}}>
                <div className="d-flex justify-content-center align-items-center h-100">
                    <div className="text-white">
                        <h1 className="mb-3">{title}</h1>
                        <div className="mb-3" dangerouslySetInnerHTML={{__html: text}}/>
                    </div>
                </div>
            </div>
        </div>

        <div className="row">
            {products.map((product, i) => (
                <Product
                    key={i}
                    image="https://picsum.photos/1280/720"
                    name={product.name}
                    price={product.price}
                    code={product.code}
                />
            ))}
        </div>
    </>
);
