<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        // if (isset($_GET['field_id'])) {
        //     $user_id = $_GET['user_id'];
        //     $field_id = $_GET['field_id'];
        //     $sql = "SELECT crops.harvesting_cal, crops.crops_name, field.field_size, field.field_name, crops.obnotes FROM schedule 
        //     INNER JOIN field ON field.field_id = schedule.field_id 
        //     INNER JOIN crops ON crops.crops_id = schedule.crops_id WHERE schedule.field_id = :field_id AND schedule.user_id = :user_id";
        // }

        if (isset($_GET['field_id'])) {
            $user_id = $_GET['user_id'];
            $field_id = $_GET['field_id'];
            $sql = "SELECT schedule.activity, crops.harvesting_cal, schedule.actual_start_date, schedule.actual_end_date, crops.crops_name, field.field_size, field.field_name, crops.obnotes FROM schedule 
            INNER JOIN field ON field.field_id = schedule.field_id 
            INNER JOIN crops ON crops.crops_id = schedule.crops_id WHERE schedule.field_id = :field_id AND schedule.user_id = :user_id";
        }



        if (isset($_GET['user_id_field']) && isset($_GET['field_id_field'])) {

            $user_id_field = $_GET['user_id_field'];
            $field_id_field = $_GET['field_id_field'];

            $sql2 = "SELECT * FROM field WHERE field_id = :field_id_field AND user_id = :user_id_field";
        }



        if (isset($sql)) {
            $stmt = $conn->prepare($sql);


            if (isset($user_id) && isset($field_id)) {
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':field_id', $field_id);
            }



            $stmt->execute();
            $crops = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($crops);
        }

        if (isset($sql2)) {
            $stmt = $conn->prepare($sql2);

            if (isset($user_id_field) && isset($field_id_field)) {
                $stmt->bindParam(':user_id_field', $user_id_field);
                $stmt->bindParam(':field_id_field', $field_id_field);
            }

            $stmt->execute();
            $field = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($field);
        }



        break;
}
