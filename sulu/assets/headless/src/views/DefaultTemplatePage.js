import React from 'react';

export default ({
    data: {
        content: {
            title,
            content,
        },
    },
}) => (
    <>
        <h1>{title}</h1>

        <div dangerouslySetInnerHTML={{__html: content}}/>
    </>
);
