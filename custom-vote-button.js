$(document).ready(function(){
	var va_lists = [];
	$(".voted-avatar-list").each(function(i, elem) {
		va_lists.push($(elem).height());
	});
	console.log(va_lists);
	// var favbutton = $(".qa-q-view-favorite .qa-favoriting");
	// var favpos = favbutton.position();
	// var asel = $(".qa-a-selection");
	// var aselpos = asel.position();
	// favbutton.css("top", favpos.top + aicons_height + "px");
	// asel.css("top", aselpos.top + aicons_height + "px");
});
