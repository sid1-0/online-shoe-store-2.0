<?php
// Check if id is numeric
if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
} else {
    header('location:list_shoes_cat.php?msg=1');
    exit; // Stop further execution
}

require_once 'connection.php';

// Prepare and bind the DELETE statement
$sql = "DELETE FROM shoe_categories WHERE id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $id); // "i" indicates integer type

// Execute the statement
$stmt->execute();

// Check if deletion was successful
if($stmt->affected_rows == 1) {
    header('location:list_shoes_cat.php?msg=2');
} else {
    header('location:list_shoes_cat.php?msg=3');
}

// Close statement and connection
$stmt->close();
$connection->close();
?>
