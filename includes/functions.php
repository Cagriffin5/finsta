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
/**
 * Make an avatar image from a string
 * @param  string $string the string to put on the avatar
 * @param  int $size   size in pixels, square
 * @return string         file path to image
 */
function make_letter_avatar($string, $size){
    //random pastel color
    $H =   mt_rand(0, 360);
    $S =   mt_rand(25, 50);
    $B =   mt_rand(90, 96);

    $RGB = get_RGB($H, $S, $B);
    $string = strtoupper($string);

    $imageFilePath = 'avatars/' . $string . '_' .  $H . '_' . $S . '_' . $B . '.png';

    //base avatar image that we use to center our text string on top of it.
    $avatar = imagecreatetruecolor($size, $size);  
    //make and fill the BG color
    $bg_color = imagecolorallocate($avatar, $RGB['red'], $RGB['green'], $RGB['blue']);
    imagefill( $avatar, 0, 0, $bg_color );
    //white text
    $avatar_text_color = imagecolorallocate($avatar, 255, 255, 255);
    // Load the gd font and write 
    //$font = imageloadfont('gd-files/gd-font.gdf');
    ///imagestring($avatar, $font, 10, 10, $string, $avatar_text_color);
    
    $font =  '../fonts/poppins.ttf';
    $font_size = $size/2;

    // Get size of text
    list($left, $bottom, $right, , , $top) = imageftbbox($font_size, 0, $font, $string);
    // Determine offset of text
    $left_offset = ($right - $left) / 2;
    $top_offset = ($bottom - $top) / 2;
    // Generate coordinates
    $x = ($size/2) - $left_offset ;
    $y = ($size/2) + $top_offset;
    imagettftext($avatar, $font_size, 0, $x, $y, $avatar_text_color, $font, $string);

    imagepng($avatar, $imageFilePath);

    imagedestroy($avatar);

    return $imageFilePath;
}

/*
*  Converts HSV to RGB values
*  Input:     Hue        (H) Integer 0-360
*             Saturation (S) Integer 0-100
*             Lightness  (V) Integer 0-100
*  Output:    Array red, green, blue
*/
function get_RGB($iH, $iS, $iV) {
    if($iH < 0)   $iH = 0;   // Hue:
    if($iH > 360) $iH = 360; //   0-360
    if($iS < 0)   $iS = 0;   // Saturation:
    if($iS > 100) $iS = 100; //   0-100
    if($iV < 0)   $iV = 0;   // Lightness:
    if($iV > 100) $iV = 100; //   0-100

    $dS = $iS/100.0; // Saturation: 0.0-1.0
    $dV = $iV/100.0; // Lightness:  0.0-1.0
    $dC = $dV*$dS;   // Chroma:     0.0-1.0
    $dH = $iH/60.0;  // H-Prime:    0.0-6.0
    $dT = $dH;       // Temp variable

    while($dT >= 2.0) $dT -= 2.0; // php modulus does not work with float
    $dX = $dC*(1-abs($dT-1));     // as used in the Wikipedia link

    switch(floor($dH)) {
        case 0:
        $dR = $dC; $dG = $dX; $dB = 0.0; break;
        case 1:
        $dR = $dX; $dG = $dC; $dB = 0.0; break;
        case 2:
        $dR = 0.0; $dG = $dC; $dB = $dX; break;
        case 3:
        $dR = 0.0; $dG = $dX; $dB = $dC; break;
        case 4:
        $dR = $dX; $dG = 0.0; $dB = $dC; break;
        case 5:
        $dR = $dC; $dG = 0.0; $dB = $dX; break;
        default:
        $dR = 0.0; $dG = 0.0; $dB = 0.0; break;
    }

    $dM  = $dV - $dC;
    $dR += $dM; $dG += $dM; $dB += $dM;
    $dR *= 255; $dG *= 255; $dB *= 255;

    return  array(
        'red' =>  round($dR),
        'green'=> round($dG),
        'blue' => round($dB)
    );
}

/**
 * check to see if the viewer is logged in
 * @return array|bool false if not logged in, array of all user data if they are logged in
 */

 function check_login(){
    global $DB;
    //if the cookie is valid, turn it into session data
    if(isset($_COOKIE['access_token']) AND isset($_COOKIE['user_id'])){
        $_SESSION['access_token'] = $_COOKIE['access_token'];
        $_SESSION['user_id'] = $_COOKIE['user_id'];
    }

   //if the session is valid, check their credentials
    if( isset($_SESSION['access_token']) AND isset($_SESSION['user_id']) ){
        //check to see if these keys match the DB     

         $data = array(
            'access_token' =>$_SESSION['access_token'],
        );

         $result = $DB->prepare(
            "SELECT * FROM users
            WHERE  access_token = :access_token
            LIMIT 1");
         $result->execute( $data );

         if($result->rowCount() > 0){
                //token found. confirm the user_id
            $row = $result->fetch();
            if( password_verify( $row['user_id'], $_SESSION['user_id'] ) ){
                    //success! return all the info about the logged in user
                return $row;
            }else{
                return false;
            }

        }else{
            return false;
        }
    }else{
            //not logged in
        return false;
    }
}

function category_dropdown( $cat = 0 ){
    global $DB; 
    //grt all of the category names in alpha order. 
    $result = $DB->prepare('SELECT * FROM categories ORDER BY name ASC');
    $result->execute();
    if( $result->rowCount() ){
        echo '<select name="category_id">';
        while( $row = $result->fetch()  ){
            extract($row);
            
            echo "<option value='$category_id'";
            selected($cat, $category_id );
            echo ">$name</option>";
        }
        echo '<select>';
    }
}
function show_post_image( $unique, $size = 'medium', $alt = 'post image' ){
    $url = $unique;
    if( ! strpos( $unique, 'http' ) ){
    $url = "uploads/$unique" . '_' .  "$size.jpg";
    }
    echo "<img src='$url' alt'$alt' class='post-image is-$size' >";
}
/**
 * LIKE BUTTON ADDITIONS
 * Count the likes on any post
 */

 function count_likes( $post_id ){
    global $DB;
    $result = $DB->prepare( "SELECT COUNT(*) AS total_likes
              FROM likes
              WHERE post_id = ?" );
    $result->execute( array($post_id) );
    if( $result->rowCount() >= 1 ){
      $row = $result->fetch();
      $total = $row['total_likes'];
  
      
      return $total;
  
    }
  }
  /**
 * Interface for "like" button and count
 * works on any post
 */
function like_interface( $post_id ){
    global $DB;
    global $logged_in_user;
    //is the viewer logged in?
    if($logged_in_user){
        //does the user like this post?
        $result = $DB->prepare('SELECT * FROM likes
            WHERE user_id = ?
            AND post_id = ? 
            LIMIT 1');
        $result->execute( array( $logged_in_user['user_id'], $post_id ) );
        if( $result->rowCount() ){
            $class = 'you-like';
        }else{
            $class = 'not-liked';
        }
    }//end if logged in
    ?>
    <span class="like-interface">
        <span class="<?php echo $class; ?>">
            <span class="heart-button" data-postid="<?php echo $post_id; ?>">???</span>
            <?php echo count_likes( $post_id ); ?>
        </span>
    </span>
    <?php
}

/**Checkbox Helper */
function checked( $a, $b ){

    if($a == $b){
        echo 'checked';
    }
}
/**dropdown Helper */
function selected( $a, $b ){

    if($a == $b){
        echo 'selected';
    }
}