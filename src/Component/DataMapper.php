<?php
namespace Component;
use Model\Core\Helper\SQL\Helper;
use Monolog\Logger;
use PDO;
use Stripe\Stripe;

$stripe = array(
    "secret_key"      => "sk_test_8BxTOeXo8UnKVVqmtt0IG6sf",
    "publishable_key" => "pk_test_Q0H9XgWLqmqFhAiwL0MXWgBV"
);

\Stripe\Stripe::setApiKey($stripe['secret_key']);

/**
 * Description of DataMapper
 *
 * @author Arslan Hajdarevic <arslan.h@tech387.com>
 */
class DataMapper
{

    protected $connection;
    protected $configuration;
    protected $sqlHelper;
    protected $monolog;

    /**
     * Creates new mapper instance
     *
     * @param PDO $connection
     * @param array $configuration A list of table name aliases
     *
     * @codeCoverageIgnore
     */
    public function __construct(PDO $connection, array $configuration, Logger $monolog)
    {
        // , array $configuration
        $this->connection = $connection;
        $this->sqlHelper = new Helper();
        $this->configuration = $configuration;
        $this->monolog = $monolog;
    }
}

