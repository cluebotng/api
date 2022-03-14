<?PHP

namespace ApiInterface;

class ApiModule
{
    private static $modules = array();

    public static function register($name, $className)
    {
        self::$modules[$name] = '\\ApiInterface\\' . $className;
    }

    public static function find($name)
    {
        if (array_key_exists($name, self::$modules)) {
            return new self::$modules[$name]();
        }
    }
}
