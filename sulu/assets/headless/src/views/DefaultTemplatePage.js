import React from 'react';
import { observer } from 'mobx-react';

@observer
class DefaultTemplatePage extends React.Component {
    render() {
        const {
            content: {
                title,
                content,
            },
        } = this.props.data;

        return (
            <>
                <h1>{title}</h1>

                <div dangerouslySetInnerHTML={{__html: content}}/>
            </>
        );
    }
}

export default DefaultTemplatePage;
