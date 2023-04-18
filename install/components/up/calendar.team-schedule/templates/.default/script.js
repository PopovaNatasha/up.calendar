function changeView(evt, tabName)
{
	console.log(tabName);
	var i, x, tablinks;

	tablinks = document.getElementsByClassName("tab");
	for (i = 0; i < 2; i++) {
		tablinks[i].className = tablinks[i].className.replace(" is-active", "");
	}
	calendar.changeView(tabName);
	evt.currentTarget.className += " is-active";
}