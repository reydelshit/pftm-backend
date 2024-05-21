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
            c.calendar_id,
            CONCAT(c.calendar_title, ' - is assigned to ', p.assigned_farmer ) AS calendar_title,
            c.start AS NOTIFICATION_DATE,
            c.pig_tag,
            CASE 
                WHEN DATEDIFF(c.start, CURDATE()) < 0 THEN 'passed'
                WHEN DATEDIFF(c.start, CURDATE()) = 0 THEN 'today'
                WHEN DATEDIFF(c.start, CURDATE()) = 1 THEN 'tomorrow'
                WHEN DATEDIFF(c.start, CURDATE()) BETWEEN 2 AND 7 THEN '7 days'
            END AS remarks
        FROM 
            calendar c
        LEFT JOIN 
            pigs p ON c.pig_tag = p.pig_tag
        WHERE 
            DATEDIFF(c.start, CURDATE()) <= 7 
            AND c.account_id = :user_id;
        ";
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
