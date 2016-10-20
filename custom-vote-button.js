$(document).ready(function(){

	var set_buttons_height = function() {
		var avatar_list_heights = [];
		$(".voted-avatar-list").each(function(i, elem) {
			avatar_list_heights.push($(elem).height());
		});

		var favbutton = $(".qa-q-view-favorite .qa-favoriting");
		var favpos = favbutton.position();
		if (favpos !== undefined) {
			favbutton.css("top", favpos.top + avatar_list_heights[0] + "px");
			$(".qa-a-selection").each(function(i, elem) {
				var elempos = $(elem).position();
				$(elem).css("top", elempos.top + avatar_list_heights[i + 1] + "px");
			});
		}
	}

	set_buttons_height();

});
