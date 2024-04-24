<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        if (isset($_GET['user_id'])) {
            $user_id = $_GET['user_id'];
            $sql = "SELECT * FROM pigs WHERE user_id = :user_id";
        }

        if (isset($_GET['pig_id'])) {
            $pig_id = $_GET['pig_id'];
            $sql = "SELECT * FROM pigs WHERE pig_id = :pig_id";
        }




        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($pig_id)) {
                $stmt->bindParam(':pig_id', $pig_id);
            }

            if (isset($user_id)) {
                $stmt->bindParam(':user_id', $user_id);
            }



            $stmt->execute();
            $pigs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($pigs);
        }



        break;





    case "POST":
        $pig = json_decode(file_get_contents('php://input'));
        // Prepare SQL statement
        $sql = "INSERT INTO pigs (pig_id, pig_tag, assigned_farmer, building, pen, short_desc, created_at, user_id) 
                VALUES (:pig_id, :pig_tag, :assigned_farmer, :building, :pen, :short_desc, :created_at, :user_id)";

        $stmt = $conn->prepare($sql);

        // Bind parameters
        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':pig_id', $pig->pig_id);
        $stmt->bindParam(':pig_tag', $pig->pig_tag);
        $stmt->bindParam(':assigned_farmer', $pig->assigned_farmer);
        $stmt->bindParam(':building', $pig->building);
        $stmt->bindParam(':pen', $pig->pen);
        $stmt->bindParam(':short_desc', $pig->short_desc);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':user_id', $pig->user_id);


        // Execute statement
        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Pig inserted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Failed to insert pig"
            ];
        }

        echo json_encode($response);
        break;

    case "PUT":
        $pigs = json_decode(file_get_contents('php://input'));
        $sql = "UPDATE pig 
        SET 
            pig_tag = :pig_tag,
            assigned_farmer = :assigned_farmer,
            building = :building,
            pen = :pen,
            short_desc = :short_desc
            user_id = :user_id
        WHERE
            pig_id = :pig_id";

        $stmt = $conn->prepare($sql);


        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':pig_tag', $pigs->pig_tag);
        $stmt->bindParam(':assigned_farmer', $pigs->assigned_farmer);
        $stmt->bindParam(':building', $pigs->building);
        $stmt->bindParam(':pen', $pigs->pen);
        $stmt->bindParam(':short_desc', $pigs->short_desc);
        $stmt->bindParam(':user_id', $pigs->user_id);
        $stmt->bindParam(':pig_id', $pigs->pig_id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Pig information updated successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Failed to update pig information"
            ];
        }
        echo json_encode($response);
        break;

    case "DELETE":
        $field = json_decode(file_get_contents('php://input'));
        $sql = "DELETE FROM pigs WHERE pig_id = :pig_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':pig_id', $field->pig_id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "pig_id deleted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "pig_id delete failed"
            ];
        }

        echo json_encode($response);
        break;
}
