<?php

class DB
{
  /**
   * @var Db Stores an instance of the Db class.
   */
  protected static $instance;

  /**
   * @var string The name of the querying.
   */
  protected $table;

  /**
   * @var \PDO Stores an instance of the PDO class.
   */
  protected $conn;

  /**
   * @var array The name of the columns.
   */
  protected $columns = array('*');

  /**
   * @var array Any database error messages.
   */
  protected $message = array();

  /**
   * @var string The query string.
   */
  protected $query;

  /**
   * @var string The where clauses.
   */
  protected $where;

  /**
   * @var string The join query
   */
  protected $join;

  /**
   * @var array The data to go along with the query.
   */
  protected $query_data = array();

  /**
   * @var int The number of records to limit.
   */
  protected $limit;

  /**
   * @var string The order in which records are pulled.
   */
  protected $order;

  /**
   * To determine if the class should return the insert ID on success.
   * @var boolean
   */
  protected $get_id = false;

  protected static $host;
  protected static $username;
  protected static $password;
  protected static $database;

  /**
   * To enable debugging
   * @var boolean
   */
  protected static $debug = false;

  protected $from;

  public static function connect(array $connect)
  {
    self::$host = $connect['host'];
    self::$username = $connect['username'];
    self::$password = $connect['password'];
    self::$database = $connect['database'];
  }

  /**
   * The construct function opens up a connection to
   * the database using the provided settings.
   */
  protected function __construct()
  {
    try {
      $this->conn = new PDO('mysql:host='.self::$host.';dbname='.self::$database.';', self::$username, self::$password, array(
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
      ));
    } catch (PDOException $e) {
      throw new PDOException('Database Connection Error: ' . $e->getMessage());
    }
  }

  /**
   * Creates a singleton instance of the Db class
   * and the returns it.
   * @return DB An instance of the DB class.
   */
  protected static function init()
  {
    if (!self::$instance instanceof self) {
      static::$instance = new self;
    }
    return self::$instance;
  }

  /**
   * Sends the DB object into debug mode. This will dump out
   * various information when executing commands such as the query
   * constructued, where data, etc.
   * @return null
   */
  public static function debug()
  {
    self::$debug = true;
  }

  /**
   * Gets the primary key of the specified table.
   * @return string The primary key name of the specified table.
   */
  protected function get_primary_key()
  {
    $query = $this->query; // Backup the query.
    $this->query = 'SHOW KEYS FROM ' . $this->table . ' WHERE Key_name = \'PRIMARY\'';
    $result = $this->execute();
    if (empty($result)) return false; // Nothing found so prevent any errors.
    $primary_key = $result[0]->Column_name;
    $this->query = $query;
    return $primary_key;
  }

  /**
   * Inserts an array of data into the database. You must pass an array
   * of data like so: array('ColName' => 'ColValue')
   * @param  array  $data The data to be inserted into the database
   * @return integer       Returns the ID for the data inserted.
   */
  public function insert_get_id(array $data)
  {
    $this->get_id = true;
    return $this->insert($data);
  }

  /**
   * Inserts an array of data into the database. You must pass an array
   * of data like so: array('ColName' => 'ColValue')
   * @param  array  $data The data to be inserted into the database
   * @return boolean True on success, False on failure
   */
  public function insert(array $data)
  {
  	$this->query = 'INSERT INTO ' . $this->table . ' (';
  	$tmp = '';
  	foreach ($data as $key => $value) {
    	$this->query .= $tmp . $key;
      $tmp = ', ';
  	}
    $this->query .= ') VALUES (';
    $tmp = '';
    foreach ($data as $key => $value) {
      $this->query .= $tmp . '?';
      $tmp = ', ';
      $this->query_data[] = $value;
    }
    $this->query .= ');';

    return $this->execute('INSERT');
  }

  /**
   * Set the name of the table the client is currently working with
   *
   * @param string The name of the table
   * @return object The current object
   */
  public static function table($table)
  {
    $db = static::init();
    $db->clean();
    $db->table = $table;
    return $db;
  }

  /**
   * Updates a current record in the database, supply a where-> clause
   * to specify what record gets updated.
   * @param  array  $data The data of the new records
   * @return boolean       True|False
   */
  public function update(array $data)
  {
    $this->query = 'UPDATE ' . $this->table . ' SET ';
    $tmp = '';
    $this->query_data = array(); // Reset the query data

    foreach ($data as $key => $value) {
      $this->query .= $tmp . '`'.$key.'`' . ' = ?';
      $tmp = ', ';
      $this->query_data[] = $value;
    }
    $this->from = null;

    return $this->execute('UPDATE');
  }

  public function delete($columns = '', $all = false)
  {
    if (empty($this->where) && $all === false) {
      throw new \Exception('Warning: You are trying to delete all the records.');
    }
    $this->query = 'DELETE '.$columns.' FROM ' . $this->table;
    return $this->execute('DELETE');
  }

  public function where_in($column, array $data)
  {
    // DELETE FROM message WHERE id IN (4, 5)
    // SELECT * FROM `user` WHERE `id` IN (1, 2, 4)

    $this->where = $column . ' IN (' . implode(', ', $data) . ')';

    return $this;
  }

