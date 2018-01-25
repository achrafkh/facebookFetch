<?php
require_once 'core.php';

$page_ids = [];
if (defined('STDIN')) {
    unset($argv[0]);

    if (isset($argv[1]) && 'file' == $argv[1]) {
        $page_ids = file('files/' . $argv[2], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $since = $argv[3];
        $until = $argv[4];
    } else {
        $countA = count($argv);
        $until = $argv[($countA)];

        $since = $argv[($countA - 1)];
        unset($argv[($countA)]);
        unset($argv[($countA - 1)]);
        $page_ids = $argv;
    }
} else {
    if (isset($_GET['page_ids'])) {
        $page_ids = $_GET['page_ids'];
    }
}
$since = Carbon\Carbon::parse($since)->timestamp;
$until = Carbon\Carbon::parse($until)->timestamp;

dump('Total page ids to fetch : ' . count($page_ids));
foreach ($page_ids as $count => $page_id) {
    dump('Fetching page num : ' . $count . ' | ID : ' . $page_id);
    fetchPosts($page_id);
}

function fetchPosts($page_id)
{
    $ids = [];

    $i = 0;
    while (true) {
        if (0 == $i) {
            $res = getPosts(null, $page_id);
        } else {
            $res = getPosts($res->paging->next, $page_id);
        }
        $i++;
        if (count($res->data) == 0) {
            break;
        }
        // to improve
        foreach ($res->data as $post) {
            savePosts($post);
        }
    }
    return $ids;
}
