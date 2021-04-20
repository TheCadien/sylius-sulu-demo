const path = require('path');
const nodeModulesPath = path.resolve(__dirname, 'node_modules');

/* eslint-disable-next-line no-unused-vars */
module.exports = (env, argv) => {
    return {
        resolve: {
            modules: [nodeModulesPath, 'node_modules'],
        },
        resolveLoader: {
            modules: [nodeModulesPath, 'node_modules'],
        },
    };
};

