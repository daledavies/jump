const path = require('path');

module.exports = {
    mode: 'production',
    entry: './jumpapp/assets/js/src/index.js',
    output: {
        filename: 'index.bundle.js',
        path: path.resolve(__dirname, './jumpapp/assets/js/'),
    },
};