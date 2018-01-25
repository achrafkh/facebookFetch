<?php

function getPosts($url = null, $page_id)
{
    $client = new GuzzleHttp\Client();
    $since = 1483228800;
    $until = 1513695698;

    if (null == $url) {
        $url = 'https://graph.facebook.com/v2.6/' . $page_id . '/posts?fields=from,id,message,message_tags,full_picture,picture,shares,created_time,story,story_tags,actions,type,status_type,updated_time,is_hidden,is_instagram_eligible,is_popular,is_expired,attachments.limit(100){media,title,type,url,description}&limit=50&since=' . $since . '&until=' . $until . '&access_token=' . TOKEN;
    }
    try {
        $res = $client->request('GET', $url);
    } catch (\Exception $e) {
        dump($e->getResponse()->getBody()->getContents());
        dump('Sleeping for 15 min');
        sleep(900);
        dump('Sleep ended, trying again');
        return getPosts($url, $page_id);
    }
    return json_decode($res->getBody()->getContents());
}

function getReactions($posts)
{
    $ids = [];
    foreach ($posts as $post) {
        $ids[] = $post['id'];
    }
    $client = new GuzzleHttp\Client();
    $url = 'https://graph.facebook.com/v2.10/?ids=' . implode(',', $ids) . '&fields=';
    $url .= 'reactions.type(LIKE).limit(0).summary(total_count).as(LIKE),';
    $url .= 'reactions.type(HAHA).limit(0).summary(total_count).as(HAHA),';
    $url .= 'reactions.type(ANGRY).limit(0).summary(total_count).as(ANGRY),';
    $url .= 'reactions.type(SAD).limit(0).summary(total_count).as(SAD),';
    $url .= 'reactions.type(WOW).limit(0).summary(total_count).as(WOW),';
    $url .= 'reactions.type(LOVE).limit(0).summary(total_count).as(LOVE)';
    $url .= '&access_token=' . TOKEN;

    try {
        $res = $client->request('GET', $url);
    } catch (\Exception $e) {
        dump('Sleeping for 15 min');
        sleep(900);
        dump('Sleep ended, trying again');
    }
    return json_decode($res->getBody()->getContents());
}

function getComments($post_id, $url = null)
{
    $client = new GuzzleHttp\Client();

    if (null == $url) {
        $url = 'https://graph.facebook.com/v2.6/' . $post_id . '/comments?fields=like_count,message,id,created_time,comments.limit(15000){id,message,created_time,like_count}&limit=500&access_token=' . TOKEN;
    }

    try {
        $res = $client->request('GET', $url);
    } catch (\Exception $e) {
        dump($e->getMessage());
        return getComments($post_id, $url);
    }
    return json_decode($res->getBody()->getContents());
}
