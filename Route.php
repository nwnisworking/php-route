<?php
if(!enum_exists('Method')){
	enum Method{
		case GET;
		case POST;
		case DELETE;
		case PATCH;
	}
}

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class Route{
	public ?array $middlewares = [];

  public array $paths = [
    Method::GET->name=>[],
    Method::POST->name=>[],
    Method::DELETE->name=>[],
    Method::PATCH->name=>[]
  ];

	public function __construct(
		public readonly string $path,
		public readonly Method $method = Method::GET,
		array ...$middlewares
	)
	{
		$this->middlewares = $middlewares;
	}

  /**
   * Registers a path into route 
   */
  public function add(string $path, Method $method, array $middlewares): self{
    $this->paths[$method->name][$this->path.$path] = array_merge($this->middlewares, $middlewares);
    return $this;
  }

  /**
   * Find the matching url and return middleware
   */
  public function match(string $url, Method $method = Method::GET): ?Generator{
    foreach($this->paths[$method->name] as $path=>$middlewares){
      if(preg_match("#$path#", $url, $matches))
        foreach($middlewares as $v)
          yield [$v, array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY)];

      else
        continue;
    }

    return null;
  }

  /**
   * Get registered path inside a class
   */
  public static function get(string $class): ?self{
    $class = new ReflectionClass($class);
    $main = $class->getAttributes(self::class);

    if(!count($main))
      return null;

    $main = $main[0]->newInstance();

    foreach($class->getMethods(ReflectionMethod::IS_PUBLIC) as $fn){
      foreach($fn->getAttributes(self::class) as $attr){
        $arg = $attr->getArguments();
        $main->add(
          array_shift($arg), 
          array_shift($arg), 
          [...$arg, [$fn->class, $fn->name]]
        );
      }
    }

    return $main;
  }
}