<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        if (isset($_GET['user_id'])) {
            $user_id = $_GET['user_id'];
            $sql = "SELECT * FROM crops WHERE user_id = :user_id";
        }

        if (isset($_GET['crops_id'])) {
            $crops_id_spe = $_GET['crops_id'];
            $sql = "SELECT * FROM crops WHERE crops_id = :crops_id";
        }


        if (!isset($_GET['crops_id']) && !isset($_GET['user_id'])) {
            $sql = "SELECT * FROM crops ORDER BY crops_id DESC ";
        }


        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($crops_id_spe)) {
                $stmt->bindParam(':crops_id', $crops_id_spe);
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
        $crops = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO crops (crops_id, crops_img, crops_name, planting_method, pest_brand, fertilizer_type, harvesting_cal, fertilizer, pest, obnotes, created_at, variety, user_id) VALUES (:crops_id, :crops_img, :crops_name, :planting_method, :pest_brand, :fertilizer_type, :harvesting_cal, :fertilizer, :pest, :obnotes, :created_at, :variety, :user_id)";

        $stmt = $conn->prepare($sql);
        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':crops_id', $crops->crops_id);
        $stmt->bindParam(':crops_img', $crops->crops_img);
        $stmt->bindParam(':crops_name', $crops->crops_name);

        $stmt->bindParam(':planting_method', $crops->planting_method);
        $stmt->bindParam(':harvesting_cal', $crops->harvesting_cal);
        $stmt->bindParam(':pest', $crops->pest);
        $stmt->bindParam(':obnotes', $crops->obnotes);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':variety', $crops->variety);
        $stmt->bindParam(':user_id', $crops->user_id);
        $stmt->bindParam(':fertilizer', $crops->fertilizer);
        $stmt->bindParam(':pest_brand', $crops->pest_brand);
        $stmt->bindParam(':fertilizer_type', $crops->fertilizer_type);



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

    case "PUT":
        $crops = json_decode(file_get_contents('php://input'));

        $sql = "UPDATE crops SET crops_img = :crops_img, crops_name = :crops_name, planting_method = :planting_method, 
        harvesting_cal = :harvesting_cal, pest = :pest, obnotes = :obnotes, pest_brand = :pest_brand, fertilizer_type = :fertilizer_type,  fertilizer = :fertilizer,
        variety = :variety, user_id = :user_id WHERE crops_id = :crops_id AND user_id = :user_id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':crops_id', $crops->crops_id);
        $stmt->bindParam(':crops_img', $crops->crops_img);
        $stmt->bindParam(':crops_name', $crops->crops_name);
        $stmt->bindParam(':planting_method', $crops->planting_method);
        $stmt->bindParam(':harvesting_cal', $crops->harvesting_cal);
        $stmt->bindParam(':pest', $crops->pest);
        $stmt->bindParam(':pest_brand', $crops->pest_brand);
        $stmt->bindParam(':fertilizer_type', $crops->fertilizer_type);
        $stmt->bindParam(':obnotes', $crops->obnotes);
        $stmt->bindParam(':fertilizer', $crops->fertilizer);
        $stmt->bindParam(':variety', $crops->variety);
        $stmt->bindParam(':user_id', $crops->user_id);


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
