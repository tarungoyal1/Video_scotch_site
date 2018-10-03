<div id="footer">
    <div id="logo">
    		<a href="/"><img src="vslogo.png" alt="Videoscotch" width="200" height="50" /></a>
    </div>
    <div id="foot_links">
         Links:<br>
         <a class="submit-link" href="/vidfetcher.php" target="_blank">Fetch a single video</a><br>
         <a class="submit-link" href="/channelfetch.php" target="_blank">Fetch all Videos of a channel</a><br>
    </div>
    <div id="pvcount">Views so far: <?php echo number_format(trim(show_pageviews())); ?></div>
    <span id="counter" style="display: none;"></span>
</div>
<script type="text/javascript">
// Check if the page has loaded completely                                         
$(document).ready( function() { 
    setTimeout( function() { 
        $("#counter").load('inc.php'); 
    }, 1000); 
}); 
</script> 