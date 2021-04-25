import React from 'react';
import Product from '../components/Product';
import styled from "styled-components";

const Title = styled.h1`
  font-family: "Teko", sans-serif;
  font-weight: bold;
  font-size: 3em;
  margin-bottom: 0 !important;
  color: tomato;
  text-transform: uppercase;
`;

const Author = styled.h5` 
  font-family: "Teko", sans-serif;
  font-weight: lighter;
  font-size: 1.5em;
  margin-bottom: 2em;
`;

const ProductImage = styled.img`
  max-width: 100%;
  border-radius: 4px;
  box-shadow: 2px 2px 4px rgba(0,0,0,0.4);
`;

const ProductWrapper = styled.div`
  margin: 2rem 0;
  
  div {
    flex-basis: 50%;
  }
`;

export default ({
                    data: {
                        content: {
                            title,
                            text,
                            author,
                            publishedDate,
                            image: {
                                formatUri = '',
                            },
                            products,
                        },
                    },
                }) => (
    <>
        <ProductWrapper className="mb-5 d-flex flex-column-reverse flex-lg-row">
            <div className="d-flex flex-column pr-3">
                <Title className="mb-3">{title}</Title>
                <Author>{author && <span>{author}</span>} {publishedDate && <span>{publishedDate}</span>}</Author>
                <p>{text}</p>
                <button className="btn btn-outline-dark mr-auto mt-auto">Put Into Cart</button>
            </div>
            <div className="mb-lg-0 mb-3">
                <ProductImage src={formatUri.replace('{format}', '1280x720')} alt=""/>
            </div>
        </ProductWrapper>

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
