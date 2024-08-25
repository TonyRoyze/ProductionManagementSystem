<?php global $conn;
session_start();
include "../connector.php";
include "../functions.php";

$user_data = checkLogin($conn);

$errorMessage = "";
$details = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!isset($_GET["username"])) {
        header("location: ./user-dashboard.php");
        exit();
    }

    $username = $_GET["username"];

    $sql = "SELECT * FROM user WHERE user_name='$username'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if (!$row) {
        header("location: ./user-dashboard.php");
        exit();
    }

    $name = $row["user_name"];
    $user_type = $row["user_type"];
    $pass = "";
    $repass = "";
} else {
    $username = $_POST["username"];
    $name = $_POST["name"];
    $user_type = $_POST["user_type"];
    $pass = $_POST["pass"];
    $repass = $_POST["repass"];

    if ($pass == $repass) {
        do {
            $pwd = password_hash($pass, PASSWORD_DEFAULT);

            $sql =
                /** @lang text */
                "UPDATE user " .
                "SET user_name = '$name', user_type = '$user_type', password = '$pwd' " .
                "WHERE user_name = '$username'";

            try {
                $result = $conn->query($sql);
            } catch (Exception $e) {
                $errorMessage = "Invalid query";
                $details = $conn->error;
                break;
            }

            $successMessage = "User Updated Successfully";

            header("location: ./user-dashboard.php");
            exit();
        } while (false);
    } else {
        $errorMessage = "Passwords don't match";
    }
}
?>

<?php
include "./admin-header.php";
echo "
    <div class='wrapper medium'>
        <div class='form-box'>
            <h2>Edit User</h2>
            <form method='post'>
                <input type='hidden' name='username' value='$username'>
                <div class='form-container medium'>
                    <div class='input-box'>
                        <input type='text' name='name' value='$name' required>
                        <label>Username</label>
                    </div>
                    <div class='input-box'>
                        <select name='user_type' required>
                            <option value='ADMIN'" .
    ($user_type == "ADMIN" ? " selected" : "") .
    ">Admin</option>
                            <option value='MANAGER' " .
    ($user_type == "MANAGER" ? " selected" : "") .
    ">Manager</option>
                        </select>
                    </div>
                    <div class='input-box'>
                        <input type='password' name='pass' value='$pass' required>
                        <label>Password</label>
                    </div>
                    <div class='input-box'>
                        <input type='password' name='repass' value='$repass' required>
                        <label>Re-enter Password</label>
                    </div>
                </div>
                <div class='footer'>
                    <button type='submit' class='btn'>Update</button>
                    <a class='btn' href='user-dashboard.php'>Cancel</a>
                </div>
            </form>" .
    (empty($errorMessage)
        ? ""
        : "<p title='$details' class='err-msg'>$errorMessage</p>") .
    "</div>
    </div>
";


?>
