<?php

use Bitrix\Main\Routing\Controllers\PublicPageController,
	Bitrix\Main\Routing\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->get('/', new PublicPageController('/local/modules/up.calendar/views/calendar-main.php'))->where('query', '/^[a-zа-яё\d]{1}[a-zа-яё\d\s]*[a-zа-яё\d]{1}$/i');
    $routes->get('/', new PublicPageController('/local/modules/up.calendar/views/calendar-main.php'));
	$routes->post('/', new PublicPageController('/local/modules/up.calendar/views/calendar-main.php'));

    $routes->get('/groups/my/', new PublicPageController('/local/modules/up.calendar/views/calendar-my-teams.php'))->where('query', '/^[a-zа-яё\d]{1}[a-zа-яё\d\s]*[a-zа-яё\d]{1}$/i');
    $routes->post('/groups/my/', new PublicPageController('/local/modules/up.calendar/views/calendar-my-teams.php'));

    $routes->get('/groups/', new PublicPageController('/local/modules/up.calendar/views/calendar-teams.php'));
    $routes->get('/profile/', new PublicPageController('/local/modules/up.calendar/views/calendar-profile.php'));

	$routes->any('/auth', new PublicPageController('/local/modules/up.calendar/views/auth.php'));



	$routes->get('/group/{id}/', new PublicPageController('/local/modules/up.calendar/views/calendar-team-detail.php'));
    $routes->post('/group/{id}/', new PublicPageController('/local/modules/up.calendar/views/calendar-team-detail.php'))->where('id', '[0-9]+');

    $routes->get('/invite/{link}/', new PublicPageController('/local/modules/up.calendar/views/calendar-team-invite.php'));
    $routes->post('/invite/{link}/', new PublicPageController('/local/modules/up.calendar/views/calendar-team-invite.php'));

	$routes->get('/group/{id}/schedule/', new PublicPageController('/local/modules/up.calendar/views/calendar-team-schedule.php'));
	$routes->post('/group/{id}/schedule/', new PublicPageController('/local/modules/up.calendar/views/calendar-team-schedule.php'));
};