<?php

function dd()
{
    dump(func_get_args());
    exit();
}

function cleanReaction($reactions)
{
    $output = [];
    foreach ($reactions as $key => $react) {
        $el = [];
        foreach ($react as $type => $reactions) {
            if ('id' == $type) {
                $el[$type] = $reactions;
                continue;
            }
            $el[$type] = $reactions->summary->total_count;
        }
        $output[] = $el;
    }
    return $output;
}

function cleanComment($comment)
{
    $hasChildren = false;
    $comments = [];

    $cmt = cleanCmt($comment);
    $comments[] = $cmt;
    if (!isset($comment->comments)) {
        return $comments;
    }
    foreach ($comment->comments->data as $com) {
        $comments[] = cleanCmt($com);
    }

    return $comments;
}

function cleanCmt($comment)
{
    foreach ($comment as $key => $attr) {
        if ('comments' == $key) {
            $hasChildren = true;
            continue;
        }
        if ('created_time' == $key) {
            $attr = Carbon\Carbon::parse($attr)->toDateTimeString();
        }
        $parent[$key] = $attr;
    }

    return $parent;
}

function cleanString($string)
{
    $string = str_replace('"', "", $string);
    $string = str_replace("'", "", $string);
    $string = trim(preg_replace('/\s\s+/', ' ', $string));
    $string = remove_emoji($string);

    return $string;
}

function remove_emoji($text)
{
    return preg_replace('/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $text);
}
