<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":

        if (isset($_GET['farrowing'])) {
            $sql = "SELECT DATE_FORMAT(pigs.farrowing_date, '%M') AS name, COUNT(*) AS total
            FROM pigs
            WHERE pigs.farrowing_date IS NOT NULL
            GROUP BY MONTH(pigs.farrowing_date)";

            if (isset($sql)) {
                $stmt = $conn->prepare($sql);

                $stmt->execute();
                $account = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode($account);
            }
        }





        break;
}
