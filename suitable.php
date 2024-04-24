<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        if (isset($_GET['suitable_crops_id']) && isset($_GET['user_id'])) {
            $suitable_crops_id = $_GET['suitable_crops_id'];
            $user_id = $_GET['user_id'];
            $sql = "SELECT * FROM suitable WHERE suitable_crops_id = :suitable_crops_id AND user_id = :user_id";
        }


        if (!isset($_GET['suitable_crops_id']) && !isset($_GET['user_id'])) {
            $sql = "SELECT * FROM crops ORDER BY crops_id DESC ";
        }


        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($suitable_crops_id) && isset($user_id)) {
                $stmt->bindParam(':suitable_crops_id', $suitable_crops_id);
                $stmt->bindParam(':user_id', $user_id);
            }

            $stmt->execute();
            $crops = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($crops);
        }

        break;


    case "POST":
        $suitable = json_decode(file_get_contents('php://input'));


        $sql = "INSERT INTO suitable (suitable_id, suitable_month, suitability, suitable_notes, suitable_index, suitable_crops_id, user_id) VALUES (:suitable_id, :suitable_month, :suitability, :suitable_notes, :suitable_index, :suitable_crops_id, :user_id)";

        $stmt = $conn->prepare($sql);
        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':suitable_id', $suitable->suitable_id);
        $stmt->bindParam(':suitable_month', $suitable->suitable_month);
        $stmt->bindParam(':suitable_notes', $suitable->suitable_notes);
        $stmt->bindParam(':suitability', $suitable->suitability);
        $stmt->bindParam(':suitable_index', $suitable->suitable_index);
        $stmt->bindParam(':suitable_crops_id', $suitable->suitable_crops_id);
        $stmt->bindParam(':user_id', $suitable->user_id);


        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "crops successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "crops failed"
            ];
        }

        echo json_encode($response);
        break;
        // }


    case "PUT":
        $suitable = json_decode(file_get_contents('php://input'));

        $sql = "UPDATE suitable 
                        SET suitable_month = :suitable_month, 
                            suitability = :suitability, 
                            suitable_notes = :suitable_notes, 
                            suitable_index = :suitable_index, 
                            suitable_crops_id = :suitable_crops_id
                        WHERE suitable_id = :suitable_id AND user_id = :user_id ";


        $stmt = $conn->prepare($sql);


        $stmt->bindParam(':suitable_id', $suitable->suitable_id);
        $stmt->bindParam(':suitable_month', $suitable->suitable_month);
        $stmt->bindParam(':suitable_notes', $suitable->suitable_notes);
        $stmt->bindParam(':suitability', $suitable->suitability);
        $stmt->bindParam(':suitable_index', $suitable->suitable_index);
        $stmt->bindParam(':suitable_crops_id', $suitable->suitable_crops_id);
        $stmt->bindParam(':user_id', $suitable->user_id);


        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Suitability information successfully updated"
            ];
        } else {

            $response = [
                "status" => "error",
                "message" => "Failed to update suitability information"
            ];
        }

        echo json_encode($response);
        break;

    case "DELETE":
        $crops = json_decode(file_get_contents('php://input'));
        $sql = "DELETE FROM crops WHERE crops_id = :crops_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':crops_id', $crops->crops_id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "crops deleted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "crops delete failed"
            ];
        }

        echo json_encode($response);
        break;
}
