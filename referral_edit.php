<?php
    include_once("db.php"); 
    include_once("referral.php"); 

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $db = new Database();
        $referral = new Referral($db);
        $referralData = $referral->read($id);

        if ($referralData) {
        } else {
            echo "Record not found.";
        }
    } else {
        echo "Referral ID not provided.";
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $data = [
            'EmployeeID' => $_POST['EmployeeID'],
            'ReferrerName' => $_POST['ReferrerName'],
            'ReferrerEmail' => $_POST['ReferrerEmail'],
            'ReferralStatusID' => $_POST['ReferralStatusID'],
            'PerformanceID' => $_POST['PerformanceID'],
        ];

        $db = new Database();
        $referral = new Referral($db);

        if ($referral->update($id, $data)) {
            echo "Record updated successfully.";
        } else {
            echo "Failed to update the record.";
        }
        }
?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="styles.css">
        <title>Edit Referral Record</title>
    </head>
    <body>
        <?php include('header.html'); ?>
        <?php include('navbar.php'); ?>

        <div class="content">
        <h2>Edit Referral Record</h2>
        <form action="" method="post">
            <label for="EmployeeID">Select Employee:</label>
            <select name="EmployeeID" required>
                <?php
                    $db = new PDO("mysql:host=localhost;dbname=mydb", "root", "0622000722");
                    $sql = "SELECT idemployees, CONCAT(first_name, ' ', last_name) as employee_name FROM employees";
                    $stmtEmployees = $db->query($sql);

                    while ($row = $stmtEmployees->fetch(PDO::FETCH_ASSOC)) {
                        $selected = ($row['idemployees'] == $referralData['EmployeeID']) ? 'selected' : '';
                        echo "<option value='{$row['idemployees']}' $selected>{$row['employee_name']}</option>";
                    }
                ?>
            </select>

        <label for="ReferrerName">Referrer Name:</label>
        <input type="text" name="ReferrerName" value="<?php echo $referralData['ReferrerName']; ?>" required>

        <label for="ReferrerEmail">Referrer Email:</label>
        <input type="email" name="ReferrerEmail" value="<?php echo $referralData['ReferrerEmail']; ?>" required>

        <label for="PerformanceID">Select Performance:</label>
        <select name="PerformanceID" required>
            <?php
                $db = new PDO("mysql:host=localhost;dbname=mydb", "root", "0622000722");
                $sqlPerformance = "SELECT performance_id, performance_name FROM performance";
                $stmtPerformance = $db->query($sqlPerformance);

                while ($row = $stmtPerformance->fetch(PDO::FETCH_ASSOC)) {
                    $selected = ($row['performance_id'] == $referralData['PerformanceID']) ? 'selected' : '';
                    echo "<option value='{$row['performance_id']}' $selected>{$row['performance_name']}</option>";
                }
            ?>
        </select>

        <label for="ReferralStatusID">Select Status:</label>
        <select name="ReferralStatusID" required>
            <?php
                $sqlStatus = "SELECT StatusID, StatusName FROM referralstatus";
                $stmtStatus = $db->query($sqlStatus);

                while ($row = $stmtStatus->fetch(PDO::FETCH_ASSOC)) {
                    $selected = ($row['StatusID'] == $referralData['ReferralStatusID']) ? 'selected' : '';
                    echo "<option value='{$row['StatusID']}' $selected>{$row['StatusName']}</option>";
                }
            ?>
        </select>

                <input type="submit" value="Update Referral">
            </form>
        </div>
    </body>
</html>
