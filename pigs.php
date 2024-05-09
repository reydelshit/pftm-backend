<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        if (isset($_GET['user_id'])) {
            $user_id = $_GET['user_id'];
            $sql = "SELECT pigs.*, buffs.buff_name, buffs.buff_type FROM pigs LEFT JOIN buffs ON buffs.buff_id = pigs.buff_id WHERE pigs.user_id = :user_id ORDER BY pigs.created_at DESC";
        }


        if (isset($_GET['user_id']) && isset($_GET['latest_eartag'])) {
            $user_id = $_GET['user_id'];
            $sql = "SELECT 
                    MAX(pig_tag) AS latest_eartag
                FROM pigs
                WHERE user_id = :user_id";
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

            if (isset($_GET['user_id']) && isset($_GET['latest_eartag'])) {
                $stmt->bindParam(':user_id', $user_id);
            }


            $stmt->execute();
            $pigs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($pigs);
        }


        break;




    case "POST":
        $pig = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO pigs (pig_id, pig_tag, assigned_farmer, building, pen, short_desc, created_at, user_id, pig_type, date_breed, farrowing_date, buff_id) 
                VALUES (:pig_id, :pig_tag, :assigned_farmer, :building, :pen, :short_desc, :created_at, :user_id, :pig_type, :date_breed, :farrowing_date, :buff_id)";

        $stmt = $conn->prepare($sql);

        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':pig_id', $pig->pig_id);
        $stmt->bindParam(':pig_tag', $pig->pig_tag);
        $stmt->bindParam(':assigned_farmer', $pig->assigned_farmer);
        $stmt->bindParam(':building', $pig->building);
        $stmt->bindParam(':pen', $pig->pen);
        $stmt->bindParam(':short_desc', $pig->short_desc);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':user_id', $pig->user_id);
        $stmt->bindParam(':pig_type', $pig->pig_type);
        $stmt->bindParam(':date_breed', $pig->date_breed);
        $stmt->bindParam(':buff_id', $pig->buff_id);


        if (!empty($pig->date_breed)) {
            $date_breed = new DateTime($pig->date_breed);
            $date_breed->add(new DateInterval('P112D'));
            $farrowing_date = $date_breed->format('Y-m-d');
            $stmt->bindParam(':farrowing_date', $farrowing_date);
        } else {
            $stmt->bindValue(':farrowing_date', null, PDO::PARAM_NULL);
        }



        // Execute statement
        if ($stmt->execute()) {

            $pig_id = $conn->lastInsertId();
            $sql2 = "INSERT INTO sched (sched_id, sched_name, category, pig_id, sched_date, created_at, user_id) 
            VALUES (NULL, :sched_name, :category, :pig_id, :sched_date, :created_at, :user_id)";

            $stmt2 = $conn->prepare($sql2);

            $sched_name = "Breeding";
            $category = "Breeding";

            $created_ats = date('Y-m-d H:i:s');
            $stmt2->bindParam(':sched_name', $sched_name);
            $stmt2->bindParam(':category', $category);
            $stmt2->bindParam(':pig_id', $pig->pig_tag);
            $stmt2->bindParam(':sched_date', $pig->date_breed);
            $stmt2->bindParam(':created_at', $created_ats);
            $stmt2->bindParam(':user_id', $pig->user_id);



            if ($stmt2->execute()) {
                $response = [
                    "status" => "success",
                    "message" => "Pig breeding successfully"
                ];
            } else {
                $response = [
                    "status" => "error",
                    "message" => "Failed to insert pig breeding"
                ];
            }



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
        $sql = "UPDATE pigs
        SET 
            pig_tag = :pig_tag,
            assigned_farmer = :assigned_farmer,
            building = :building,
            pen = :pen,
            short_desc = :short_desc,
            user_id = :user_id,
            pig_type = :pig_type
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
        $stmt->bindParam(':pig_type', $pigs->pig_type);

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
