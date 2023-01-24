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

/**
 * Sanitize any string user input. Strips HTML and converts special characters
 * @param  string $dirty the untrusted string data
 * @return string        the sanitized data
 */
function clean_string( $dirty ){
    $clean = htmlspecialchars( trim( strip_tags( $dirty ) ), ENT_QUOTES );
    return $clean;
}

/**
 * Display the HTML feedback element for basic forms
 * @param  string $heading the H2 content
 * @param  array  $list    the list of issues to fix
 * @param  string $class   either "success" or "error"
 * @return mixed          HTML element
 */
function show_feedback( $heading, $list = array(), $class = 'error' ){
    if( isset( $heading ) AND $heading != '' ){
        echo "<div class='feedback $class'>";
        echo "<h2>$heading</h2>";
        //if the list is not empty, show it is a <ul>
        if( ! empty( $list ) ){
            echo '<ul>';
            foreach( $list as $item ){
                echo "<li>$item</li>";
            }
            echo '</ul>';
        }
        echo '</div>';
    }
}

/**
* displays sql query information including the computed parameters.
* Silent unless DEBUG MODE is set to 1 in CONFIG.php
* @param [statement handler] $sth -  any PDO statement handler that needs troubleshooting
*/
function debug_statement($sth){
    if( DEBUG_MODE ){
        echo '<pre>';
        $info = debug_backtrace();
        echo '<b>Debugger ran from ' . $info[0]['file'] . ' on line ' . $info[0]['line'] . '</b><br><br>';
        $sth->debugDumpParams();
        echo '</pre>';
    }
}