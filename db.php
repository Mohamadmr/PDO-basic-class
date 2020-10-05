<?php


class db
{
    protected $pdo;
    private $server;
    private $db;
    private $user;
    private $password;
    private $tbl;

    public function __construct($server = "", $db = "", $user = "", $pass = "")
    {
        $this->server = $server;
        $this->db = $db;
        $this->user = $user;
        $this->password = $pass;
        $this->connection();
    }

    public function connection()
    {
        try {
            $this->pdo = new PDO("mysql:host={$this->server};dbname={$this->db}", $this->user, $this->password);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    //set table's name from front
    public function setTbl($tbl)
    {
        $this->tbl = $tbl;
    }

    //general select data
    public function selectData($data)
    {
        if (is_array($data)) {
            //generate sql command
            $value = "'" . implode("','", $data) . "'";
        }
        $stm = $this->pdo->prepare("SELECT {$value} FROM {$this->tbl}");
        $stm->execute();
        $row = $stm->fetchAll(PDO::FETCH_OBJ);
        return $row;
    }

    //general insert to database
    public function insertData($fields, $data)
    {
        if (is_array($data)) {
            //generate sql command
            $data = "'" . implode("','", $data) . "'";
        }
        if (is_array($fields)) {
            //generate sql command
            $filds = implode(",", $fields);
        }
        $stm = $this->pdo->prepare("INSERT INTO {$this->tbl} ($fields) VALUES ($data)");
        $stm->execute();
    }

    //general update a record from database (email is unique in this database.)
    public function updateData($id, $fields, $data)
    {
        //we need change many filds
        if (is_array($fields)) {
            foreach ($fields as $key => $val) {
                $command [] = $val . "='" . $data[$key] . "'";
            }
            $command = implode(",", $command);
        } else {
            //or just we need change one field
            $command = "'{$fields}'='{$data}'";
        }
        $stm = $this->pdo->prepare("UPDATE {$this->tbl} SET " . $command . " WHERE id='$id'");
        $stm->execute();
    }

    //general delete a record from database (email is unique in this database.)
    public function deleteData($id)
    {
        $stm = $this->pdo->prepare("DELETE FROM {$this->tbl} WHERE id='$id'");
        $stm->execute();
    }

    //general search for data from database
    public function searchData($key, $value)
    {
        $stm = $this->pdo->prepare("SELECT * FROM {$this->tbl} WHERE $key='$value'");
        $stm->execute();
        //email is unique in this database then return just one record
        $results = $stm->fetch(PDO::FETCH_OBJ);
        return $results;
    }

    //general search for data from database
    public function searchLikeData($key, $value)
    {
        $stm = $this->pdo->prepare("SELECT * FROM {$this->tbl} WHERE $key LIKE '$value'");
        $stm->execute();
        $results = $stm->fetchAll(PDO::FETCH_OBJ);
        return $results;
    }
}
