# Laravel from WordPress

The goal of this package is to provide an abstract factory pattern via a manager to access WordPress from Laravel. Our
current driver requirement is to be able to read, albeit some drivers may be able to write in the future. We use this
to aid with migration away from WordPress or use WordPress as a CRM with Laravel on the front-end.


TODO:
* Write more tests
* Write how-to

app('wordpress') returns an instance of Crumbls\Services\WordPress.