<?php

use Bitrix\Main\Routing\Controllers\PublicPageController,
	Bitrix\Main\Routing\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->get('/', new PublicPageController('/local/modules/up.calendar/views/calendar-main.php'))->where('query', '/^[a-zа-яё\d]{1}[a-zа-яё\d\s]*[a-zа-яё\d]{1}$/i');
//    $routes->post('/', new PublicPageController('/local/modules/up.people/views/task-list.php'));
//    $routes->get('/delete/{id}/', new PublicPageController('/local/modules/up.people/views/task-delete.php'))->where('id', '[0-9]+');
//    $routes->get('/task/{id}/', new PublicPageController('/local/modules/up.people/views/task-details.php'))->where('id', '[0-9]+');
//    $routes->post('/task/{id}/', new PublicPageController('/local/modules/up.people/views/task-details.php'))->where('id', '[0-9]+');
//    $routes->get('/documentation/', new PublicPageController('/local/modules/up.people/views/task-documentation.php'));
};