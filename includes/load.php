<?php
// -----------------------------------------------------------------------
// DEFINE SEPARATORS AND ROOT PATHS
// -----------------------------------------------------------------------
define("URL_SEPARATOR", '/');
define("DS", DIRECTORY_SEPARATOR);
defined('SITE_ROOT')? null: define('SITE_ROOT', realpath(dirname(__FILE__)));
define("LIB_PATH_INC", SITE_ROOT.DS);

// -----------------------------------------------------------------------
// INCLUDE NECESSARY FILES
// -----------------------------------------------------------------------
require_once(LIB_PATH_INC.'config.php');
require_once(LIB_PATH_INC.'functions.php');
require_once(LIB_PATH_INC.'session.php');
require_once(LIB_PATH_INC.'upload.php');
require_once(LIB_PATH_INC.'database.php');
require_once(LIB_PATH_INC.'sql.php');

// -----------------------------------------------------------------------
// DATABASE CONNECTION
// -----------------------------------------------------------------------
$db = new Database(); // Assuming you have a Database class that manages the connection

// -----------------------------------------------------------------------
// FUNCTION TO GET TOTAL SALES
// -----------------------------------------------------------------------
function get_total_sales() {
    global $db; // Use the global database connection
    $sql = "SELECT SUM(price) AS total_sales FROM sales"; // Adjust based on your schema
    $result = $db->query($sql);
    $data = $result->fetch_assoc();
    return $data['total_sales'] ?? 0;
}

// -----------------------------------------------------------------------
// FUNCTION TO GET TOTAL QUANTITY SOLD
// -----------------------------------------------------------------------
function get_total_quantity_sold() {
    global $db; // Use the global database connection
    $sql = "SELECT SUM(quantity) AS total_quantity FROM sales"; // Adjust based on your schema
    $result = $db->query($sql);
    $data = $result->fetch_assoc();
    return $data['total_quantity'] ?? 0;
}

// -----------------------------------------------------------------------
// OTHER FUNCTIONS (if any)
// -----------------------------------------------------------------------
?>
