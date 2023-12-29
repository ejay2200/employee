<?php
include_once("db.php"); 
include_once("referral.php"); 

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = $_GET['id']; 
    $confirmationMessage = "Are you sure you want to delete this record?";

    echo "<script>
            var confirmDelete = confirm('$confirmationMessage');
            if (confirmDelete) {
                window.location.href = 'delete_record.php?id=$id';
            } else {
                window.location.href = 'index.php'; 
            }
          </script>";
}
?>