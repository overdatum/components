## Httpful Laravel Bundle

[Httpful](http://phphttpclient.com) is a simple Http Client library for PHP 5.3+.  There is an emphasis of readability, simplicity, and flexibility – basically provide the features and flexibility to get the job done and make those features really easy to use.

This only bundles Httpful for usage with Laravel, all credit for Httpful goes to Nate Good. 

- [Httpful on GitHub](http://github.com/nategood/httpful)
- [Httpful Landing Page](http://phphttpclient.com)

The current version of the bundle uses commit 50c6314cd5 of Httpful.

### Installation

```bash
php artisan bundle:install httpful
```

### Bundle Registration

Add the following to your **application/bundles.php** file:

```php
'httpful' => array(
	'auto' => true,
),
```

### Example Usage

```
$uri = "https://www.googleapis.com/freebase/v1/mqlread?query=%7B%22type%22:%22/music/artist%22%2C%22name%22:%22The%20Dead%20Weather%22%2C%22album%22:%5B%5D%7D";
$response = Httpful::get($uri)->send();
echo 'The Dead Weather has ' . count($response->body->result->album) . " albums.\n";

```

Notice that it is ``Httpful::get()`` and not ``Request::get()`` to avoid collisions between Laravel and Httpful.

More infomation about how to use Httpful [can be found here](http://phphttpclient.com).