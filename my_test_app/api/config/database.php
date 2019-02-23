<?php
class Database{
 
    // specify your own database credentials
    private $host = "tb154ws8l9a25zm.cszeqrsni6fa.us-east-2.rds.amazonaws.com";
    private $db_name = "registration_v1";
    private $username = "test";
    private $password = "12345698";
    public $conn;
 
    // get the database connection
    public function getConnection(){
 
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
 
        return $this->conn;
    }
}
?>
