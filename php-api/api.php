<?php

    header("Content-Type: application/json");
    include 'db.php';

    $request_method = $_SERVER["REQUEST_METHOD"];

    switch ($request_method) {
        case 'GET':
            if (!empty($_GET["id"])) {
                $id = intval($_GET["id"]);
                get_user($id);
            } else {
                get_users();
            }
            break;

        case 'POST':
            create_user();
            break;

        case 'PUT':
            $id = intval($_GET["id"]);
            update_user($id);
            break;

        case 'DELETE':
            $id = intval($_GET["id"]);
            delete_user($id);
            break;

        default:
            header("HTTP/1.0 405 Method Not Allowed");
            break;
    }

    // RESTful API functions for performing the CRUD (Create, Read, Update, Delete) Operations on database:
    function get_users() {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM users");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($users);
    }

    function get_user($id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($user);
    }

    function create_user() {
        global $conn;
        $data = json_decode(file_get_contents("php://input"));
        $name = $data->name;
        $email = $data->email;

        $stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        if ($stmt->execute([$name, $email])) {
            echo json_encode(["message" => "User  created successfully."]);
        } else {
            echo json_encode(["message" => "Failed to create user."]);
        }
    }

    function update_user($id) {
        global $conn;
        $data = json_decode(file_get_contents("php://input"));
        $name = $data->name;
        $email = $data->email;

        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        if ($stmt->execute([$name, $email, $id])) {
            echo json_encode(["message" => "User  updated successfully."]);
        } else {
            echo json_encode(["message" => "Failed to update user."]);
        }
    }

    function delete_user($id) {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$id])) {
            echo json_encode(["message" => "User  deleted successfully."]);
        } else {
            echo json_encode(["message" => "Failed to delete user."]);
        }
    }

?>