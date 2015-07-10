<?php
/* Truncate title */
$get_title_length = strlen($media['title']);
$thelength = 55;
$truncated_title = substr($media['title'], 0, $thelength);
if ($get_title_length > $thelength) $truncated_title .= "...";


$slideshow_content = '


<li>
	<a href="'.$media['url'].'"'.$shortcode_atts['new_window'].'">
		<img src="'.$media['video_thumbnail_large'].'" alt="'.$media['title'].'" class="kirjastokaista_carousel_image">
	</a>';

if ($shortcode_atts['enable_caption']) {
	$slideshow_content .= '
	<div class="kirjastokaista_subtitle">
		<div class="kirjastokaista_subtitle_center">
			<a href="'.$media['url'].'"'.$shortcode_atts['new_window'].'>'.$truncated_title.'</a>
		</div>
	</div>';
}

$slideshow_content .= '
</li>




'; ?>