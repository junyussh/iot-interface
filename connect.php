<?php
header('Content-Type: application/json; charset=utf-8');
define("DB_username", "root");
define("DB_passwd", "j08160816");
define("DB_name", "jgate_modbus");
define("DB_host", "localhost");

class Database
{
    public $conn;
    public function getConnection()
    {
        $this->conn = new mysqli(DB_host, DB_username, DB_passwd, DB_name);
        if ($conn->connect_error) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }
}

function change_name($name)
{
    // select all query
    $sql = "UPDATE device_info SET name=" . $name . " WHERE ID=1;";
    echo $sql;
    // prepare query statement
    $stmt = mysqli_query($conn, $sql);

    return $stmt;
}
/*
if ($db->connect_error) {
die("Connection failed: " . mysqli_connect_error());
}

// prepare query statement
$stmt = mysqli_query($db, $sql);
 */
$method = $_SERVER['REQUEST_METHOD'];
$input = file_get_contents('php://input');
$db = new mysqli(DB_host, DB_username, DB_passwd, DB_name);

switch ($method) {
    case "POST":
        $res = new stdClass();
        $obj = json_decode($input);
        $sql = "UPDATE device_info SET name='" . $obj->name . "' WHERE ID=1;";
        $stmt = mysqli_query($db, $sql);
        if ($stmt) {
            $res->error = false;
            $res->message = "save changes";
        } else {
            $res->error = true;
            $res->message = "Query error";
        }
        echo json_encode($res);
        break;
    case "GET":
        $sql = "SELECT * FROM device_info;";
        $result = mysqli_query($db, $sql);
        $row    = mysqli_fetch_assoc($result);
        $arr = new stdClass();
        $arr->error = false;
        $arr->name = $row;
        //echo json_encode($arr);
        echo $row["name"];
        break;
}
