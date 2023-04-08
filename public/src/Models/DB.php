<?php

namespace App\Models;

use Exception;
use PDO;
use PDOException;

class DB
{
    private string $table;
    private string $query;
    private array $values;
    private array $columns;
    private array $errors;

    /**
     * @throws Exception
     */
    public function __construct($table)
    {
        if (empty($table))
            throw new Exception('Database Table is required');
        else
            $this->table = $table;
        return $this;
    }

    ## PDO Connection ##

    /**
     * @throws Exception on connection error
     */
    private function PDOConnection()
    {
        require __DIR__ . '/config.php';

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            return new PDO('mysql:host=' . $dbHost . ';dbname=' . $dbName . ';charset=utf8', $dbUser, $dbPass, $options);
        } catch (PDOException $exception) {
            throw new Exception('Error connecting to database');
        }
    }

    ## Check query
    private function checkQuery($pdo)
    {
        $validate = $pdo->prepare($this->query);
        if (!$validate) {
            throw new Exception("Query could not be prepared");
        }
        return $validate;
    }

    ## Set columns and values 
    public function setColumnsAndValues($columnsAndValues)
    {
        if (!is_array($columnsAndValues)) throw new Exception('setColumnsAndValues requires an array as parameter');
        $columns = array();
        $values = array();
        foreach ($columnsAndValues as $column => $value) {
            $columns[] = $column;
            $values[] = $value;
        }
        $this->columns = $columns;
        $this->values = $values;
        return $this;
    }

    ## Set errors ##
    private function setError($error)
    {
        $this->errors = $error;
        return $this;
    }

    ## Get errors ##
    public function getErrors()
    {
        return $this->errors;
    }

    ## Insert ##
    public function insert()
    {
        if (empty($this->values)) throw new Exception('Values are required');
        if (empty($this->columns)) throw new Exception('Columns are required');

        $valueString = rtrim(str_repeat("?, ", count($this->values)), ", ");
        $this->query = 'INSERT INTO ' . $this->table . ' (' . implode(', ', $this->columns) . ') values (' . $valueString . ')';
        $pdo = $this->PDOConnection();
        $statement = $this->checkQuery($pdo);
        $statement->execute($this->values);
        if ($statement->rowCount() != 0) {
            return $pdo->lastInsertId();
        } else {
            $this->setError($statement->errorInfo());
        }
        return false;
    }

    ## Run Query ##
    public function makeQuery($data)
    {
        // Create connection
        $pdoConnection = $this->PDOConnection();
        $query = $pdoConnection->prepare($data['query']);
        if (!empty($data['values'])) {
            $query->execute($data['values']);
        } else {
            $query->execute();
        }

        // Confirm Query
        if (!empty($data['returnConfirmation'])) {
            if ($query->rowCount() > 0) {
                return true;
            } else {
                return 500;
            }
        }

        // return insert ID
        if (!empty($data['returnInsertID'])) {
            if ($query->rowCount() == 1) {
                return $pdoConnection->lastInsertId();
            }
        }

        // Fetch Results
        if (!empty($data['singleRecord'])) {
            return $query->fetch(PDO::FETCH_OBJ);
        } else {
            return $query->fetchAll(PDO::FETCH_OBJ);
        }
    }
}
