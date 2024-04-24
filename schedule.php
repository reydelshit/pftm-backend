<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        if (isset($_GET['user_id'])) {
            $user_id = $_GET['user_id'];
            $sql = "SELECT schedule.*, crops.crops_name, field.field_name
            FROM schedule 
            RIGHT JOIN crops ON crops.crops_id = schedule.crops_id 
            RIGHT JOIN field ON field.field_id = schedule.field_id
            WHERE schedule.user_id = :user_id";
        }

        if (isset($_GET['field_id'])) {
            $field_id = $_GET['field_id'];
            $sql = "SELECT * FROM field WHERE field_id = :field_id";
        }


        // if (!isset($_GET['crops_id']) && !isset($_GET['user_id'])) {
        //     $sql = "SELECT * FROM crops ORDER BY crops_id DESC ";
        // }


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
        $schedule = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO schedule (schedule_id, field_id, crops_id, activity, scheduled_date, status, actual_start_date, actual_end_date, user_id) VALUES (:schedule_id, :field_id, :crops_id, :activity, :scheduled_date, :status, :actual_start_date, :actual_end_date, :user_id)";

        $stmt = $conn->prepare($sql);
        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':schedule_id', $schedule->schedule_id);
        $stmt->bindParam(':field_id', $schedule->field_id);
        $stmt->bindParam(':crops_id', $schedule->crops_id);
        $stmt->bindParam(':activity', $schedule->activity);
        $stmt->bindParam(':scheduled_date', $schedule->scheduled_date);
        $stmt->bindParam(':actual_start_date', $schedule->actual_start_date);
        $stmt->bindParam(':actual_end_date', $schedule->actual_end_date);
        $stmt->bindParam(':user_id', $schedule->user_id);
        $stmt->bindParam(':status', $schedule->status);




        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "schedule successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "schedule failed"
            ];
        }

        echo json_encode($response);
        break;

    case "PUT":
        $schedule = json_decode(file_get_contents('php://input'));

        // $sql = "UPDATE schedule 
        // SET field_id = :field_id, crops_id = :crops_id, activity = :activity, 
        //     scheduled_date = :scheduled_date, actual_start_date = :actual_start_date, 
        //     actual_end_date = :actual_end_date, user_id = :user_id 
        // WHERE schedule_id = :schedule_id";

        // $stmt = $conn->prepare($sql);

        // $stmt->bindParam(':schedule_id', $schedule->schedule_id);
        // $stmt->bindParam(':field_id', $schedule->field_id);
        // $stmt->bindParam(':crops_id', $schedule->crops_id);
        // $stmt->bindParam(':activity', $schedule->activity);
        // $stmt->bindParam(':scheduled_date', $schedule->scheduled_date);
        // $stmt->bindParam(':actual_start_date', $schedule->actual_start_date);
        // $stmt->bindParam(':actual_end_date', $schedule->actual_end_date);
        // $stmt->bindParam(':user_id', $schedule->user_id);



        $sql = "UPDATE schedule SET status = :status WHERE schedule_id = :schedule_id AND user_id = :user_id";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':status', $schedule->status);
        $stmt->bindParam(':schedule_id', $schedule->schedule_id);
        $stmt->bindParam(':user_id', $schedule->user_id);


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
        $schedule = json_decode(file_get_contents('php://input'));
        $sql = "DELETE FROM schedule WHERE schedule_id = :schedule_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':schedule_id', $schedule->schedule_id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "schedule deleted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "schedule delete failed"
            ];
        }

        echo json_encode($response);
        break;
}
