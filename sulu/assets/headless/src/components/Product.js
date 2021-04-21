import React from 'react';

class Product extends React.Component {
    render() {
        const {
            image,
            name,
            description,
            price,
        } = this.props;

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
                    </div>
                </div>
            </div>
        );
    }
}

export default Product;
