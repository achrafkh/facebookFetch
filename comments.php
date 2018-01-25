<?php
require_once 'core.php';
$posts_ids = $posts = getPostsDb('id')->fetchAll(PDO::FETCH_ASSOC);

foreach ($posts_ids as $count => $post_id) {
    dump('Fetch Comments for : ' . $post_id['id'] . ' | N : ' . $count);
    fetchComments($post_id['id']);
}

function fetchComments($post_id)
{
    $ids = [];
    $break = false;
    $i = 0;

    while (true) {
        if (0 == $i) {
            $res = getComments($post_id, null);
        } else {
            if (!isset($res->paging->next)) {
                $break = true;
            } else {
                $res = getComments($post_id, $res->paging->next);
            }
        }
        $i++;
        foreach ($res->data as $comment) {
            $comments = cleanComment($comment);
            saveComments($comments, $post_id);
        }

        if ($break) {
            break;
        }
    }
    return $ids;
}
