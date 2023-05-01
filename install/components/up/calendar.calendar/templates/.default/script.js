function changeView(evt, tabName) {
    var i, tablinks;

    tablinks = document.getElementsByClassName('tab');
    for (i = 0; i < 3; i++) {
        tablinks[i].className = tablinks[i].className.replace(' is-active', '');
    }
    CalendarEventsList.calendar.changeView(tabName);
    evt.currentTarget.className += ' is-active';
    setRenderRangeText();
}

function setRenderRangeText() {
    let renderRange = document.getElementById('renderRange');
    let calendar = CalendarEventsList.calendar;
    moment.locale('ru');
    let options = calendar.getOptions();
    let viewName = calendar.getViewName();
    let html = [];
    if (viewName === 'day') {
        html.push(moment(calendar.getDate().getTime()).format('DD MMMM YYYY'));
    } else if (viewName === 'month' &&
        (!options.month.visibleWeeksCount || options.month.visibleWeeksCount > 4)) {
        html.push(moment(calendar.getDate().getTime()).format('MMMM YYYY'));
    } else {
        html.push(moment(calendar.getDateRangeStart().getTime()).format('DD MMM YYYY'));
        html.push(' - ');
        html.push(moment(calendar.getDateRangeEnd().getTime()).format('DD MMM YYYY'));
    }
    renderRange.innerHTML = html.join('');
}

document.addEventListener('DOMContentLoaded', () => {
    setRenderRangeText();
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const todayBtn = document.getElementById('todayBtn');

    prevBtn.addEventListener('click', e => {
        CalendarEventsList.calendar.prev();
        setRenderRangeText();
    });

    nextBtn.addEventListener('click', e => {
        CalendarEventsList.calendar.next();
        setRenderRangeText();
    });

    todayBtn.addEventListener('click', e => {
        CalendarEventsList.calendar.today();
        setRenderRangeText();
    });

    const chButtons = document.getElementsByClassName('change-color');
    let popupForm = document.getElementById('change-color-form');
    for (let i = 0; i < chButtons.length; ++i) {
        chButtons[i].addEventListener('click', e => {
            let teamColor = chButtons[i].dataset.color;
            let teamTitle = chButtons[i].dataset.title;
            document.getElementById('input-color').value = teamColor;
            document.getElementById('input-title').value = teamTitle;
            document.getElementById('input-id').value = chButtons[i].id;
            let coordinates = chButtons[i].getBoundingClientRect();
            popupForm.style.left = coordinates.left + 'px';
            popupForm.style.top = coordinates.bottom + 'px';
            popupForm.style.display = 'block';
        });
    }

    let closeBtn = document.getElementById('close-button');
    closeBtn.addEventListener('click', e => {
        let form = document.getElementById('change-color-form');
        form.style.display = 'none';
    });
});