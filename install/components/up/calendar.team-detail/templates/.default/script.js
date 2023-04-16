// const MONTH_STRING_EN = [
// 	'Jan',
// 	'Feb',
// 	'Mar',
// 	'Apr',
// 	'May',
// 	'Jun',
// 	'Jul',
// 	'Aug',
// 	'Sep',
// 	'Oct',
// 	'Nov',
// 	'Dec'
// ]
// var dateInput = document.getElementsByClassName('date-input')[0]
//
// renderDates();
// dateInput.addEventListener('change', console.log('ggg'))
//
// function renderDates(){
// 	var today
// 	if(dateInput.value)
// 		today = new Date(dateInput.value)
// 	else
// 		today = new Date()
//
// 	var year = today.getFullYear();
// 	var month = today.getMonth();
// 	var daysOfMonth = getDaysOfMonth(today.getFullYear(), today.getMonth())
//
// 	var daysDom = Array.apply(null, {length: daysOfMonth}).map(function(_, i){
// 		var class_string = '';
// 		if (i + 1 === today.getDate())
// 			class_string = 'date-item current';
// 		else
// 			class_string = 'date-item'
// 		return "<span class='" + class_string + "'>" + (i+1) + "</span>"
// 	}).join('')
//
//
//
// 	document.getElementsByClassName('dates')[0].innerHTML = daysDom
//
// 	document.getElementsByClassName('year-month')[0].innerHTML = MONTH_STRING_EN[today.getMonth()] + " " + today.getFullYear();
//
// 	document.getElementsByClassName('dates')[0].addEventListener('click', changeDate.bind({dateInput, year, month}))
//
//
//
// }
//
// function changeDate(e) {
// 	var date = this.year + '/' + (this.month + 1) + '/' + e.target.textContent;
// 	this.dateInput.value = date;
// 	renderDates();
// }
//
// function createDomElement(type, props, child){
// 	var element = document.createElement(type);
//
// 	Object.keys(props).forEach(function (key) {
// 		element.setAttribute(key, props[key]);
// 	});
//
// 	switch (typeof child) {
// 		case 'number':
// 		case 'string':
// 			element.innerHTML = child;
// 			break;
// 		case 'object':
// 			element.innerHTML = child.join('');
// 			break;
// 	}
//
// 	return element;
// }
//
// function getDaysOfMonth (year, month) {
// 	return [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
// }