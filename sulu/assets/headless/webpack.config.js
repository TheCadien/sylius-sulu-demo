const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
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
        plugins: [new MiniCssExtractPlugin()],
        module: {
            rules: [
                {
                    test: /\.css$/i,
                    use: [MiniCssExtractPlugin.loader, 'css-loader'],
                },
            ],
        },
    };
};