  /**
   * Performs a simple but effective LIKE statement
   * @param  string $column The name of the column to search
   * @param  string $value  The value you would like to match
   */
  public function like($column, $value)
  {
    $this->where($column, 'LIKE', "'%$value%'");
    return $this;
  }

  /**
   * Performs a where clause.
   *
   * @param string $column The name of the column we are working with
   * @param string $operator The operator. Eg: =, >, <, != etc.
   * @param string $value The value of the column.
   * @param string $connector The name of the where clause connector. AND, OR.
   * @return $this
   */
  public function where($column, $operator = null, $value = null, $connector = 'AND')
  {
    // Prepare the where part of the query.
    $where = "$column $operator $value";

    // Check if the current where query is empty, if not
    // then we need to append.
    if (empty($this->where)) {
      $this->where = $where;
    } else {
      // Yeah... like I said append.
      $this->where .= ' ' . $connector . ' ' . $where;
    }
    // and... Fin!
    return $this;
  }

  /**
   * Performs an OR WHERE clause.
   *
   * @param string $column The name of the column we are working with
   * @param string $operator The operator. Eg: =, >, <, != etc.
   * @param string $value The value of the column.
   * @return $this
   */
  public function or_where($column, $operator = null, $value = null)
  {
    return $this->where($column, $operator, $value, 'OR');
  }

  /**
   * Unfinished
   *
   * Join two tables together
   * @param  string $table    The name of the table to join
   * @param  string $col1     The name of the first column
   * @param  string $operator The operator to use Eg: >, <, = etc.
   * @param  string $col2     The name of the second column
   * @param  string $type     The type of join to perform INNER, LEFT etc.
   * @return Db
   */
  public function join($table, $col1, $operator = null, $col2 = null, $type = 'INNER')
  {
    $join = "$type JOIN $table ON $col1 $operator $col2";
    $this->join .= ' ' . $join;
    return $this;
  }

  /**
   * Unfinished
   *
   * Left join two tables together
   * @param  string $table    The name of the table to join
   * @param  string $col1     The name of the first column
   * @param  string $operator The operator to use Eg: >, <, = etc.
   * @param  string $col2     The name of the second column
   * @return Db
   */
  public function left_join($table, $col1, $operator = null, $col2 = null)
  {
    $this->join($table, $col1, $operator, $col2, 'LEFT');
    return $this;
  }

  /**
   * Get the minimum value of the specified columns
   * @param  string $column The name of the column
   * @return array|bool
   */
  public function min($column)
  {
    $this->limit = 1;
    $this->query = "SELECT MIN($column) as min FROM $this->table";
    return $this->execute();
  }

  /**
   * Get the maximum value for the specified
   * @param  string $column The name of the column to find maximum.
   * @return integer|false
   */
  public function max($column)
  {
    $this->limit = 1;
    $this->query = "SELECT MAX($column) as max FROM $this->table";
    return $this->execute();
  }

  /**
   * Get the average of a column
   * @param  string $column The name of the column to get the average for
   * @return integer|false
   */
  public function avg($column)
  {
    $this->limit = 1;
    $this->query = "SELECT AVG($column) as average FROM $this->table";
    return $this->execute();
  }

  /**
  * Get the sum of a column
  * @param  integer $column The name of the column to return the sum
  * @return integer|false
  */
  public function sum($column)
  {
    $this->limit = 1;
    $this->query = "SELECT SUM($column) AS sum FROM $this->table";
    return $this->execute();
  }

  /**
   * Get the count of the items in a column
   * @param  string $column The name of the column
   * @return int The record count
   */
  public function count($column = '*')
  {
    $this->limit = 1;
    $this->query = "SELECT COUNT($column) AS count FROM $this->table";
    return $this->execute();
  }

  /**
   * Unfinished
   *
   * Allows the client to execute RAW SQL commands.
   * @param  string  $query   The query to be executed.
   * @param  boolean $command Does this command return any values.
   */
  public static function raw($query, $command = false)
  {
    $conn = static::init()->conn;
    if ($command) {
      return $conn->exec($query);
    }
    $sth = $conn->query($query);
    return $sth->fetchAll();
  }

  /**
   * Seems more 'normal' to call limit than grab.
   * @param  integer $amount The amount to limit by.
   * @return this
   */
  public function limit($amount = 1)
  {
    $this->grab($amount);
    return $this;
  }

  /**
   * Grab a specified amount of records.
   * @param  integer $amount The amount of records to return.
   * @return Db
   */
  public function grab($amount = 1)
  {
    $this->limit = (int)$amount;
    return $this;
  }

  public function offset($amount)
  {
    if (isset($this->limit)) {
      $this->limit .= ', ' . (int)$amount;
    }
    return $this;
  }

  /**
   * Grab the last record from the table.
   */
  public function last()
  {
    return $this->first(true);
  }

