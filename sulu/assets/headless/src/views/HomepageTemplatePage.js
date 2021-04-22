import React from 'react';
import Carousel from 'react-bootstrap/Carousel';
import Product from '../components/Product';

export default ({
    data: {
        content: {
            headerImages,
            title,
            text,
            products,
        },
    },
}) => (
    <>
        <Carousel className="slide my-4">
            {headerImages.map(({id, title, formatUri}) => (
                <Carousel.Item key={id}>
                    <img
                        className="d-block w-100"
                        src={formatUri.replace('{format}', '1280x720')}
                        alt={title}
                    />
                    <Carousel.Caption>
                        <h3>{title}</h3>
                    </Carousel.Caption>
                </Carousel.Item>
            ))}
        </Carousel>

        <div className="row">
            <div className="col-lg-12 mb-4">
                <h1>{title}</h1>

                <div dangerouslySetInnerHTML={{__html: text}}/>
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
