<?php

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'kasir') {
    header("Location: ../index.php");
    exit;
}
