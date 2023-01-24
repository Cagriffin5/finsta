<?php 
//get every set variable 
$vars = get_defined_vars(); 
$output = '';
foreach ($vars as $key => $value) {
 
  $output .= "<div class='card'><footer><b>\$$key</b><br>";
  $output .= print_r($value, true);
  $output .= '</footer></div>';
}
?>
<label for="modal_1" class="button" id="debug-button">DEBUG</label>
<div class="modal" id="debug-output">
  <input id="modal_1" type="checkbox" />
  <label for="modal_1" class="overlay"></label>
  <article>
    <header>
      <h3>Debug Info. ESC to close. F9 to open. </h3>
  
      <label for="modal_1" class="button close">&times;</label>
    </header>

    <section class="debug-array"><?php print_r( $output ); ?></section>    
  </article>
</div>