<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='utf-8' />
    <link rel="stylesheet" href="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.css" />
    <script src="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.js"></script>
</head>
<body>
<script>
    const Calendar = tui.Calendar;
</script>
<div id="calendar" style="height: 800px">
    <script>

        const calendar = new Calendar('#calendar', {
            defaultView: 'month',
            template: {
                time(event) {
                    const { start, end, title } = event;

                    return `<span style="color: white;">${formatTime(start)}~${formatTime(end)} ${title}</span>`;
                },
                allday(event) {
                    return `<span style="color: gray;">${event.title}</span>`;
                },
            },
            calendars: [
                {
                    id: 'cal1',
                    name: 'Personal',
                    backgroundColor: '#03bd9e',
                },
            ],
        });
        calendar.createEvents([
            {
                id: '1',
                calendarId: '1',
                title: 'my event',
                category: 'time',
                dueDateClass: '',
                start: '2023-04-18T22:30:00+09:00',
                end: '2023-04-19T02:30:00+09:00',
            },
            {
                id: '2',
                calendarId: '1',
                title: 'second event',
                category: 'time',
                dueDateClass: '',
                start: '2018-01-18T17:30:00+09:00',
                end: '2018-01-19T17:31:00+09:00',
            },
        ]);
    </script>
</div>
</body>
</html>