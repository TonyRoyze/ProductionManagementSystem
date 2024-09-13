# CS 2001 Group Project (45)
## Database Structure
Creating the database

    CREATE DATABASE prodmanage;
    USE prodmanage;

Part Table

    CREATE TABLE part (
    part_id INT NOT NULL AUTO_INCREMENT,
    part_name VARCHAR(20) NOT NULL,
    part_desc TEXT,
    CONSTRAINT PK_part_id PRIMARY KEY (part_id)
    );

Workstation Table

    CREATE TABLE workstation (
    workstation_id INT NOT NULL AUTO_INCREMENT,
    workstation_capacity INT NOT NULL,
    is_active INT NOT NULL,
    part_id INT NOT NULL,
    CONSTRAINT PK_workstation_id PRIMARY KEY (workstation_id),
    CONSTRAINT FK_part_id_workstation_table FOREIGN KEY (part_id) REFERENCES part (part_id)
    );

User Table

    CREATE TABLE user (
    user_id INT NOT NULL AUTO_INCREMENT,
    user_name VARCHAR(20) NOT NULL,
    password VARCHAR(100) NOT NULL,
    user_type VARCHAR(20) NOT NULL,
    workstation_id INT,
    CONSTRAINT PK_user_id PRIMARY KEY (user_id),
    CONSTRAINT U_user_name UNIQUE (user_name),
    CONSTRAINT FK_workstation_id_user_table FOREIGN KEY (workstation_id) REFERENCES workstation (workstation_id)
    );

Order Table

    CREATE TABLE orders (
    order_id INT NOT NULL AUTO_INCREMENT,
    part_id INT NOT NULL,
    quantity INT NOT NULL,
    workstation_id INT NOT NULL,
    order_status INT NOT NULL,
    CONSTRAINT PK_order_id PRIMARY KEY (order_id),
    CONSTRAINT FK_part_id_order_table FOREIGN KEY (part_id) REFERENCES part (part_id),
    CONSTRAINT FK_workstation_id_order_table FOREIGN KEY (workstation_id) REFERENCES workstation (workstation_id)
    );
## Database Connection
    <?php
    $dbServerName = "localhost";
    $dbUserName = "root";
    $dbPassword = "";
    $dbName = "prodmanage";
    
    $conn = mysqli_connect($dbServerName, $dbUserName, $dbPassword, $dbName);
    
    if ($conn->connect_error) {
        die("Connection Failed" . $conn->connect_error);
    }
    
The `connector.php` file contains the code for the database connection which is included in the files that require database manipulations.

## Login Page
Validates the username and password and redirect to the relavent page based on the user type.

    if (password_verify($login_pass,$user_data["password"]) || $user_data["password"] == $login_pass){
	    $_SESSION["Username"] = $user_data["user_name"];
	    $successMessage = "Logged In Successfully";
	    if ($user_data["user_type"] == "ADMIN") {
		    header("location: /admin/user-dashboard.php");
		    exit();
		} elseif ($user_data["user_type"] == "MANAGER") {
			header("location: /manager/order-dashboard.php");
			exit();
		} else {
			$sql = "SELECT is_active FROM workstation WHERE workstation_id = $user_data[workstation_id]";
			try {
				$result = $conn->query($sql);
            } catch (Exception $e) {
	            $errorMessage = "Invalid Query";
            }
            $workstation_data = $result->fetch_assoc();
            header("location: /workstation/workstation-dashboard.php?workstation_id=$user_data[workstation_id]&is_active=$workstation_data[is_active]");
            exit();
        }
    } else {
	    $errorMessage = "Username or Password is Incorrect";
	    break;
	}
The parameter `is_active` is queried `$sql = "SELECT is_active FROM workstation WHERE workstation_id = $user_data[workstation_id]";` and passed in the url `...?workstation_id=$user_data[workstation_id]&is_active=$workstation_data[is_active]` as it is need to show the status of the workstation.

## Check Login Function
    function checkLogin($conn)
    {
        if (isset($_SESSION["Username"])) {
            $username = $_SESSION["Username"];
    
            $sql = "SELECT * FROM user WHERE user_name='$username' LIMIT 1";
            $result = $conn->query($sql);
            if ($result && mysqli_num_rows($result) > 0) {
                return $result->fetch_assoc();
            }
        }
    
        header("location: /login.php");
        exit();
    }
This function checks if the username is set to the session thereby preventing access to pages without logging in.

## Part Dashboard
### Search Function

    <form method="post">
    <input placeholder="Search" type="search" class="input" name="search" value="<?php echo isset($_POST["search"]) ? $_POST["search"]: ""; ?>">
    </form>
Filtering the data based on whether the  `part_name` or `part_desc` contains the `$searchTerm`.

    $searchTerm = isset($_POST["search"]) ? $_POST["search"] : "";
    $sql = "SELECT * FROM part WHERE part_name LIKE '%$searchTerm%' OR part_desc LIKE '%$searchTerm%'";

> The  `LIKE`  operator is used in a  `WHERE`  clause to search for a specified pattern in a column.
> `SELECT * FROM Customers WHERE city LIKE  '%L%';` returns all customers from a city that _contains_ the letter 'L':

