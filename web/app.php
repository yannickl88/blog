<?php
umask(0000);
require __DIR__ . '/../vendor/autoload.php';

// Init
$request_url = $_SERVER['REQUEST_URI'];
$blogger     = \Yannickl88\Blog\Blogger::load(__DIR__ . '/../var/cache/blogs.json');
$parser      = new Parsedown();

// Twig
$loader = new Twig_Loader_Filesystem(__DIR__ . '/../assets');
$twig   = new Twig_Environment($loader, array(
    'cache'       => __DIR__ . '/../var/cache',
    'auto_reload' => true,
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
} elseif (1 === preg_match('~^/rss$~', $request_url, $matches)) {
    header('Content-type: text/xml');

    echo $twig->loadTemplate('rss.xml.twig')->render([
        'blogs' => $blogger->getBlogs(),
        'host'  => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']
    ]);
} elseif (1 === preg_match('~^/deploy$~', $request_url, $matches)) {
    if (!isset($_SERVER['GITHUB_SECRET']) || empty($secret = $_SERVER['GITHUB_SECRET'])) {
        header('HTTP/1.0 500 Internal Server Error');

        echo "Secret not configured, please add 'GITHUB_SECRET' to your environment variables.";
        exit;
    }
    $event = json_decode(file_get_contents('php://input'), true);

    if (!is_array($event) || !isset(
        $event['repository']['full_name'],
        $event['repository']['git_url'],
        $event['repository']['master_branch']
    )) {
        header('HTTP/1.0 500 Internal Server Error');

        echo 'Missing event data.';
        exit;
    }

    $manager = new \Yannickl88\Blog\RepositoryManager(__DIR__ . '/../blogs', __DIR__ . '/../var/cache');
    $manager->update(new \Yannickl88\Blog\Repository(
        $event['repository']['full_name'],
        $event['repository']['git_url'],
        'origin/' . $event['repository']['master_branch']
    ));
} else {
    header('HTTP/1.0 404 Not Found');

    echo $twig->loadTemplate('404.html.twig')->render([]);
}
