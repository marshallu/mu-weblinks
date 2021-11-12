const mix = require('laravel-mix');

mix.setPublicPath('./');

mix.postCss('./source/css/mu-weblinks.css', 'css/mu-weblinks.css', [
    require('postcss-import'),
    require('postcss-nesting'),
    require('tailwindcss'),
		require('autoprefixer')
  ]
);

if (mix.inProduction()) {
    mix.version();
}
