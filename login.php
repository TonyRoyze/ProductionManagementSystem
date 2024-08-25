<?php global $conn;
session_start();
include "./connector.php";
include "./functions.php";

$login = true;
$login_username = "";
$login_pass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["btn-login"])) {
        $login_username = $_POST["login_username"];
        $login_pass = $_POST["login_pass"];
    }

    if (!empty($login_username) && !empty($login_pass)) {
        do {
            $sql = "SELECT * FROM user WHERE user_name = '$login_username' LIMIT 1";

            try {
                $result = $conn->query($sql);
            } catch (Exception $e) {
                $errorMessage = "Invalid Query";
                break;
            }

            if ($result && mysqli_num_rows($result) > 0) {
                $user_data = $result->fetch_assoc();

                if (
                    password_verify($login_pass, $user_data["password"]) ||
                    $user_data["password"] == $login_pass
                ) {
                    $_SESSION["Username"] = $user_data["user_name"];
                    $successMessage = "Logged In Successfully";
                    if ($user_data["user_type"] == "ADMIN") {
                        header("location: /admin/user-dashboard.php");
                        exit();
                    } elseif ($user_data["user_type"] == "MANAGER") {
                        header("location: /manager/order-dashboard.php");
                        exit();
                    } else {
                        header("location: /workstation/order-dashboard.php");
                        exit();
                    }
                } else {
                    $errorMessage = "Username or Password is Incorrect";
                    break;
                }
            }
        } while (false);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prod Manage</title>
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/admin.css">

</head>

<body>
<?php echo "
<div class='wrapper login'>
    <div class='form-box login'>
        <h2>Login</h2>
        <form method='post' class='form'>
            <div class='input-box'>
                <span class='icon'><ion-icon name='mail-outline'></ion-icon></span>
                <input type='text' name='login_username' required>
                <label>User Name</label>
            </div>
            <div class='input-box'>
                <span class='icon'><ion-icon name='lock-closed-outline'></ion-icon></span>
                <input type='password' name='login_pass' required>
                <label>Password</label>
            </div>
            <input type='submit' class='btn' name='btn-login' value='Submit'>
        </form>" .
    (empty($errorMessage) ? "" : "<p class='err-msg'>$errorMessage</p>") .
    "</div>
</div>
"; ?>
<!-- <script src="login.js"></script> -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
