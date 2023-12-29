<?php
    include_once("db.php"); 
    include_once("referral.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $data = [    
        'EmployeeID' => $_POST['EmployeeID'],
        'ReferrerName' => $_POST['ReferrerName'],
        'ReferrerEmail' => $_POST['ReferrerEmail'],
        'PerformanceID' => $_POST['PerformanceID'],
        'ReferralStatusID' => $_POST['ReferralStatusID'],
        ];

        $database = new Database();
        $referral = new Referral($database);
            
        if ($referral->create($data)){
            echo "Record inserted successfully.";
        } else {
            echo "Failed to insert Record.";
        }
    }
?>


<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="styles.css">

        <title>Add New Referral</title>
    </head>
    <body>
        <?php include('header.html'); ?>
        <?php include('navbar.php'); ?>

        <div class="content">
        <h1>Add New Referral</h1>
            <form action="" method="post" class="centered-form">
                <label for="EmployeeID">Select Employee:</label>
                <select name="EmployeeID" required>
                    <?php
                        $db = new PDO("mysql:host=localhost;dbname=mydb", "root", "0622000722");
                        $database = new Database();
                        $referral = new Referral($database);

                        $sql = "SELECT idemployees, CONCAT(first_name, ' ', last_name) as employee_name 
                                FROM employees";
                        $stmt = $db->query($sql);

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$row['idemployees']}'>{$row['employee_name']}</option>";
                        }
                    ?>
                </select>

            <label for="ReferrerName">Referrer Name:</label>
            <input type="text" name="ReferrerName" required>

            <label for="ReferrerEmail">Referrer Email:</label>
            <input type="email" name="ReferrerEmail" required>

            <label for="PerformanceID">Select Performance:</label>
                <select name="PerformanceID" required>
                    <?php
                        $db = new PDO("mysql:host=localhost;dbname=mydb", "root", "0622000722");
                        $database = new Database();
                        $referral = new Referral($database);

                        $sql = "SELECT performance_id, performance_name FROM performance";
                        $stmt = $db->query($sql);

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$row['performance_id']}'>{$row['performance_name']}</option>";
                        }
                    ?>
                </select>

            <label for="ReferralStatusID">Select Status:</label>
                <select name="ReferralStatusID" required>
                <?php
                    $db = new PDO("mysql:host=localhost;dbname=mydb", "root", "0622000722");
                    $database = new Database();
                    $referral = new Referral($database);

                    $sql = "SELECT StatusID, StatusName FROM referralstatus";
                    $stmt = $db->query($sql);

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['StatusID']}'>{$row['StatusName']}</option>";
                    }
                ?>
            </select>

            <input type="submit" value="Submit Evaluation">
        </form>
    </body>
</html>