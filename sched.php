<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        if (isset($_GET['user_id'])) {
            $user_id = $_GET['user_id'];
            $sql = "SELECT sched.*, pigs.assigned_farmer FROM sched INNER JOIN pigs ON pigs.pig_tag = sched.pig_id WHERE sched.user_id =  :user_id";
        }

        if (isset($_GET['sched_id'])) {
            $sched_id = $_GET['sched_id'];
            $sql = "SELECT * FROM sched WHERE sched_id = :sched_id";
        }




        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($sched_id)) {
                $stmt->bindParam(':sched_id', $sched_id);
            }

            if (isset($user_id)) {
                $stmt->bindParam(':user_id', $user_id);
            }



            $stmt->execute();
            $sched = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($sched);
        }



        break;





    case "POST":
        $schedule = json_decode(file_get_contents('php://input'));

        $sql = "INSERT INTO sched (sched_id, sched_name, category, pig_id, sched_date, created_at, user_id) 
                VALUES (:sched_id, :sched_name, :category, :pig_id, :sched_date, :created_at, :user_id)";

        $stmt = $conn->prepare($sql);

        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':sched_id', $schedule->sched_id);
        $stmt->bindParam(':sched_name', $schedule->sched_name);
        $stmt->bindParam(':category', $schedule->category);
        $stmt->bindParam(':pig_id', $schedule->pig_id);
        $stmt->bindParam(':sched_date', $schedule->sched_date);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':user_id', $schedule->user_id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Schedule inserted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Failed to insert schedule"
            ];
        }

        echo json_encode($response);
        break;

    case "PUT":
        $schedule = json_decode(file_get_contents('php://input'));

        $sql = "UPDATE sched 
        SET 
            sched_name = :sched_name,
            category = :category,
            pig_id = :pig_id,
            sched_date = :sched_date,
            user_id = :user_id
        WHERE
            sched_id = :sched_id";

        $stmt = $conn->prepare($sql);

        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':sched_name', $schedule->sched_name);
        $stmt->bindParam(':category', $schedule->category);
        $stmt->bindParam(':pig_id', $schedule->pig_id);
        $stmt->bindParam(':sched_date', $schedule->sched_date);
        $stmt->bindParam(':user_id', $schedule->user_id);
        $stmt->bindParam(':sched_id', $schedule->sched_id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Schedule information updated successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Failed to update schedule information"
            ];
        }
        echo json_encode($response);
        break;

    case "DELETE":
        $sched = json_decode(file_get_contents('php://input'));
        $sql = "DELETE FROM sched WHERE sched_id = :sched_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':sched_id', $sched->sched_id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "sched_id deleted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "sched_id delete failed"
            ];
        }

        echo json_encode($response);
        break;
}
