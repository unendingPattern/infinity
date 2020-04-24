<?php
// include "inc/functions.php";

if (php_sapi_name() == 'fpm-fcgi' && !$admin && count($_GET) == 0) {
        error('Cannot be run directly.');
}

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function shortenText($text, $maxlength = 70, $appendix = "...")
{
  if (mb_strlen($text) <= $maxlength) {
    return $text;
  }
  $text = mb_substr($text, 0, $maxlength - mb_strlen($appendix));
  $text .= $appendix;
  return $text;
}

$RT_boards = listBoards(TRUE, TRUE);
$RT_board_tables = array();
$RT_threads = array();
$RT_sort_threads = array();
$RT_posts = array();
foreach ($RT_boards as &$board) {
        $query = sprintf("SELECT ``id`` AS 'id', ``subject`` as 'title', ``body_nomarkup`` as 'content', ``bump`` as 'time' FROM ``posts_%s`` WHERE ``thread`` IS NULL ORDER BY ``bump`` DESC LIMIT 1", $board);
        //echo $query;
        $threadQuery = prepare($query);
        $threadQuery->execute() or error(db_error($threadQuery));
        $threadResult = $threadQuery->fetchAll(PDO::FETCH_ASSOC);
        //print_r($threadResult);
        foreach ($threadResult as &$thread) {
                if (isset($thread['id'])) {
                        $query = sprintf("SELECT COUNT(*) AS 'reply_count' FROM ``posts_%s`` WHERE ``thread`` = %s", $board, $thread['id']);
                        //echo $query;
                        $threadQuery = prepare($query);
                        $threadQuery->execute() or error(db_error($threadQuery));
                        $threadResult = $threadQuery->fetchAll(PDO::FETCH_ASSOC)[0];
                        //print_r($threadResult);



                        //echo $thread['id'] . " - " . $thread['title'] . "\n";
                        $RT_threads[$board] = array(
                                'id'=>$thread['id'],
                                'title'=>$thread['title'],
                                'content'=>$thread['content'],
                                'time'=>$thread['time'],
                                'replies'=>$threadResult['reply_count'],
                                'board'=>$board,
                                );
                }
        }
}

usort($RT_threads, function($a, $b) {
    return $a['time'] - $b['time'];
});

$RT_threads = array_reverse($RT_threads);

$RT_HTML = '
    <table class="board-list-table">
        <colgroup>
            <col class="board-title">
                <col class="board-uri">
                    <col class="board-max">
                        <col class="board-unique">
        </colgroup>
        <thead class="board-list-head">
            <tr>
                <th class="board-title" data-column="title">Recent Threads</th>
                <th class="board-uri" data-column="uri">Board</th>
                <th class="board-max" data-column="posts_total">Replies</th>
                <th class="board-unique" data-column="active">Last Post</th>
            </tr>
        </thead>
        <tbody class="board-list-tbody">';

foreach ($RT_threads as $thread) {
        $thread_id = $thread['id'];
        if (isset($thread['title'])) {
                $thread_title = $thread['title'];
        }else{
                $thread_title = $thread['content'];
        }
        //$thread_content = $thread['content'];
        $thread_time = $thread['time'];
        $thread_replies = $thread['replies'];
        $thread_board = $thread['board'];

        $RT_HTML .= '
            <tr>
                <td class="board-title">
                    <p class="board-cell"><a href="/'.$thread_board.'/res/'.$thread_id.'.html">'.shortenText($thread_title, 30).'</a></p>
                </td>
                <td class="board-uri">
                    <p class="board-cell"><a href="/'.$thread_board.'/">/'.$thread_board.'/</a></p>
                </td>
                <td class="board-max">
                    <p class="board-cell">'.$thread_replies.'</p>
                </td>
                <td class="board-unique">
                    <p class="board-cell">'.time_elapsed_string('@'.$thread_time).'</p>
                </td>
            </tr>';
}
$RT_HTML .= '
        </tbody>
    </table>';


/*if (php_sapi_name() == 'cli') {
        $page_RT_HTML = Element("page.html", array(
                        "title" => "Recent threads on ".$config['site_name'],
                        "config" => $config,
                        "body"   => $RT_HTML,
                )
        );

        file_write("recent_threads.html", $page_RT_HTML);
}*/

//echo $RT_HTML;
