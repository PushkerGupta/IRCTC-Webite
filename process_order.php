<?php
session_start();

$host = "sql108.infinityfree.com";
$db_user = "if0_42253514";       
$db_pass = "12345678DCsac";           
$db_name = "if0_42253514_irctc";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $points_deducted = isset($_POST['points_deducted']) ? intval($_POST['points_deducted']) : 0;
    $restaurant_name = isset($_POST['restaurant_name']) ? $conn->real_escape_string($_POST['restaurant_name']) : 'Unknown Restaurant';
    $total_amount = isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0.00;
    $items_json = isset($_POST['order_items_json']) ? $conn->real_escape_string($_POST['order_items_json']) : '[]';
    
    if (isset($_SESSION['user_id'])) {
        $uid = $_SESSION['user_id'];
        
        // Fetch user data directly from irctc database to identify linked account mobile number
        $user_query = "SELECT mobile FROM users WHERE id = '$uid' LIMIT 1";
        $user_result = $conn->query($user_query);
        
        if ($user_result && $user_result->num_rows > 0) {
            $user_row = $user_result->fetch_assoc();
            $mobile = $user_row['mobile'];
            
            // Insert order details directly inside internal order tables registry database track
            $rand_order_id = "OD" . rand(100000, 999999);
            $order_insert = "INSERT INTO orders (user_id, order_id, items, restaurant_name, total_amount, status) VALUES ('$uid', '$rand_order_id', '$items_json', '$restaurant_name', '$total_amount', 'CONFIRMED')";
            $conn->query($order_insert);

            if ($points_deducted > 0) {
                // 1. Update internal point tracking balance counters mirror
                $update_irctc_points = "UPDATE users SET win_bin_points = win_bin_points - $points_deducted WHERE id = '$uid'";
                $conn->query($update_irctc_points);
                
                // 2. CRITICAL SYNC: Decrement real primary reward balances from WinBin schema table directly
                $update_winbin_points = "UPDATE `if0_42253514_winbin_db`.`users` SET points = points - $points_deducted WHERE mobile = '$mobile'";
                $conn->query($update_winbin_points);
                
                // 3. LEDGER INSIGHT ENTRY: Insert explicit tracker log row entry into WinBin central audit history ledger
                $log_winbin_history = "INSERT INTO `if0_42253514_winbin_db`.`transaction_history` (mobile, action_type, points_changed) VALUES ('$mobile', 'deducted', $points_deducted)";
                $conn->query($log_winbin_history);
            }
        }
    }
    
    echo "<script>alert('Order Processed Successfully! Win-Bin Points Deducted and Balanced across Ecosystem.'); window.location.href='dashboard.php?tab=winbin';</script>";
}

$conn->close();
?>