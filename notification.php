<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        if (isset($_GET['user_id'])) {
            $user_id = $_GET['user_id'];
            $sql = "SELECT farrowing_date, pig_tag, date_breed, 
                CASE
                    WHEN DATEDIFF(farrowing_date, CURDATE()) = 0 THEN 'today'
                    WHEN DATEDIFF(farrowing_date, CURDATE()) = 1 THEN 'tomorrow'
                    WHEN DATEDIFF(farrowing_date, CURDATE()) BETWEEN 2 AND 7 THEN '7 days'
                END AS remarks
            FROM pigs
            WHERE DATEDIFF(farrowing_date, CURDATE()) >= 0 AND DATEDIFF(farrowing_date, CURDATE()) <= 7 AND user_id = :user_id";
        }


        if (isset($sql)) {
            $stmt = $conn->prepare($sql);


            if (isset($user_id)) {
                $stmt->bindParam(':user_id', $user_id);
            }



            $stmt->execute();
            $notif = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($notif);
        }



        break;
}
