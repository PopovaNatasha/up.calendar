# Bitrix module example

Clone repository to `${doc_root}/local/modules`

Install module using admin panel

Set `Hobby Calendar` as your primary site template

## Setup modern Bitrix routing

Add `calendar.php` in `routing` section of `${doc_root}/bitrix/.settings.php` file:

```php
'routing' => ['value' => [
	'config' => ['calendar.php']
]],
```

Put following content into your `${doc_root}/index.php` file:

```php
<?php
require_once __DIR__ . '/bitrix/routing_index.php';
```

Replace following lines in your `${doc_root}/.htaccess` file:

```
-RewriteCond %{REQUEST_FILENAME} !/bitrix/urlrewrite.php$
-RewriteRule ^(.*)$ /bitrix/urlrewrite.php [L]

+RewriteCond %{REQUEST_FILENAME} !/index.php$
+RewriteRule ^(.*)$ /index.php [L]
```

## Symlinks for handy development

You probably want to make following symlinks:

```
local/components/up -> local/modules/up.calendar/install/components/up
local/templates -> local/modules/up.calendar/install/templates
local/routes/calendar.php -> local/modules/up.calendar/install/routes/calendar.php
```