  /**
   * Grab the first record from the table
   */
  public function first($last = false)
  {
    $primary_key = $this->get_primary_key();
    $this->from = ' FROM ' . $this->table;
    $this->order = ' ORDER BY ' . $primary_key;
    $this->order .= ($last === true) ? ' DESC' : '';
    $this->limit = 1;
    return $this->execute();
  }

  /**
   * Order records by ASC|DESC
   */
  public function order_by($column, $keyword)
  {
    // TODO: Check!
    if (empty($this->order)) {
      $this->order = ' ORDER BY ' . $column . ' ' . $keyword;
    } else {
      $this->order .= ', ' . $column . ' ' . $keyword;
    }
    return $this;
  }

  /**
   * Grab records by their column names.
   */
  public function only()
  {
    $args = func_get_args();
    return $this->get(empty($args) ? array('*') : $args);
  }

  /**
   * Finds a record by its ID, this method assumes the database has
   * a field named ID. The client may pass an optional parameter to
   * specify a custom column name. The client may also pass an array of
   * data to perform a where clause. Eg: array('username' => 'jonno')
   *
   * @param  mixed $data   An integer or array depending.
   * @param  string $column (OPTIONAL) Custom name for the ID field.
   * @return array Returns an array of records as objects.
   */
  public function find($data, $column = null)
  {
    $this->from = ' FROM ' . $this->table;
    if (is_array($data) && $column === null) {
      // We have been passed a bundle of joy
      foreach ($data as $key => $value) {
        $this->where($key, '=', $value);
      }
    } else {
      // Assume this is an INT for ID
      $this->limit = 1;
      $this->where(!is_null($column) ? $column : 'id', '=', (int)$data);
    }
    return $this->execute();
  }

  /**
   * This method instructs the DB class to execute any
   * queries the client has created.
   * @param  array  $columns (OPTIONAL) The columns to be returned.
   * Eg: array('id', 'username', 'password AS pass')
   */
  public function get(array $columns = array())
  {
    $this->from = ' FROM ' . $this->table;
    if (!empty($columns)) $this->columns = $columns;
    return $this->execute();
  }

  /**
   * Clean all of the query thingies
   * @return NULL
   */
  protected function clean()
  {
    $this->where = null;
    $this->where_data = null;
    $this->join  = null;
    $this->limit = null;
    $this->order = null;
    $this->from = null;
    $this->query = null;
    $this->query_data = null;
    $this->columns = array('*');
  }

  public function __call($method, $params)
  {
    if (!preg_match('/^(find)By(\w+)$/i', $method, $matches)) {
      throw new \Exception("Called to undefined method [$method]");
    }
    $criterialKeys = explode('And', preg_replace('/([a-z0-9])([A-Z])/', '$1$2', $matches[2]));
    $criterialKeys = array_map('strtolower', $criterialKeys);
    $criterialValues = array_slice($params, 0, count($criterialKeys));
    $criteria = array_combine($criterialKeys, $criterialValues);
    $method = $matches[1];
    return $this->$method($criteria);
  }

  private function __clone() {}

  protected function execute($type = 'SELECT')
  {
    if (strlen($this->query) === 0) {
      $this->query = "SELECT ";

      // 1. Do we need to select any columns?
      $tmp = '';
      foreach($this->columns as $column) {
        $this->query .= $tmp . $column;
        $tmp = ', ';
      }
    }

    // From...
    if (strlen($this->query) === 0) {
      $this->query .= ' FROM ' . $this->table;
    }

    if (!is_null($this->from)) {
      $this->query .= $this->from;
    }

    // Do we have a join?
    if (!empty($this->join)) {
      $this->query .= $this->join;
    }

    // Do we have a where clause?
    if (!empty($this->where)) {
      $this->query .= ' WHERE ' . $this->where;
    }

    if (!empty($this->order)) {
      $this->query .= $this->order;
    }

    // Shall we limit the records returned?
    if (!empty($this->limit) && $type != 'UPDATE') {
      $this->query .= ' LIMIT ' . $this->limit;
    }

    $sth = $this->conn->prepare($this->query);

    $sth->execute($this->query_data);

    // // Any debugging sir?
    if (self::$debug) {
      echo "Database Errors";
      var_dump($this->conn->errorInfo());
      echo "<br>";

      echo "QUERY:";
      var_dump($this->query);
      echo "<br>";

      echo "Query Data";
      var_dump($this->query_data);
      echo "<br>";

      echo "Where Clause";
      var_dump($this->where);
      echo "<br>";

      // echo "Return from the database";
      // return;
    }

    switch (strtoupper($type)) {
      case 'SELECT':
        $data = (isset($this->limit) && $this->limit === 1)
                ? $sth->fetch() : $sth->fetchAll();
        break;
      case 'INSERT':
        if ($sth->rowCount() > 0) {
          return ($this->get_id) ? $this->conn->lastInsertId() : true;
        } else return false;
        break;
      case 'UPDATE':
      case 'DELETE':
        return ($sth->rowCount() > 0) ? true : false;
        break;
      default:
        throw new \Exception('['.$type.'] unknown action called.. I\'m out!');
        break;
    }
    // Clean up lads.
    $this->clean();
    return (count($data) > 0) ? $data : null;
  }
}
