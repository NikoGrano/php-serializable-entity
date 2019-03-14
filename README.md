# Serializable Entity

Convert entity into array or json. 
Apply JsonSerializable into entity by extending a class.

See my blog post about this 
[here](https://xn--gran-8qa.fi/serializing-php-entities-to-json/)!

## Install

Via [composer](http://getcomposer.org):

```shell
composer require niko9911/serializable-entity
```

## Usage

You can use this to convert objects statically.

`EntityToArray::convert(object $entity ,[, int $recursionDepth = 2 
[, bool $throwExceptionOnRecursionLimit = true ] 
[, bool $replaceValuesOnRecursionLimit = true ]]]): array`

Or if you want benefit from implementing \JsonSerializable interface
you can extend class \Niko9911\Serializable\Serializable. In that case
you will have 3 new methods in your class, `toArray, toJson and jsonSerialize`.

### Example: Using Static Way

```php
<?php
declare(strict_types=1);

// These is just our example entities.
final class Flag
{
    /** @var string */
    private $mainColor;
    
    /** @var int */
    private $height;
    
    /** @var int */
    private $width;
    
    /** @var bool */
    private $registered;
    
    public function __construct(
        string $mainColor,
        int $height,
        int $width,
        bool $registered
    ) 
    {
        $this->mainColor = $mainColor;
        $this->height = $height;
        $this->width = $width;
        $this->registered = $registered;
    }
    
    public  function getMainColor(): string
    {
        return $this->mainColor;
    }
    
    public  function getHeight(): int
    {
        return $this->height;
    }
    
    public  function getWidth(): int
    {
        return $this->width;
    }
    
    public  function getRegistered(): bool
    {
        return $this->registered;
    }
}

// It is not mandatory to extend. Only if you want benefit from
// implementing \JsonSerializable and having toArray, toJson methods.
final class Country extends \Niko9911\Serializable\Serializable
{
    // If you don't want implement \JsonSerializable,
    // but you want methods `toArray & toJson` into
    // you entity, you can add this trait.
    use \Niko9911\Serializable\SerializableTrait;
    
    /** @var string  */
    private $name;
    
    /** @var int  */
    private $id;
    
    /** @var Flag */
    private $flag;
    
    public function __construct(string $name, int $id, Flag $flag) 
    {
        $this->name = $name;
        $this->id = $id;
        $this->flag = $flag;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getId(): int
    {
        return $this->id;
    }
    
    public function getFlag(): Flag
    {
        return $this->flag;
    }
}

$entity = new Country('Finland', 358, new Flag('Blue', 150, 245, true));

$result1 = \Niko9911\Serializable\EntityToArray::convert($entity);
$result2 = $entity->toArray();
$result3 = $entity->toJson();
$result4 = \json_encode($entity);

var_dump($result1); // ['name'=>self::NAME,'id'=>self::CODE,'flag'=>['mainColor'=>self::MAIN,'height'=>self::SIZE[0],'width'=>self::SIZE[1],'registered'=>self::REGI,'options'=>[]]]
var_dump($result1 === $result2); // True

var_dump($result3); // {"name":"Finland","id":358,"flag":{"options":[],"mainColor":"Blue","height":150,"width":245,"registered":true}}
var_dump($result3 === $result4); // True

```

## License

Licensed under the [MIT license](http://opensource.org/licenses/MIT).
