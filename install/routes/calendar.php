<?php

use Bitrix\Main\Routing\Controllers\PublicPageController,
    Bitrix\Main\Routing\RoutingConfigurator,
	Up\Calendar\Controller\Calendar;

return function (RoutingConfigurator $routes) {
    $routes->get('/', new PublicPageController('/local/modules/up.calendar/views/calendar-main.php'));
    $routes->get('/', new PublicPageController('/local/modules/up.calendar/views/calendar-main.php'));
    $routes->post('/', new PublicPageController('/local/modules/up.calendar/views/calendar-main.php'));

    $routes->get('/groups/my/', new PublicPageController('/local/modules/up.calendar/views/calendar-my-teams.php'));
    $routes->post('/groups/my/', new PublicPageController('/local/modules/up.calendar/views/calendar-my-teams.php'));

    $routes->get('/groups/', new PublicPageController('/local/modules/up.calendar/views/calendar-teams.php'));

    $routes->get('/profile/', new PublicPageController('/local/modules/up.calendar/views/calendar-profile.php'));
    $routes->post('/profile/', new PublicPageController('/local/modules/up.calendar/views/calendar-profile.php'));

    $routes->any('/auth', new PublicPageController('/local/modules/up.calendar/views/auth.php'));

    $routes->get('/group/{id}/', new PublicPageController('/local/modules/up.calendar/views/calendar-team-detail.php'));
    $routes->post('/group/{id}/create_event/', [Calendar::class, 'createEvent'])->where('id', '[0-9]+');

    $routes->get('/invite/{link}/', new PublicPageController('/local/modules/up.calendar/views/calendar-team-invite.php'));
    $routes->post('/invite/{link}/', new PublicPageController('/local/modules/up.calendar/views/calendar-team-invite.php'));

    $routes->get('/group/{id}/schedule/', new PublicPageController('/local/modules/up.calendar/views/calendar-team-schedule.php'));
    $routes->post('/group/{id}/schedule/', new PublicPageController('/local/modules/up.calendar/views/calendar-team-schedule.php'));
};