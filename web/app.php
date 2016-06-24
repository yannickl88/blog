<?php
umask(0000);
require __DIR__ . '/../vendor/autoload.php';

// Init
$request_url = $_SERVER['REQUEST_URI'];
$blogger     = \Yannickl88\Blog\Blogger::load(__DIR__ . '/../blogs.yml');
$parser      = new Parsedown();

// Twig
$loader = new Twig_Loader_Filesystem(__DIR__ . '/../assets');
$twig   = new Twig_Environment($loader, array(
    'cache'       => __DIR__ . '/../cache',
    'auto_reload' => false,
));

// Add Markdown Filter
$twig->addFilter(new Twig_SimpleFilter('markdown', function ($string) use ($parser) {
    return $parser->text($string);
}, ['is_safe' => ['html']]));

// Routing
$matches = [];
if (1 === preg_match('~^/post/(.+)$~', $request_url, $matches) && $blogger->hasBlog($matches[1])) {
    $blog = $blogger->getBlog($matches[1]);

    echo $twig->loadTemplate('post.html.twig')->render([
        'blog'    => $blog,
        'related' => $blogger->getBlogsForAuthor($blog->getAuthor())
    ]);
} elseif (1 === preg_match('~^/$~', $request_url, $matches)) {
    echo $twig->loadTemplate('index.html.twig')->render([
        'blogs' => $blogger->getBlogs()
    ]);
} else {
    header('HTTP/1.0 404 Not Found');

    echo $twig->loadTemplate('404.html.twig')->render([]);
}
