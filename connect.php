<?php
header('Content-Type: application/json; charset=utf-8');
define("DB_username", "root");
define("DB_passwd", "root");
define("DB_name", "jgate_modbus");
define("DB_host", "localhost");

$method = $_SERVER['REQUEST_METHOD'];
$input = file_get_contents('php://input');
$db = new mysqli(DB_host, DB_username, DB_passwd, DB_name);

if ($db->connect_error) {
    die("Connection failed: " . mysqli_connect_error());
}

$name = exec("python get.py Name");
switch ($method) {
    case "POST":
        $res = new stdClass();
        $obj = json_decode($input);
        $sql = "UPDATE system SET value='" . $obj->name . "' WHERE name='system_name';";
        $sql .= "UPDATE cloud_setting SET name='" . $obj->name . "' WHERE ID=1;";
        $sql .= "UPDATE cloud_setting SET Domain='" . $obj-> domain . "' WHERE ID=1;";
        $sql .= "UPDATE cloud_setting SET Frequency=". $obj -> frequency ." WHERE ID=1;";
        $sql .= "UPDATE cloud_setting SET Active=". $obj -> active ." WHERE ID=1;";
        $stmt = $db -> multi_query($sql);
        
        /*
        $file = fopen("setting.txt", "w") or die("Unable to open file!");
        $txt = $obj -> frequency;
        fwrite($file, $txt);
        fclose($file);
        */
        if ($stmt) {
            $res->error = false;
            $res->message = "save changes";
        } else {
            $res->error = true;
            $res->message = mysqli_error($db);
        }
        echo json_encode($res);
        break;
    case "GET":
        $sql = "SELECT * FROM device_info;";
        $result = mysqli_query($db, $sql);
        $row = mysqli_fetch_assoc($result);
        $arr = new stdClass();
        $arr->error = false;
        $arr->name = $row;
        //echo json_encode($arr);
        echo $row["name"];
        break;
}
