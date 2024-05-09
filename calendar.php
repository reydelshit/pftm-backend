<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":

        $sql = "SELECT calendar_id AS id, 
                CONCAT(COALESCE(calendar_title, 'No Title'), ' - ', pigs.assigned_farmer) AS title, 
                    start, 
                    end, 
                    allDay, 
                    pigs.assigned_farmer 
                FROM calendar 
                INNER JOIN pigs ON pigs.pig_tag = calendar.pig_tag;";


        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $calendar = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($calendar);
        }


        break;

    case "POST":
        $calendar = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO calendar (calendar_id, calendar_title, start, end, allDay, account_id, pig_tag) 
                VALUES (null, :calendar_title, :start, :end, :allDay, :account_id, :pig_tag)";
        $stmt = $conn->prepare($sql);
        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':calendar_title', $calendar->calendar_title);
        $stmt->bindParam(':start', $calendar->start);
        $stmt->bindParam(':end', $calendar->end);
        $stmt->bindParam(':allDay', $calendar->allDay);
        $stmt->bindParam(':account_id', $calendar->account_id);
        $stmt->bindParam(':pig_tag', $calendar->pig_tag);


        if ($stmt->execute()) {

            $event_id = $conn->lastInsertId();
            $sql2 = "INSERT INTO sched (sched_id, sched_name, category, pig_id, sched_date, created_at, user_id, event_id) 
                    VALUES (:sched_id, :sched_name, :category, :pig_id, :sched_date, :created_at, :user_id, :event_id)";

            $stmt2 = $conn->prepare($sql2);

            $stmt2->bindParam(':sched_id', $calendar->sched_id);
            $stmt2->bindParam(':sched_name', $calendar->sched_name);
            $stmt2->bindParam(':category', $calendar->category);
            $stmt2->bindParam(':pig_id', $calendar->pig_id);
            $stmt2->bindParam(':sched_date', $calendar->sched_date);
            $stmt2->bindParam(':created_at', $created_at);
            $stmt2->bindParam(':user_id', $calendar->user_id);
            $stmt2->bindParam(':event_id', $event_id);


            $stmt2->execute();


            $response = [
                "status" => "success",
                "message" => "calendar created successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "calendar creation failed"
            ];
        }

        echo json_encode($response);
        break;

    case "PUT":
        $calendar = json_decode(file_get_contents('php://input'));
        $sql = "UPDATE calendar SET calendar_title= :calendar_title, start=:start, end=:end, allDay=:allDay 
                WHERE calendar_id = :calendar_id";
        $stmt = $conn->prepare($sql);
        $updated_at = date('Y-m-d');
        $stmt->bindParam(':calendar_id', $calendar->calendar_id);
        $stmt->bindParam(':calendar_title', $calendar->calendar_title);
        $stmt->bindParam(':start', $calendar->start);
        $stmt->bindParam(':end', $calendar->end);
        $stmt->bindParam(':allDay', $calendar->allDay);

        if ($stmt->execute()) {

            $response = [
                "status" => "success",
                "message" => "User updated successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "User update failed"
            ];
        }

        echo json_encode($response);
        break;

    case "DELETE":
        $sql = "DELETE FROM calendar WHERE calendar_id = :id";
        $path = explode('/', $_SERVER['REQUEST_URI']);

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $path[3]);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "User deleted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "User deletion failed"
            ];
        }
}
