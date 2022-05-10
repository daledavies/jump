const path = require('path');
const Terser = require('terser-webpack-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

module.exports = {
    mode: 'production',
    entry: {
        index: './jumpapp/assets/js/src/index.js',
        styles: './jumpapp/assets/css/src/styles.css',
    },
    output: {
        filename: '[name].[contenthash].min.js',
        path: path.resolve(__dirname, './jumpapp/assets/js/'),
    },
    module: {
        rules: [
            {
                test: /\.css$/,
                use: [
                    {
                        loader: MiniCssExtractPlugin.loader
                    },
                    {
                        loader: "css-loader",
                        options: {
                            url: false // Stop webpack emitting image/font from URLs found in CSS.
                        }
                    }
                ],
            }
        ]
    },
    plugins: [
        new HtmlWebpackPlugin({
            filename: path.resolve(__dirname, './jumpapp/templates/header.mustache'),
            template: path.resolve(__dirname, './jumpapp/templates/src/header.mustache'),
            inject: false,
            minify: false, // Required to prevent addition of closing tags like body and html.
        }),
        new HtmlWebpackPlugin({
            filename: path.resolve(__dirname, './jumpapp/templates/footer.mustache'),
            template: path.resolve(__dirname, './jumpapp/templates/src/footer.mustache'),
            inject: false,
            minify: false, // Required to prevent addition of closing tags like body and html.
        }),
        new MiniCssExtractPlugin({filename: '../css/[name].[contenthash].min.css'}),
        new RemoveEmptyScriptsPlugin(),
        new CleanWebpackPlugin({
            dry: false,
            verbose: true,
            cleanStaleWebpackAssets: true,
            cleanOnceBeforeBuildPatterns: [
                'index.*.min.js',
                path.resolve(__dirname, './jumpapp/assets/css/styles.*.min.css')
            ],
            dangerouslyAllowCleanPatternsOutsideProject: true,
        })
    ],
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
            new CssMinimizerPlugin(),
        ],
    },
};
