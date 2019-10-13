<?php

namespace models;

use PDO;

class Comments
{
    /**
     * @var array Errors from user
     */
    public $errors = [];

    /**
     * @var array Array of fields
     */
    public $fields = ['name', 'email', 'text'];

    /**
     * @var array Array of values
     */
    public $values;

    /**
     * Constructor.
     */
    public function __construct()
    {
        foreach ($this->fields as $field) {
            $this->values[$field] = "";
        }
    }


    /**
     * Return name of table
     *
     * @return string
     */
    static public function getTable(){
        return 'comments';
    }

    /**
     * Return an array of rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'email', 'text'], 'not_empty'],
            [['name'], 'string', 'max_length' => 100],
            [['text'], 'string'],
            [['email'], 'mail'],
        ];
    }

    /**
     * Returns true if is not empty value
     * and fills an array of errors if false
     * @param $field
     * @return false|int
     */
    private function isNotEmptyValue($field)
    {
        if (mb_strlen($this->values[$field]) > 0) return true;
        $this->errors[$field][] = "This field must be filled!";
        return false;
    }

    /**
     * Returns true if is it correct e-mail
     * and fills an array of errors if false
     * @param $field
     * @return false|int
     */
    private function isEmail($field)
    {
        $pattern = '/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/i';
        if (preg_match($pattern, $this->values[$field])) return true;
        $this->errors[$field][] = "This field must be a valid e-mail!";
        return false;
    }

    /**
     * Returns true if is it string
     * and fills an array of errors if false
     * @param $field
     * @return false|int
     */
    private function isString($field)
    {
        if (is_string($this->values[$field])) return true;
        $this->errors[$field][] = "This field must be a string!";
        return false;
    }

    /**
     * Returns true if length of the value does not exceed the maximum value
     * and fills an array of errors if false
     *
     * @param $field
     * @param $max_length
     * @return false|int
     */
    private function isNotMaxLength($field, $max_length)
    {
        if (mb_strlen($this->values[$field]) <= $max_length) return true;
        $this->errors[$field][] = "Number of characters in this field must not exceed {$max_length}!";
        return false;
    }

    /**
     *  Returns false if $_REQUEST[$field] is not set
     *  and fills an array of errors
     *
     * @param $form
     * @return bool
     */
    private function isValuesInRequest($form)
    {
        $result = true;
        foreach ($this->fields as $field) {
            if (!isset($_REQUEST[$form][$field])) {
                $result = false;
                $this->errors[$field][] = "Fill this field please and try again!";
            }
        }
        return $result;
    }


    /**
     * Trim all values in array
     *
     * @return bool
     */
    private function trimAll()
    {
        foreach ($this->values as $ind => $value) {
            $this->values[$ind] = trim($value);
        }
        return true;
    }

    /**
     * Set values from Request
     * @param $form
     */
    private function setValuesFromRequest($form)
    {
        foreach ($this->fields as $field) {
            $this->values[$field] = $_REQUEST[$form][$field];
        }
    }

    /**
     * Get and set the values from Request
     * return true if all correct
     *
     * @param $form
     * @return bool
     */
    public function setAttributes($form)
    {
        if ($this->isValuesInRequest($form)) {
            $this->setValuesFromRequest($form);
            $this->trimAll();
            return true;
        }
        return false;
    }

    /**
     * Returns the truth if validation passes
     *
     * @return bool
     */
    public function validate()
    {
        $result = true;
        foreach ($this->rules() as $rule) {
            $current_rule = $rule[1];
            foreach ($rule[0] as $field) {
                if ($current_rule === 'not_empty') {
                    $result = !$this->isNotEmptyValue($field) ? false : $result;
                } elseif ($current_rule === 'mail') {
                    $result = !$this->isEmail($field) ? false : $result;
                } elseif ($current_rule === 'string') {
                    if (($result = !$this->isString($field) ? false : true) && isset($rule['max_length'])) {
                        $result = !$this->isNotMaxLength($field, $rule['max_length']) ? false : true;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Make array for pdo execute in PDO
     *
     * @return array
     */

    private function getBindData(){
        $arr = [];
        foreach ($this->fields as $field) {
            $arr[":".$field] = $this->values[$field];
        }
        return $arr;
    }


    /**
     * Return object PDO connection
     *
     * @return PDO
     */
    private static function getPdo() {
        return new PDO("mysql:host=" . CONFIG['db']['host'] . ";dbname=" . CONFIG['db']['db'] . ";",
            CONFIG['db']['user'],
            CONFIG['db']['password']);
    }


    /**
     * Save data to a table
     *
     * @return bool
     */
    public function save()
    {
        $pdo = self::getPdo();
        $bind_data = $this->getBindData();
        $columns = implode(', ', $this->fields);
        $bind_alias = implode( ', ', array_keys($bind_data));
        $sql = 'INSERT INTO `'.self::getTable().'`(' . $columns . ') VALUES (' . $bind_alias . ')';
        $query = $pdo->prepare($sql);
        if ($result = $query->execute($bind_data)) {
            $_SESSION['flash'][] = 'Your data was saved!';
        }
        return $result;

    }

    /**
     * Get all data from table
     *
     * @return array
     */
    static public function getAll() {
        $pdo = self::getPdo();
        $sql = 'SELECT * FROM `'.self::getTable().'`';
        $query = $pdo->query($sql);
        return $query->fetchAll();
    }

}