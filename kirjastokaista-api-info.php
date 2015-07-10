<?php

if (isset($posti->{'id'})) {
	$media['id'] = $posti->{'id'};
}
if (isset($posti->{'url'})) {
	$media['url'] = $posti->{'url'};
}
if (isset($posti->{'title'})) {
	$media['title']= $posti->{'title'};
}
if (isset($posti->{'content'})) {
	$media['content']= $posti->{'content'};
}
if (isset($posti->{'excerpt'})) {
	$media['excerpt']= $posti->{'excerpt'};
}
if (isset($posti->{'date'})) {
	$media['date']= $posti->{'date'};
}
if (isset($posti->{'custom_fields'}->{'duration'}[0])) {
	$media['duration']= $posti->{'custom_fields'}->{'duration'}[0];
}
if (isset($posti->{'custom_fields'}->{'video_thumbnail_large'}[0])) {
	$media['video_thumbnail_large']= $posti->{'custom_fields'}->{'video_thumbnail_large'}[0];
}
if (isset($posti->{'custom_fields'}->{'video_thumbnail_medium'}[0])) {
	$media['video_thumbnail_medium']= $posti->{'custom_fields'}->{'video_thumbnail_medium'}[0];
}
if (isset($posti->{'custom_fields'}->{'video_thumbnail_small'}[0])) {
	$media['video_thumbnail_small']= $posti->{'custom_fields'}->{'video_thumbnail_small'}[0];
}
if (isset($posti->{'custom_fields'}->{'videoembed_videoembed'}[0])) {
	$media['embedcode'] = $posti->{'custom_fields'}->{'videoembed_videoembed'}[0];
}
?>