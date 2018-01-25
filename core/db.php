<?php
require 'PDOConnection.php';

function connection()
{
    try {
        $db = PDOConnection::instance();
        return $db->getConnection('mysql:host=' . HOST . ';dbname=' . DB_NAME, LOGIN, PWD);
    } catch (\Exception $e) {
        dd($e->getMessage());
    }
}

function savePosts($post)
{
    $sql = "REPLACE INTO posts (id, message, full_picture, type, created_time)
                  VALUES (:id, :message, :full_picture, :type, :created_time)";
    try {
        $stmt = connection()->prepare($sql);

        $stmt->bindParam(':id', $post->id);
        $stmt->bindParam(':message', $post->message);
        $stmt->bindParam(':full_picture', $post->full_picture);
        $stmt->bindParam(':type', $post->type);
        $date = Carbon\Carbon::parse($post->created_time)->toDateTimeString();
        $stmt->bindParam(':created_time', $date);

        $inserted = $stmt->execute();
    } catch (\Exception $e) {
        dump("Couldn't insert record, check db");
        dump($e->getMessage());
    }
}

function getPostsDb($cols = '*')
{
    try {
        $statement = connection()->prepare("SELECT {$cols} FROM posts");
        $statement->execute();

        return $statement;
    } catch (\Exception $e) {
        dd($e->getMessage());
    }
}

function saveReaction($reactions)
{
    $v = '';
    foreach ($reactions as $key => $reaction) {
        $v .= "('{$reaction['id']}',{$reaction['LIKE']},{$reaction['HAHA']},{$reaction['ANGRY']},{$reaction['SAD']},{$reaction['WOW']},{$reaction['LOVE']}), ";
    }

    $sql = "REPLACE INTO reactions (post_id, LIKEE, HAHA, ANGRY, SAD, WOW, LOVE) VALUES " . $v;
    $sql = rtrim($sql, ', ');

    try {
        $stmt = connection()->exec($sql);
    } catch (\Exception $e) {
        dump("Couldn't insert record, check db");
        dump($e->getMessage());
    }
}

function saveComments($comments, $post_id)
{

    $v = '';
    foreach ($comments as $key => $comment) {
        $msg = addslashes($comment['message']);
        $v .= "('{$comment['id']}','{$post_id}','{$msg}',{$comment['like_count']},'{$comment['created_time']}'), ";
    }

    $sql = "REPLACE INTO comments (id, post_id, message, like_count, created_time) VALUES " . $v;
    $sql = rtrim($sql, ', ');

    try {
        $stmt = connection()->exec($sql);
    } catch (\Exception $e) {
        dump("Couldn't insert record, check db");
        dump($e->getMessage());
    }
}
