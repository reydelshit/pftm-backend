<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        if (isset($_GET['user_id']) && isset($_GET['crops_id'])) {
            $user_id = $_GET['user_id'];
            $crops_id = $_GET['crops_id'];
            $sql = "SELECT * FROM crops WHERE crops_id = :crops_id AND user_id = :user_id";
        }


        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($crops_id)) {
                $stmt->bindParam(':crops_id', $crops_id);
                $stmt->bindParam(':user_id', $user_id);
            }


            $stmt->execute();
            $crops = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($crops);
        }



        break;
}
