<?php
include_once("db.php");
include_once("referral.php");

$db = new Database();
$connection = $db->getConnection();
$referral = new Referral($db);

$referrer = "Welcome <br> to <br> TalentSprint";
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Employee Referral Management</title>
        <link rel="stylesheet" type="text/css" href="styles.css">
    </head>
    <body>
        <?php include('header.html'); ?>
        <?php include('navbar.php'); ?>

        <div class="content-1">
            <h2><?php echo $referrer; ?></h2>
        </div>

        <?php 
        ?>
    </body>
</html>