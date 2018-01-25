<?php

require_once 'core.php';

$posts = getPostsDb()->fetchAll(PDO::FETCH_ASSOC);

$j = 0;
$post48 = [];
foreach ($posts as $index => $post) {
    if ($j < 49) {
        $post48[] = $post;
        $j++;
        continue;
    }
    dump('Saving chunk');
    $j = 0;
    $reactions = getReactions($post48);

    saveReaction(cleanReaction($reactions));

    $post48 = [];
}
