const path = require('path');
const Terser = require('terser-webpack-plugin');

module.exports = {
    mode: 'production',
    entry: './jumpapp/assets/js/src/index.js',
    output: {
        filename: 'index.bundle.js',
        path: path.resolve(__dirname, './jumpapp/assets/js/'),
    },
    optimization: {
        minimizer: [
            new Terser({
                terserOptions: {
                    format: {
                        comments: false,
                    },
                },
                extractComments: false,
            }),
        ],
    },
};
