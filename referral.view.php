<?php
include_once("db.php");
include_once("referral.php");

$db = new Database();
$connection = $db->getConnection();
$referral = new Referral($db);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referrals</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <?php include('header.html'); ?>
    <?php include('navbar.php'); ?>

    <div class="content">
    <h2>Referrals</h2>
    <table class="orange-theme">
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>Job Position</th>
                <th>Referrer Name</th>
                <th>Referrer Email</th>
                <th>Referral Date</th>
                <th>Performance</th>
                <th>Referral Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
       
            <?php
            $results = $referral->getAll(); 
            foreach ($results as $result) {
            ?>
            <tr>
                <td><?php echo $result['EmployeeID']; ?></td>
                <td><?php echo $result['employee_name']; ?></td>
                <td><?php echo $result['job_category']; ?></td>
                <td><?php echo $result['ReferrerName']; ?></td>
                <td><?php echo $result['ReferrerEmail']; ?></td>
                <td><?php echo $result['ReferralDate']; ?></td>
                <td><?php echo $result['performance_name']; ?></td>
                <td><?php echo $result['StatusName']; ?></td>
                <td>
                    <a href="referral_edit.php?id=<?php echo $result['ReferralID']; ?>">Edit</a>
                    |
                    <a href="referral_delete.php?id=<?php echo $result['ReferralID']; ?>">Delete</a>
                </td>
            </tr>
        <?php } ?>

           
        </tbody>
    </table>
        
    <a class="button-link" href="referral_add.php">Add New Referral</a>

        </div>
    <p></p>
</body>
</html>