### Deleting a Part
Since we shouldn't allow deletion of parts that are assigned to a workstation we need to check for this first. `SELECT COUNT(*) as count FROM workstation WHERE part_id = $part_id;` and if so 
the user is alerted.
 
    if ($row["count"] > 0) {
	    echo "<script>alert('Cannot delete part because it is assigned to a workstations.');
	    window.location.href = 'part-dashboard.php';</script>";
    }
 
## User Dashboard
### Restricting duplicate usernames
First we have to check if the entered username already exists in the database, `SELECT * FROM user WHERE user_name = '$username'` if the username exists we display that the username is already taken, 

    if ($result->num_rows > 0) {
	    $errorMessage = "Username is already taken";
    }
   
## Workstation Dashboard (Admin)
Since the data required to be displayed is in 3 different tables that are in the **3rd Normal Form**, they need to be joined.

| Workstation Name | Workstation Capacity | Is Active | Part         |
|------------------|----------------------|-----------|--------------|
| Workstation2     | 30                   | Yes       | Engine       |

The Workstation Name column-> From user table
The Workstation Capacity column -> From workstation table
The Is Active column -> From workstation table
The Part column -> From part table

Therefore the 3 tables should be joined by matching up the correct foreign keys.

### Workstation and User tables

user table
| user_id | user_name    | password | user_type   | workstation_id |
|---------|--------------|----------|-------------|----------------|
| 1       | uoc	| uoc      | ADMIN | NULL             |
| 2       | Manager1 | man1      | MANAGER | NULL              |
| 4       | Workstation2 | ws2      | WORKSTATION | 2              |
| 5       | Workstation3 | ws3      | WORKSTATION | 3              |

workstation table

| workstation_id | workstation_capacity | is_active | part_id |
|----------------|----------------------|-----------|---------|
| 2              | 30                   | 1         | 1       |
| 3              | 10                   | 0         | 2       |

Combined

    SELECT * FROM part JOIN workstation ON user.workstation_id = workstation.workstation_id;

| user_id | user_name    | password | user_type   | workstation_id | workstation_id | workstation_capacity | is_active | part_id |
|---------|--------------|----------|-------------|----------------|----------------|----------------------|-----------|---------|
| 4       | Workstation2 | ws1      | WORKSTATION | 2              | 2              | 30                   | 1         | 1       |
| 5       | Workstation3 | ws2      | WORKSTATION | 3              | 3              | 10                   | 0         | 2       |

### Workstation and Part tables

workstation table

| workstation_id | workstation_capacity | is_active | part_id |
|----------------|----------------------|-----------|---------|
| 2              | 30                   | 1         | 1       |
| 3              | 10                   | 0         | 2       |

part table

| part_id | part_name    | part_desc       |
|---------|--------------|-----------------|
| 1       | Engine       | The Engine      |
| 2       | Transmission | Th Transmission |

Combined

    SELECT * FROM part JOIN workstation ON user.workstation_id = workstation.workstation_id;

| workstation_id | workstation_capacity | is_active | part_id | part_id | part_name    | part_desc       |
|----------------|----------------------|-----------|---------|---------|--------------|-----------------|
| 2              | 30                   | 1         | 1       | 1       | Engine       | The Engine      |
| 3              | 10                   | 0         | 2       | 2       | Transmission | The Transmission |

### Combining all three tables and selecting only the required columns

    $sql = "SELECT user.user_name, password, " .
	    "workstation.workstation_id, workstation_capacity, is_active, part_name " .
	    "FROM user JOIN workstation ON user.workstation_id = workstation.workstation_id " .
	    "JOIN part ON part.part_id = workstation.part_id WHERE user_name LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchParam = "%$searchQuery%";
    $stmt->bind_param("s", $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
 The `LIKE` operator is used for searching.
 
### Creating a Workstation
Since a workstation is also a user we must include in both tables.

Create Workstation 

    INSERT INTO workstation (workstation_capacity, workstation_status, is_active, part_id) 
    VALUES ('$workstation_capacity', 0, 0, $part_id);
	
Get the last `workstation_id`

    SELECT workstation_id FROM workstation 
    ORDER BY workstation_id DESC LIMIT 1;

Create User and assign the correct `workstation_id`

    INSERT INTO user (user_name, user_type, password, workstation_id) 
    VALUES ('$workstation_name', 'WORKSTATION' , '$pwd', $workstation_id[workstation_id]);

### Deleting a Workstation
First we have to check if there are orders that haven't been shipped yet assigned to the workstation.

    SELECT COUNT(*) as count FROM orders 
    WHERE workstation_id = $workstation_id 
    AND order_status < 3
  
 And show an alert to the user.

    if ($row["count"] > 0) {
	    echo "<script>alert('Cannot delete workstation because there are orders bieng processed.');
	    window.location.href = 'part-dashboard.php'</script>";
     }

Next we delete the orders that have that `workstation_id`, then the user with that `workstation_id` and finally the workstation.

    $sql = "DELETE FROM orders WHERE workstation_id = '$workstation_id'";
    $conn->query($sql);
    
    $sql = "DELETE FROM user WHERE workstation_id = '$workstation_id'";
    $conn->query($sql);
    
    $sql = "DELETE FROM workstation WHERE workstation_id = '$workstation_id'";
    $conn->query($sql);
    
    header("location: ./workstation-dashboard.php");
    exit();


