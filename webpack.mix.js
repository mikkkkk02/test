const { mix } = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.webpackConfig({
	output: {
		// @FIX: [name] not getting the filename
		chunkFilename: './js/build/chunks/[name]?v=[hash].js'
	}
});

mix.js('public/adminlte/dist/js/app.js', 'public/adminlte/dist/js/app.min.js');
mix.js(['public/js/script.js'], 'public/js/build/script.js').version();

mix.js('resources/assets/js/app.js', 'public/js')
   .extract(['vue', 'moment', 'jquery']);

mix.styles(['public/css/helper.css', 'public/css/general.css', 'public/css/styles.css', 'public/css/custom.css'], 'public/css/build/app.css').version();

if(mix.config.inProduction) {
	/* Merge & minify all css files into one */
	mix.styles(['public/css/helper.css', 'public/css/general.css', 'public/css/styles.css', 'public/css/custom.css'], 'public/css/build/styles.min.css').version(['public/css/build/styles.min.css']);

	/* Minify js files */
	mix.js('public/js/script.js', 'public/js/build/script.min.js').version(['public/js/build/script.min.js']);   
}