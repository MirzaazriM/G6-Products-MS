<?php
namespace Component;
use Model\Contract\CanCreateMapper;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use RuntimeException;
use PDO;
/**
 * Description of MapperFactory
 *
 * @author Arslan Hajdarevic <arslan.h@tech387.com>
 */
class MapperFactory implements CanCreateMapper {

    private $connection;
    private $cache = [];
    private $configuration;
    private $monolog;


    /**
     * Creates new factory instance
     *
     * @param PDO $connection
     * @param array $configuration A list of table name aliases
     */
    public function __construct(PDO $connection, array $configuration)
    {
        // , array $configuration
        $this->connection = $connection;
        $this->configuration = $configuration;
        $this->monolog = new Logger('monolog');
        $this->monolog->pushHandler(new StreamHandler('../resources/loggs/monolog.txt', Logger::WARNING));
    }


    /**
     * Method for retrieving an SQL data mapper instance
     *
     * @param string $className Fully qualified class name of the mapper
     * @throws RuntimeException if mapper's class can't be found
     */
    public function create(string $className): DataMapper
    {
        if (array_key_exists($className, $this->cache)) {
            return $this->cache[$className];
        }
        if (!class_exists($className)) {
            throw new RuntimeException("Mapper not found. Attempted to load '{$className}'.");
        }
        $instance = new $className($this->connection, $this->configuration, $this->monolog);
        $this->cache[$className] = $instance;
        return $instance;
    }

}