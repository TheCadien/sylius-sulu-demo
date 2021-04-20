import React from 'react';
import { observer } from 'mobx-react';

@observer
class HomepageTemplatePage extends React.Component {
    render() {
        const {
            content: {
                title,
                article,
            },
        } = this.props.data;

        return (
            <>
                <h1>{title}</h1>

                <p>{article}</p>
            </>
        );
    }
}

export default HomepageTemplatePage;
