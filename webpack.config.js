const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const { VueLoaderPlugin } = require('vue-loader')

/**
 * path.resolve : 주어진 경로들을 기반으로 절대 경로를 생성합니다.
 * 
 * __dirname : Node.js 환경에서 사용되는 전역 변수로, 현재 스크립트 파일의 경로를 얻을 수 있습니다.
 */
module.exports = {
    entry: {
        react_bundle: './src/react_index.js',
        vue_bundle: './src/vue_index.js'
      },
    mode: 'development',
    output: {
        filename: '[name].js',
        path: path.resolve(__dirname, 'dist')
    },
    module: {
        rules: [
            {
                /** 
                 * [정규 표현식]
                 * \. : 문자 . 을 뜻함
                 * (js|jsx) : js 또는 jsx
                 * $ : 문자열의 끝을 표현
                 * 
                 * 결과 : .js 또는 .jsx을 가진 확장자 파일을 로드할땐
                 * babel-loader를 이용해서 가져오자 라는 뜻
                 */
                test: /\.(js|jsx)$/,
                exclude: /node_modules/,
                use: 'babel-loader'
            },
            {
                test: /\.vue$/,
                exclude: /node_modules/,
                loader: 'vue-loader'
            },
            {
                test: /\.css$/,
                exclude: /node_modules/,
                use: [
                    'vue-style-loader',
                    'css-loader'
                ]
            },
            {
                // i의의미: 대소문자 구분 X
                test: /\.(png|jpe?g|gif)$/i,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            // ext: 확장자 약어
                            name: '[contenthash].[ext]',
                        },
                    },
                ],
            },
            {
                test: /\.svg$/,
                use: [
                    {
                        loader: 'svg-inline-loader'
                    },
                ],
            }
        ]
    },
    resolve: {
        /**
         * import 할때 파일명만 넣고 확장자를 입력하지 않을 경우
         * 파일들의 이름이 같을 때가 생길 수 있습니다.
         * 이때 아래와 같은 순서로 확장자를 확인합니다.
         * 
         * ... : 은 webpack에서 사용하는 기본 확장자들도 확인하라는 뜻입니다.
         */
        extensions: ['.js', '.jsx', 'vue', 'css', 'html', '...']
    },
    plugins: [
        new VueLoaderPlugin(),
        /**
         * HTML 파일을 자동으로 생성해주고, 번들링한 결과물을 자동으로 추가해 줍니다.
         */
        new HtmlWebpackPlugin({
            template: path.resolve(__dirname, 'public', 'index.html'),
            filename: './index.html'
        }),
    ]
}