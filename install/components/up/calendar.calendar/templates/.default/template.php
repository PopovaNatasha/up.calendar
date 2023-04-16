<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='utf-8' />
    <script src='/local/modules/up.calendar/install/fullcalendar/dist/index.global.min.js'></script>
</head>
<body>
<div id='calendar'>

    <script src='/local/modules/up.calendar/install/fullcalendar/packages/core/locales/ru.global.js'></script>
    <script>

        $(function() {

            $('#calendar').fullCalendar({

            });

        });

    </script>


    <script>

        document.addEventListener('DOMContentLoaded', function () {
            createCalendar();
        });
    </script>
    <script>
        function createCalendar() {
            var calendarEl = document.getElementById('calendar');



            var calendar = new FullCalendar.Calendar(calendarEl, {
                selectable: true,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'timeGridWeek,dayGridMonth'  // 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                dateClick: function (info) {
                    //alert('clicked ' + info.dateStr);
                    console.log(info);
                },
                eventClick: function (start, end, jsEvent, view, resource) {
                    console.log(
                        'select',
                        resource ? resource.id : '(no resource)'
                    );
                },
                initialView: 'timeGridWeek',
                allDaySlot: false,
                slotMinTime: "08:00:00",
                slotMaxTime: "21:00:00",
                height: 1000,
                expandRows: true,
                eventRender: function (event, element) {
                    element.find('.fc-sticky').attr("id", event.id);
                }
            });

            var event1 = {
                id: "aabbcc", title: 'Пробное событие',
                startTime: "12:00", endTime: "14:00",
                startRecur: new Date(2023, 3, 4, 12), endRecur: new Date(2023, 5, 4, 12),
                daysOfWeek: [2]
            };

            calendar.addEvent(event1);

            calendar.render();
        }
    </script>
</div>
</body>
</html>