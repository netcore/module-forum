let mix = require('laravel-mix');
let path = require('path');

const config = {
    dev: process.env.NODE_ENV === 'development',
    src: __dirname + '/node_modules/',
    res: __dirname + '/Resources/assets/',
    out: __dirname + '/Assets/'
};

// Configure mix.
mix.js(path.join(config.res, 'js/index.js'), path.join(config.out, 'admin/js/index.js'));
mix.js(path.join(config.res, 'js/thread-edit.js'), path.join(config.out, 'admin/js/thread-edit.js'));
mix.sass(path.join(config.res, 'sass/main.scss'), path.join(config.out, 'admin/css/main.css'));

mix.disableNotifications();

