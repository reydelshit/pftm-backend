<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        if (isset($_GET['user_id'])) {
            $user_id = $_GET['user_id'];
            $sql = "SELECT farmer.farmer_name, 
            COUNT(pigs.assigned_farmer) AS number_assigned 
            FROM farmer 
            LEFT JOIN pigs ON pigs.assigned_farmer = farmer.farmer_name 
            WHERE farmer.user_id = :user_id
            GROUP BY farmer.farmer_name;";
        }

        if (isset($_GET['farmer_id'])) {
            $farmer_id = $_GET['farmer_id'];
            $sql = "SELECT * FROM farmer WHERE farmer_id = :farmer_id";
        }




        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($farmer_id)) {
                $stmt->bindParam(':farmer_id', $farmer_id);
            }

            if (isset($user_id)) {
                $stmt->bindParam(':user_id', $user_id);
            }



            $stmt->execute();
            $farmer = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($farmer);
        }



        break;





    case "POST":
        $farmer = json_decode(file_get_contents('php://input'));

        $sql = "INSERT INTO farmer (farmer_id, farmer_name, created_at, user_id) 
                VALUES (:farmer_id, :farmer_name, :created_at, :user_id)";

        $stmt = $conn->prepare($sql);

        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':farmer_id', $farmer->farmer_id);
        $stmt->bindParam(':farmer_name', $farmer->farmer_name);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':user_id', $farmer->user_id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Farmer inserted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Failed to insert farmer"
            ];
        }

        echo json_encode($response);
        break;

    case "PUT":
        $farmer = json_decode(file_get_contents('php://input'));

        $sql = "UPDATE farmer
            SET 
                farmer_name = :farmer_name
            WHERE
                farmer_id = :farmer_id";

        $stmt = $conn->prepare($sql);

        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':farmer_name', $farmer->farmer_name);

        $stmt->bindParam(':farmer_id', $farmer->farmer_id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Farmer information updated successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Failed to update farmer information"
            ];
        }

        echo json_encode($response);
        break;

    case "DELETE":
        $farmer = json_decode(file_get_contents('php://input'));
        $sql = "DELETE FROM farmer WHERE farmer_id = :farmer_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':farmer_id', $farmer->farmer_id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "farmer_id deleted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "farmer_id delete failed"
            ];
        }

        echo json_encode($response);
        break;
}
