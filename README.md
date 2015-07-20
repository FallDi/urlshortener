# Workflow
1. Site-visitor (V) enters any original URL to the Input field, like
http://anydomain/any/path/etc;
2. V clicks submit button;
3. Page makes AJAX-request;
4. Short URL appears in Span element, like http://yourdomain/abCdE (don't use any
   external APIs as goo.gl etc.);
5. V can copy short URL and repeat process with another link

## Requirements
- Pure Nginx or Apache with [mod_rewrite]
- PHP 5.4+
- MySQL 5.6+ or PostgreSQL 9.2+
- [Redis] if you want use cache
- PHP extensions:
	* pdo_mysql or pdo_pgsql
	* [Redis extension] if you want use cache
	* To make sure that you have installed all add-ons, run the following command
```sh
$ php -m | grep -e pdo -e redis
pdo_mysql
pdo_pgsql
pdo_sqlite
redis
```

## Deployment

### Web-server configuration
You should configure one web-server Nginx or Apache

#### Nginx
1. Run php-fpm on 9000 port
2. Insert into you nginx.conf server section from configuration/nginx.conf
3. Change **listen** to you port
4. Change **root** to you directory as root of project

#### Apache
1. Create new Virtual host(separate domain or another port)
2. Set root directory as root of project
3. Change AllowOverride directive of you apache httpd.conf to All(needs for .htaccess file)

### Database configuration
You should configure one database MySQL or PostgreSQL
1. Specify **host/user/password/database** in config.ini file at database section
2. Specify **db** as mysql or pgsql in config.ini file at database section
3. Create relation under specified database from configuration/database.sql


### Internal implementation notes
1. If you want use cache - enable it in config.ini file and specify hostport of Redis. Cache store shortUrl <-> longUrl as Redis strings
2. Error handling must be implemented using logger into file, but not it show error message into output for development purpose
3. Url validator

# License
The content of this project itself is licensed under the [Creative Commons Attribution 3.0 license] license, and the underlying source code used to format and display that content is licensed under the [MIT license].

[Redis]:http://redis.io/
[Redis extension]:https://github.com/phpredis/phpredis
[mod_rewrite]:http://httpd.apache.org/docs/current/mod/mod_rewrite.html
[Creative Commons Attribution 3.0 license]:(http://creativecommons.org/licenses/by/3.0/us/deed.en_US)
[MIT license]:(http://opensource.org/licenses/mit-license.php)