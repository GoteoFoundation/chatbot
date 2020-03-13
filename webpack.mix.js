const mix = require('laravel-mix');

// Backoffice
mix.js('resources/assets/backoffice/js/app.js', 'public/js')
    .js('resources/assets/backoffice/js/routes/languages_index.js', 'public/js')
    .js('resources/assets/backoffice/js/routes/languages_create-edit.js', 'public/js')
    .js('resources/assets/backoffice/js/routes/questions_create-edit.js', 'public/js')
    .js('resources/assets/backoffice/js/routes/questions_list.js', 'public/js')
    .js('resources/assets/backoffice/js/routes/questions_tree.js', 'public/js')
    .js('resources/assets/backoffice/js/routes/topics_index.js', 'public/js')
    .sass('resources/assets/backoffice/sass/app.scss', 'public/css')
    .copy('resources/assets/backoffice/images/', 'public/images/', false)
    .version();

// Frontend widget
mix.js('resources/assets/widget/js/widget.js', 'public/widget')
    .js('resources/assets/widget/js/widget-jquery.js', 'public/widget')
    .sass('resources/assets/widget/sass/widget.scss', 'public/widget')
    .copy('resources/assets/widget/images/', 'public/widget/images/', false);
