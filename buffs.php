<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        if (isset($_GET['user_id'])) {
            $user_id = $_GET['user_id'];
            $sql = "SELECT * FROM buffs WHERE user_id = :user_id";
        }

        if (isset($_GET['buff_id'])) {
            $buff_id = $_GET['buff_id'];
            $sql = "SELECT * FROM buffs WHERE buff_id = :buff_id";
        }




        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($buff_id)) {
                $stmt->bindParam(':buff_id', $buff_id);
            }

            if (isset($user_id)) {
                $stmt->bindParam(':user_id', $user_id);
            }



            $stmt->execute();
            $buffs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($buffs);
        }



        break;





    case "POST":
        $buff = json_decode(file_get_contents('php://input'));
        // Prepare SQL statement
        $sql = "INSERT INTO buffs (buff_id, buff_name, buff_type, created_at, user_id) 
                VALUES (:buff_id, :buff_name, :buff_type, :created_at, :user_id)";

        $stmt = $conn->prepare($sql);

        // Bind parameters
        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':buff_id', $buff->buff_id);
        $stmt->bindParam(':buff_name', $buff->buff_name);
        $stmt->bindParam(':buff_type', $buff->buff_type);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':user_id', $buff->user_id);

        // Execute statement
        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Buff inserted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Failed to insert buff"
            ];
        }

        echo json_encode($response);
        break;

    case "PUT":
        $buffs = json_decode(file_get_contents('php://input'));
        $sql = "UPDATE buffs 
        SET 
            buff_name = :buff_name,
            buff_type = :buff_type,
            user_id = :user_id
        WHERE
            buff_id = :buff_id";

        $stmt = $conn->prepare($sql);

        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':buff_name', $buffs->buff_name);
        $stmt->bindParam(':buff_type', $buffs->buff_type);
        $stmt->bindParam(':user_id', $buffs->user_id);
        $stmt->bindParam(':buff_id', $buffs->buff_id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Buff information updated successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Failed to update buff information"
            ];
        }
        echo json_encode($response);
        break;

    case "DELETE":
        $field = json_decode(file_get_contents('php://input'));
        $sql = "DELETE FROM buffs WHERE buff_id = :buff_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':buff_id', $field->buff_id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "buff_id deleted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "buff_id delete failed"
            ];
        }

        echo json_encode($response);
        break;
}
