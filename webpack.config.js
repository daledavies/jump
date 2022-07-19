const path = require('path');
const Terser = require('terser-webpack-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const VersionFilePlugin = require('webpack-version-file');

module.exports = {
    mode: 'production',
    entry: {
        index: './jumpapp/assets/js/src/index.js',
        styles: './jumpapp/assets/css/src/index.scss',
    },
    output: {
        filename: '[name].[contenthash].min.js',
        path: path.resolve(__dirname, './jumpapp/assets/js/'),
    },
    module: {
        rules: [
            {
                test: /\.(s(a|c)ss)$/,
                use: [
                    {
                        loader: MiniCssExtractPlugin.loader
                    },
                    {
                        loader: 'css-loader',
                        options: {
                            url: false // Stop webpack emitting image/font from URLs found in CSS.
                        }
                    },
                    'sass-loader',
                ],
            },
            {
                test: /\.jump-version/,
                type: 'asset/source',
            }
        ]
    },
    plugins: [
        new HtmlWebpackPlugin({
            filename: path.resolve(__dirname, './jumpapp/templates/partials/cssbundle.mustache'),
            template: path.resolve(__dirname, './jumpapp/templates/partials/src/cssbundle.src.mustache'),
            inject: false,
            minify: false, // Required to prevent addition of closing tags like body and html.
        }),
        new HtmlWebpackPlugin({
            filename: path.resolve(__dirname, './jumpapp/templates/partials/jsbundle.mustache'),
            template: path.resolve(__dirname, './jumpapp/templates/partials/src/jsbundle.src.mustache'),
            inject: false,
            minify: false,
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
        }),
        new VersionFilePlugin({
            data: {
                date: Math.floor(Date.now() / 1000),
            },
            output: './jumpapp/.jump-version',
            templateString: 'v<%= version %> (<%= date %>)',
        }),
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
