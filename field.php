<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        if (isset($_GET['user_id'])) {
            $user_id = $_GET['user_id'];
            $sql = "SELECT * FROM field WHERE user_id = :user_id";
        }

        if (isset($_GET['field_id'])) {
            $field_id = $_GET['field_id'];
            $sql = "SELECT * FROM field WHERE field_id = :field_id";
        }




        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($field_id)) {
                $stmt->bindParam(':field_id', $field_id);
            }

            if (isset($user_id)) {
                $stmt->bindParam(':user_id', $user_id);
            }



            $stmt->execute();
            $crops = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($crops);
        }



        break;





    case "POST":
        $field = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO field (field_id, field_name, field_size, location, soil_type, irrigation_system, crop_history, created_at, user_id) 
                VALUES (:field_id, :field_name, :field_size, :location, :soil_type, :irrigation_system, :crop_history, :created_at, :user_id)";

        $stmt = $conn->prepare($sql);
        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':field_id', $field->field_id);
        $stmt->bindParam(':field_name', $field->field_name);
        $stmt->bindParam(':field_size', $field->field_size);
        $stmt->bindParam(':location', $field->location);
        $stmt->bindParam(':soil_type', $field->soil_type);
        $stmt->bindParam(':irrigation_system', $field->irrigation_system);
        $stmt->bindParam(':crop_history', $field->crop_history);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':user_id', $field->user_id);



        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "field successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "field failed"
            ];
        }

        echo json_encode($response);
        break;

    case "PUT":
        $field = json_decode(file_get_contents('php://input'));
        $sql = "UPDATE field 
        SET 
            field_name = :field_name,
            field_size = :field_size,
            soil_type = :soil_type,
            irrigation_system = :irrigation_system,
            crop_history = :crop_history,
            location = :location,
            user_id = :user_id
        WHERE
            field_id = :field_id";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':field_name', $field->field_name);
        $stmt->bindParam(':field_size', $field->field_size);
        $stmt->bindParam(':soil_type', $field->soil_type);
        $stmt->bindParam(':irrigation_system', $field->irrigation_system);
        $stmt->bindParam(':crop_history', $field->crop_history);
        $stmt->bindParam(':location', $field->location);

        // $created_at = date('Y-m-d H:i:s');
        // $stmt->bindParam(':created_at', $created_at);

        $stmt->bindParam(':user_id', $field->user_id);
        $stmt->bindParam(':field_id', $field->field_id);



        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "crops updated successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "crops update failed"
            ];
        }

        echo json_encode($response);
        break;

    case "DELETE":
        $field = json_decode(file_get_contents('php://input'));
        $sql = "DELETE FROM field WHERE field_id = :field_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':field_id', $field->field_id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "field_id deleted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "field_id delete failed"
            ];
        }

        echo json_encode($response);
        break;
}
