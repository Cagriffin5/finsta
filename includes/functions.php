<?php
/**
 * Count the comments on any post 
 * @param int $post_id theID of the post we are counting comments for 
 * @return int          the number of comments
 */ 
function count_comments( $post_id ){
    global $DB;
    $result = $DB->prepare('SELECT COUNT(*) AS total
    FROM comments
    WHERE post_id = ?
    AND is_approved = 1');
//run it and bind the variable to the placeholder (?)
$result->execute( array( $post_id ) );
//check it 
if( $result->rowCount () > 0 ){
    //loop it 
    while( $row = $result->fetch()){
        echo $row['total'];
        }
    }
}

/**
 * convert any date/time stamp to a human friendly format 
 *
 * @param datetime $timestamp the original timestamp in any format
 * @return string       displays the date like " january 23rd "
 */
function nice_date( $timestamp ){
    $output  = new DateTime( $timestamp );
    echo $output->format('F jS'); 
}


/**
 * convert a date into the "time ago"
 * @param  string  $datetime 
 * @param  boolean $full     whether to break down the hours, minutes, seconds
 * @link https://stackoverflow.com/questions/1416697/converting-timestamp-to-time-ago-in-php-e-g-1-day-ago-2-days-ago
 */
function time_ago($datetime, $full = false) {
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