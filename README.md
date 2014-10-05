# ghdocs

A weekend project to render github docs easily with github api, inspired by viewdocs.

[Demo](http://ghdocs.herokuapp.com/)

Powered by open-source tools and github api.

## Running your own

Heroku filesystem is [readonly](https://devcenter.heroku.com/articles/read-only-filesystem)
so have added db caching for the github api's. You can change the
[line](https://github.com/harikt/ghdocs/blob/dd92eb2307af89b98aa7d7899827bafcbe6cd5f7/config/Common.php#L33)

```php
$di->setter['Github\HttpClient\CachedHttpClient']['setCache'] = $di->lazyNew('Rtdocs\Domain\Github\DbCache');
$di->params['Rtdocs\Domain\Github\DbCache']['pdo'] = $di->lazyGet('connection');
$di->params['Pdo'] = array(
    'dsn' => getenv('DB_CONNECTION')
);
$di->set('connection', $di->lazyNew('Pdo'));
```

to

```
$di->setter['Github\HttpClient\CachedHttpClient']['setCache'] = $di->lazyNew('Github\HttpClient\Cache\FilesystemCache');
$di->params['Github\HttpClient\Cache\FilesystemCache']['path'] = __DIR__ . '/tmp';
```
