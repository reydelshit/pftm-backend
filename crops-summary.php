<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":

        if (isset($_GET['user_id'])) {
            $user_id = $_GET['user_id'];
            $sql = "SELECT 
              COALESCE(suitable.suitable_id, 'N/A') AS suitable_id,
            COALESCE(crops.crops_id, 'N/A') AS crops_id,
            COALESCE(schedule.status, 'N/A') AS status,
            COALESCE(schedule.activity, 'N/A') AS activity,
            COALESCE(suitable.suitable_month, 'N/A') AS suitable_month,
            COALESCE(crops.crops_name, 'N/A') AS crops_name,
            COALESCE(field.field_id, 'N/A') AS field_id,
            COALESCE(field.field_name, 'N/A') AS field_name,
            COALESCE(suitable.suitability, 'N/A') AS suitability
        FROM 
            crops 
        LEFT JOIN 
            suitable ON crops.crops_id = suitable.suitable_crops_id 
        LEFT JOIN 
            schedule ON schedule.crops_id = crops.crops_id 
        INNER JOIN 
            field ON field.field_id = schedule.field_id 
        WHERE 
            crops.user_id = :user_id  GROUP BY suitable.suitable_id";
        }


        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($user_id)) {
                $stmt->bindParam(':user_id', $user_id);
            }


            $stmt->execute();
            $crops = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($crops);
        }


        break;
}
