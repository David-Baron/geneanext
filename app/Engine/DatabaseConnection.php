<?php


class DatabaseConnection
{
    protected \PDO $db;
    protected string $table_prefix = '';

    public function __construct()
    {
        if (file_exists(__DIR__ . '/../../connexion_inc.php')) {
            $this->_oldConnection();
        } else {
            $this->_connection();
        }
    }

    /**
     * Old connection from geneamania
     * @deprecated Use env file and _connection() function insteed
     */
    private function _oldConnection()
    {
        require(__DIR__ . '/../../connexion_inc.php');
        $this->db = new \PDO("mysql:host=$nserveur;dbname=$ndb", $nutil, $nmdp);
        $this->db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }

    private function _connection()
    {
        if (!isset($_ENV['DB_HOST']) || !isset($_ENV['DB_NAME'])) {
            throw new \Exception("Environment database parameters not found.", 1);
        }

        $this->db = new \PDO("mysql:host=$_ENV[DB_HOST];dbname=$_ENV[DB_NAME];charset=utf8mb4", $_ENV['DB_USER'], $_ENV['DB_PASS']);
        $this->db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'dev') {
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        if (isset($_ENV['DB_TABLES_PREFIX'])) {
            $this->table_prefix = $_ENV['DB_TABLES_PREFIX'];
        }
    }
}
