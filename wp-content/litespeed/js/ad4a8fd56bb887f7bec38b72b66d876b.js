(function($){$(document).ready(function(){$(document).on('click','a.gofollow',function(){$.post(click_object.ajax_url,{'action':'adrotate_click','track':$(this).attr("data-track")})})})}(jQuery))
;