<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        if (isset($_GET['suitable_crops_id']) && isset($_GET['user_id']) && isset($_GET['suitable_index'])) {
            $suitable_crops_id = $_GET['suitable_crops_id'];
            $user_id = $_GET['user_id'];
            $suitable_index = $_GET['suitable_index'];
            $sql = "SELECT * FROM suitable WHERE suitable_crops_id = :suitable_crops_id AND user_id = :user_id AND suitable_index = :suitable_index";
        }


        if (!isset($_GET['suitable_crops_id']) && !isset($_GET['user_id'])) {
            $sql = "SELECT * FROM crops ORDER BY crops_id DESC ";
        }


        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($suitable_crops_id) && isset($user_id) && isset($suitable_index)) {
                $stmt->bindParam(':suitable_crops_id', $suitable_crops_id);
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':suitable_index', $suitable_index);
            }

            $stmt->execute();
            $crops = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($crops);
        }

        break;
}
