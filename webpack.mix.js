const mix = require('laravel-mix');

mix.browserSync('dev.litlife.club');

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

mix.options({
    processCssUrls: true // disable file loader for url()
});

mix.setPublicPath('public/assets')
    .setResourceRoot('/assets')
    .js('resources/js/app.js', 'js')
    .js('resources/js/books_list.js', 'js')
    .js('resources/js/authors_list.js', 'js')
    .js('resources/js/authors.show.js', 'js')
    .js('resources/js/authors.edit.js', 'js')
    .js('resources/js/blog.js', 'js')
    .js('resources/js/sequences.search.js', 'js')
    .js('resources/js/users_list.js', 'js')
    .js('resources/js/comments_list.js', 'js')
    .js('resources/js/managers.on_check.js', 'js')
    .js('resources/js/books.show.js', 'js')
    .js('resources/js/book-keyword.js', 'js')
    .js('resources/js/sequences.show.js', 'js')
    .js('resources/js/topics.show.js', 'js')
    .js('resources/js/users.show.js', 'js')
    .js('resources/js/forums.js', 'js')
    .js('resources/js/books.edit.js', 'js')
    .js('resources/js/books.sections.index.js', 'js')
    .js('resources/js/books.notes.index.js', 'js')
    .js('resources/js/latest-posts.js', 'js')
    .js('resources/js/latest-comments.js', 'js')
    .js('resources/js/settings.js', 'js')
    .js('resources/js/topic.index.js', 'js')
    .js('resources/js/message.index.js', 'js')
    .js('resources/js/post.search.js', 'js')
    .js('resources/js/posts.on_check.js', 'js')
    .js('resources/js/topics.search.js', 'js')
    .js('resources/js/bookmarks.js', 'js')
    .js('resources/js/books.sections.show.js', 'js')
    .js('resources/js/users.achievements.js', 'js')
    .js('resources/js/blogs.index.js', 'js')
    .js('resources/js/books.attachments.index.js', 'js')
    .js('resources/js/group.index.js', 'js')
    .js('resources/js/jquery.wysibb.js', 'js')
    .js('resources/js/keywords.index.js', 'js')
    .js('resources/js/users.notes.index.js', 'js')
    .js('resources/js/awards.js', 'js')
    .js('resources/js/books.awards.index.js', 'js')
    .js('resources/js/users.managers.index.js', 'js')
    .js('resources/js/comments.on_check.js', 'js')
    .js('resources/js/book_files.on_moderation.js', 'js')
    .js('resources/js/books.old.page.js', 'js')
    .js('resources/js/collection/collections.index.js', 'js')
    .js('resources/js/collection/collections.comments.js', 'js')
    .js('resources/js/collection/collections.books.js', 'js')
    .js('resources/js/collection/collections.show.js', 'js')
    .js('resources/js/example.scroll_to_bottom.js', 'js')
    .js('resources/js/preview.comment.js', 'js')
    .js('resources/js/complains.index.js', 'js')
	.js('resources/js/support_requests.index.js', 'js')
    .js('resources/js/users.create.js', 'js')
    .js('resources/js/users.refer.js', 'js')
	.js('resources/js/users.settings.read_style.js', 'js')
	.js('resources/js/ideas.index.js', 'js')
    .js('resources/js/survey.create.js', 'js')
	.js('resources/js/faq.js', 'js')

    .sass('resources/sass/app.scss', 'css')
    .sass('resources/sass/sections-list.scss', 'css')
    .sass('resources/sass/notes-list.scss', 'css')
    .sass('resources/sass/sceditor_content.scss', 'css')
    .sass('resources/sass/bootstrap.scss', 'css')
    .sass('resources/sass/styles_for_epub_books.scss', 'css')
	.sass('resources/sass/bootstrap-colorpicker.scss', 'css')
	.sass('resources/sass/faq.scss', 'css')
	.copy('resources/images/no_image_male.png', 'public/assets/images/no_image_male.png')
	.copy('resources/images/no_image_female.png', 'public/assets/images/no_image_female.png')
	.copy('resources/images/no_image_unknown.png', 'public/assets/images/no_image_unknown.png')
	.copy('resources/images/no_book_cover.jpeg', 'public/assets/images/no_book_cover.jpeg')

    //.sass('resources/sass/wysibb/wbbtheme.scss', 'css')
    //.less('node_modules/sceditor/src/themes/default.less', 'sceditor/src/themes/default.css')
    .version();

if (mix.inProduction()) {

} else {
    mix.sourceMaps();
}

if (mix.inProduction()) {
    mix.options({
        terser: {
            terserOptions: {
                compress: {
                    drop_console: true
                }
            }
        }
    });
}

mix.webpackConfig({
    externals: {
        "jquery": "jQuery"
    },
    resolve: {
        extensions: ['.js', '.json', '.less'],
        modules: [
            path.resolve('./resources/js/components'),
            path.resolve('./node_modules')
        ]
    },
    // включаем поддержку трансформации arrow functions в старый формат для пакетов импортируемых из node_modules
    module: {
        rules: [
            {
                test: /\.jsx?$/,
                exclude: /(bower_components)/,
                use: [
                    {
                        loader: 'babel-loader',
                        options: Config.babel()
                    }
                ]
            }
        ]
    }
    /*
    resolve: {
        modules: [
            'node_modules',
            'webpack2.config.js'
        ]
    }
    */
});


