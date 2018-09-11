<?php

include_once ("../dbconnection/mysqlConnector.php");
include_once ("UserDao.php");

class UserDaoMysql implements UserDao
{
    // private $usr = new User();
    private $dbConn;

    public function __construct()
    {
    }

    // Insert new User
    public function insertUser($userName, $password, $firstname, $lastname, $company, $role)
    {
        $dbConn = new mysqlConnector();

        $sql = "INSERT INTO user(userName, password, firstname, lastname, role) VALUES (?, ?, ?, ?, ?)";
  
        $stmt = $dbConn->getConnector()->prepare($sql);
        $stmt->bind_param('ssssss', $userName, $password, $firstname, $lastname, $company, $role);
        $stmt->execute();
        
        $dbConn->getConnector()->close();
    }

    public function updateUser($id)
    {

    }
    public function deleteUser($id)
    {

    }
    public function selectUser($id)
    {

    }
}

?>