# PHP-Route
PHP Router to route request.

This library solves the following use case below:
- [x] Add Route attribute to class and function
- [x] Implement main middleware together with sub-middlewares
- [x] Loop through middleware after matching url and method

# Usages

Initialize a Route class that will store all paths 

```php
<?php

#[Route('/api', Method::GET, [self::class, 'auth'])]
class API{
	public function auth(){}
	
	public function check_awesome(){}

	#[Route('/get', Method::POST, [self::class, 'check_awesome'])]
	public function get(){
	}

	#[Route('/get', Method::POST, [self::class, 'check_awesome'])]
	public function get(){
	}
}

$url = '/about';
foreach(Route::get(API::class)->match($url, Method::POST) as $middleware_with_matches)
	var_dump($middleware_with_matches);

```

This will collect all the routes stated in the class and loop through the middlewares if the path that holds the middlewares matches.