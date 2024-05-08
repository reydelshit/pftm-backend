<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        if (isset($_GET['user_id'])) {
            $user_id = $_GET['user_id'];
            $sql = "SELECT sched.*, pigs.assigned_farmer FROM sched INNER JOIN pigs ON pigs.pig_tag = sched.pig_id WHERE sched.user_id = :user_id";
        }



        if (isset($sql)) {
            $stmt = $conn->prepare($sql);


            if (isset($user_id)) {
                $stmt->bindParam(':user_id', $user_id);
            }



            $stmt->execute();
            $buffs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($buffs);
        }



        break;
}
