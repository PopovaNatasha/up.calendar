<?php

use Bitrix\Main\Routing\Controllers\PublicPageController,
	Bitrix\Main\Routing\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->get('/', new PublicPageController('/local/modules/up.calendar/views/calendar-main.php'));
    $routes->get('/login', new PublicPageController('/local/modules/up.calendar/views/calendar-login.php'));
    $routes->get('/MyGroups', new PublicPageController('/local/modules/up.calendar/views/calendar-my-teams.php'))->where('query', '/^[a-zа-яё\d]{1}[a-zа-яё\d\s]*[a-zа-яё\d]{1}$/i');
    $routes->post('/MyGroups', new PublicPageController('/local/modules/up.calendar/views/calendar-my-teams.php'));
    $routes->get('/AllGroups', new PublicPageController('/local/modules/up.calendar/views/calendar-teams.php'));
    $routes->get('/Profile', new PublicPageController('/local/modules/up.calendar/views/calendar-profile.php'));

//    $routes->post('/', new PublicPageController('/local/modules/up.people/views/task-list.php'));
//    $routes->get('/delete/{id}/', new PublicPageController('/local/modules/up.people/views/task-delete.php'))->where('id', '[0-9]+');
//    $routes->get('/task/{id}/', new PublicPageController('/local/modules/up.people/views/task-details.php'))->where('id', '[0-9]+');
//    $routes->post('/task/{id}/', new PublicPageController('/local/modules/up.people/views/task-details.php'))->where('id', '[0-9]+');
//    $routes->get('/documentation/', new PublicPageController('/local/modules/up.people/views/task-documentation.php'));
};