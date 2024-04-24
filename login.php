<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":
        $username = $_GET['username'];
        $password = $_GET['password'];

        $sql = "SELECT * FROM users WHERE username = :username AND password = :password";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($users) {

            $response = [
                "status" => "success",
                "message" => "User login successful"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Failed to login "
            ];
        }


        echo json_encode($users);

        break;


    case "POST":
        $account = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO users (user_id, name, username, password, account_type, created_at) 
            VALUES (null, :name, :username, :password, :account_type, :created_at)";

        $stmt = $conn->prepare($sql);
        $created_at = date('Y-m-d');
        $stmt->bindParam(':name', $account->name);
        $stmt->bindParam(':username', $account->username);
        $stmt->bindParam(':password', $account->password);
        $stmt->bindParam(':account_type', $account->account_type);
        $stmt->bindParam(':created_at', $created_at);



        // $stmt->bindParam(':created_at', $created_at);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Account created successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Account creation failed"
            ];
        }

        echo json_encode($response);
        break;
}
