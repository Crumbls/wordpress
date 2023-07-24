# Laravel from WordPress

### This package is in very early development and shouldn't be used. 
### It's only available to collect data on what is left to be completed between a few users.
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
$wp = app('wordpress');
```

This returns an instance of Crumbls\Services\WordPress, which will let you execute basic wp-json pulls.  I'm adding parameters 
as time allows.  If you have a feature request, just email me.