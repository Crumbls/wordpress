# Laravel from WordPress

### This package is in very early development and shouldn't be used.   More than anything, it's a POC to demonstrate a specific programming pattern.
### It's only available to collect data on what is left to be completed between a few users. There are almost no comments.
### Regular changes will break this as it's just a proof of concept.

The goal of this package is to provide an abstract factory pattern via a manager to access WordPress from Laravel. Our
current driver requirement is to be able to read, albeit some drivers may be able to write in the future. We use this
to aid with migration away from WordPress or use WordPress as a CRM with Laravel on the front-end.

TODO:
* Include tests
* Write how-to
* wp-json authentication
* direct database access w/o Corcel.
* provide a framework to map these entities to models.
* provide notes about adding auth to the configuration for wp-json to get all data.

### Brief info.

Add the wordpress service to your services.php configuration file.
```
'wordpress' => [
    'driver' => 'wp-json',
	'wp-json' => [
		'base_uri' => 'https://your-url.com/wp-json/wp/v2/'
	]
]
````
Now inside of your command ( yes it can be used elsewhere, but this is a tool to help aid in migration, not rendering. )
```
$wp = app('wordpress')->driver('wp-json');
```

This returns an instance of Crumbls\Services\WordPress, which will let you execute basic wp-json pulls.  Although we don't
like using this inside of a controller since it's to assist with migrations, here's an example of how to do it.

```
<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;

class WordPressPageController extends Controller {
	public function __invoke() {
		$wp = app('wordpress')->driver('wp-json');
		$page = $wp->getPage(589);

		abort_if(!$page, 404);

		$routeName = 'wordpress.'.$page['slug'];
		abort_if(!$routeName, 404);

		$viewName = strtolower($routeName);
		$viewName = preg_replace("/[^a-z0-9\.-]/", '', $viewName);
		$viewName = trim($viewName, '.');
		abort_if(!\View::exists($viewName), 404);
		return view($viewName, ['page' => $page]);
	}
}
```

I'm adding parameters 
as time allows.  If you have a feature request, just email me.