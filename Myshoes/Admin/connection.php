<?php
/**
 * Shared DB bootstrap — keeps older pages working with $connection
 * while using the OOP Database singleton under the hood.
 */
require_once __DIR__ . '/../classes/Database.php';
$connection = Database::getInstance()->getConnection();
?>
