<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='utf-8' />
    <link rel="stylesheet" href="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.css" />
    <script src="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.js"></script>
</head>
<body>

<div id="calendar" style="height: 800px">
    <script>
        const Calendar = tui.Calendar;
        const calendar = new Calendar('#calendar', {
            // Whether to use the default form popup when creating/modifying events.
            useFormPopup: true,
            // Whether to use the default detail popup when clicking events.
            useDetailPopup: true,
            // whether use default creation popup or not
            useCreationPopup: true,
            // 'day', 'week', 'month'
            defaultView: 'month',
            // shows the milestone and task in weekly, daily view
            taskView: false,
            // shows the all day and time grid in weekly, daily view
            scheduleView: false,
            // template options
            template: {
                milestone: function(schedule) {
                    return '<span style="color:red;"><i class="fa fa-flag"></i> ' + schedule.title + '</span>';
                },
                milestoneTitle: function() {
                    return 'Milestone';
                },
                task: function(schedule) {
                    return '&nbsp;&nbsp;#' + schedule.title;
                },
                taskTitle: function() {
                    return '<label><input type="checkbox" />Task</label>';
                },
                allday: function(schedule) {
                    return schedule.title + ' <i class="fa fa-refresh"></i>';
                },
                alldayTitle: function() {
                    return 'All Day';
                },
                time: function(schedule) {
                    return schedule.title + ' <i class="fa fa-refresh"></i>' + schedule.start;
                }
            },

            week: {
                dayNames: ['Вск', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                startDayOfWeek: 1,
                narrowWeekend: true
            },
            month: {
                dayNames: ['Вск', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                startDayOfWeek: 1,
                narrowWeekend: false
            },
            // list of Calendars that can be used to add new schedule
            calendars: [
                {
                    id: 'cal1',
                    name: 'Personal',
                    backgroundColor:  'red',
                }
            ],


        });
        calendar.createEvents([
            {
                id: 'event1',
                calendarId: 'cal2',
                title: 'Weekly meeting',
                start: '2023-04-18T09:00:00',
                end: '2023-04-18T10:00:00',
            },
            {
                id: 'event2',
                calendarId: 'cal1',
                title: 'Lunch appointment',
                start: '2023-04-19T12:00:00',
                end: '2023-04-19T13:00:00',
            },
            {
                id: 'event3',
                calendarId: 'cal2',
                title: 'Vacation',
                start: '2023-04-20',
                end: '2023-04-20',
                isAllday: true,
                category: 'allday',
            },
        ]);
        const timedEvent = calendar.getEvent('1', 'cal1'); // EventObject
        calendar.on('clickEvent', ({ event }) => {
            console.log(event); // EventObject
        });
    </script>
</div>
</body>
</html>