<?php
session_start();

// Check if user is logged in, else redirect
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// PNR Remove Handle Logic
if (isset($_GET['remove_pnr'])) {
    unset($_SESSION['active_pnr']);
    header("Location: dashboard.php?tab=journeys");
    exit();
}

// Database Connection
$host = "sql108.infinityfree.com";
$db_user = "if0_42253514";       
$db_pass = "12345678DCsac";           
$db_name = "if0_42253514_irctc";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

$uid = $_SESSION['user_id'];

// Fetch user dynamic fields and cross-reference WinBin point balances synchronously via Mobile Key
$user_points = 0;
$user_phone = '';
$user_email = '';
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';

$pts_query = "SELECT mobile, email FROM users WHERE id = '$uid' LIMIT 1";
$pts_result = $conn->query($pts_query);
if ($pts_result && $pts_result->num_rows > 0) {
    $pts_row = $pts_result->fetch_assoc();
    $user_phone = $pts_row['mobile'];
    $user_email = $pts_row['email'];
    
    // Remote cross-database balance check targeting the WinBin table layout
    $wb_query = "SELECT points FROM `if0_42253514_winbin_db`.`users` WHERE mobile = '$user_phone' LIMIT 1";
    $wb_result = $conn->query($wb_query);
    if ($wb_result && $wb_result->num_rows > 0) {
        $wb_row = $wb_result->fetch_assoc();
        $user_points = intval($wb_row['points']);
    }
}

// Save active selection tab parameters setup state
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IRCTC eCatering - User Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-container { display: flex; max-width: 1200px; margin: 40px auto; gap: 30px; padding: 0 20px; }
        .sidebar-menu { width: 280px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; display: flex; flex-direction: column; gap: 8px; height: fit-content; }
        .sidebar-menu-item { padding: 12px 16px; border-radius: 6px; font-size: 14px; font-weight: 500; color: #4a5568; cursor: pointer; transition: all 0.2s; text-align: left; background: none; border: none; width: 100%; }
        .sidebar-menu-item:hover { background: #f7f9fb; color: #f25c22; }
        .active-tab { background: #fffaf8 !important; color: #f25c22 !important; border-left: 4px solid #f25c22; border-radius: 0 6px 6px 0; padding-left: 12px; }
        
        .content-display-area { flex: 1; background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 35px; min-height: 500px; }
        .tab-content-panel { display: none; }
        .active-panel { display: block; }
        
        .profile-title { font-size: 22px; font-weight: 700; color: #2d3748; margin-bottom: 25px; border-bottom: 2px solid #f7f9fb; padding-bottom: 12px; }
        .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px; }
        .info-field-card { background: #f8fafc; border: 1px solid #edf2f7; padding: 16px; border-radius: 6px; }
        .field-label { font-size: 11px; text-transform: uppercase; font-weight: 600; color: #718096; letter-spacing: 0.5px; margin-bottom: 5px; }
        .field-value { font-size: 15px; font-weight: 500; color: #2d3748; }
        
        /* Modernized Attractive Orders Layout based on image_90160b.png */
        .order-card { border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px; margin-bottom: 20px; background: #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
        .order-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #e2e8f0; padding-bottom: 14px; margin-bottom: 16px; }
        .order-id { font-weight: 700; color: #1a202c; font-size: 15px; display: flex; align-items: center; gap: 6px; }
        .order-status { font-size: 12px; font-weight: 700; padding: 6px 14px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-confirmed { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .order-details { font-size: 14px; color: #4a5568; }
        .order-meta-row { display: flex; margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; }
        .order-meta-row:last-of-type { border-bottom: none; padding-bottom: 0; margin-bottom: 0; }
        .meta-label { width: 140px; font-weight: 600; color: #718096; display: flex; align-items: center; gap: 6px; }
        .meta-value { flex: 1; color: #2d3748; font-weight: 500; }
        .order-footer-strip { display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 12px 16px; border-radius: 8px; margin-top: 14px; border: 1px solid #edf2f7; }
        
        /* WinBin UI Layout */
        .winbin-wallet-card { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 12px; padding: 25px; color: white; margin-bottom: 30px; border: 1px solid #334155; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .wallet-flex { display: flex; justify-content: space-between; align-items: center; }
        .wb-logo { font-weight: bold; font-size: 18px; color: #00e699; letter-spacing: 0.5px; display: flex; align-items: center; gap: 8px; }
        .wb-balance { font-size: 32px; font-weight: 700; color: #ffffff; margin-top: 10px; }
        
        .ledger-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .ledger-table th { text-align: left; padding: 12px; background: #f1f5f9; color: #475569; font-size: 12px; text-transform: uppercase; font-weight: 600; }
        .ledger-table td { padding: 12px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #334155; }
        .badge-added { background: #dcfce7; color: #15803d; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
        .badge-deducted { background: #fee2e2; color: #b91c1c; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; text-transform: uppercase; }

        .faq-item { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #edf2f7; }
        .faq-question { font-weight: 600; color: #2d3748; font-size: 15px; margin-bottom: 5px; }
        .faq-answer { color: #718096; font-size: 14px; line-height: 1.5; }

        .btn-update-profile { background: #ffffff; border: 1px solid #cbd5e0; padding: 10px 18px; border-radius: 4px; font-size: 14px; font-weight: 500; color: #2d3748; cursor: pointer; transition: background 0.2s; }
        .btn-update-profile:hover { background: #f7fafc; }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="nav-left" onclick="window.location.href='index.php'" style="cursor:pointer;">
            <img src="https://www.ecatering.irctc.co.in/assets/images/logo.png" alt="IRCTC Logo" style="height: 42px;">
            <div style="border-left: 2px solid #e2e8f0; padding-left: 12px;">
                <h1 style="font-size: 15px; color: #2d3748; font-weight: 700; letter-spacing: 0.3px;">eCatering</h1>
                <p style="font-size: 10px; color: #718096; font-weight: 500;">Food Track Delivery</p>
            </div>
        </div>
        <div class="nav-right">
            <span class="nav-item">👤 Welcome, <strong><?php echo htmlspecialchars($user_name); ?></strong></span>
            <a href="logout.php" class="nav-item" style="color: #f25c22; font-weight: bold;">Logout</a>
        </div>
    </div>

    <div class="dashboard-container">
        
        <div class="sidebar-menu">
            <button class="sidebar-menu-item <?php echo $active_tab == 'profile' ? 'active-tab' : ''; ?>" id="tab-btn-profile" onclick="switchTab('profile')">👤 My Profile</button>
            <button class="sidebar-menu-item <?php echo $active_tab == 'orders' ? 'active-tab' : ''; ?>" id="tab-btn-orders" onclick="switchTab('orders')">🛍️ My Orders</button>
            <button class="sidebar-menu-item <?php echo $active_tab == 'journeys' ? 'active-tab' : ''; ?>" id="tab-btn-journeys" onclick="switchTab('journeys')">🚆 Active Booked Journeys</button>
            <button class="sidebar-menu-item <?php echo $active_tab == 'winbin' ? 'active-tab' : ''; ?>" id="tab-btn-winbin" onclick="switchTab('winbin')">♻️ WinBin Eco Rewards</button>
            <button class="sidebar-menu-item <?php echo $active_tab == 'faq' ? 'active-tab' : ''; ?>" id="tab-btn-faq" onclick="switchTab('faq')">❓ Help & FAQ</button>
            <button class="sidebar-menu-item <?php echo $active_tab == 'settings' ? 'active-tab' : ''; ?>" id="tab-btn-settings" onclick="switchTab('settings')">⚙️ Account Settings</button>
        </div>

        <div class="content-display-area">

            <div id="panel-profile" class="tab-content-panel <?php echo $active_tab == 'profile' ? 'active-panel' : ''; ?>">
                <h2 class="profile-title">Profile Information</h2>
                <div class="info-grid">
                    <div class="info-field-card">
                        <div class="field-label">Full Name</div>
                        <div class="field-value"><?php echo htmlspecialchars($user_name); ?></div>
                    </div>
                    <div class="info-field-card">
                        <div class="field-label">Linked Mobile Reference</div>
                        <div class="field-value"><?php echo htmlspecialchars($user_phone); ?></div>
                    </div>
                    <div class="info-field-card">
                        <div class="field-label">Email Communications</div>
                        <div class="field-value"><?php echo htmlspecialchars($user_email); ?></div>
                    </div>
                    <div class="info-field-card">
                        <div class="field-label">Current Ecosystem Sync</div>
                        <div class="field-value" style="color:#00e699; font-weight: bold;">WINBIN Node Link Active</div>
                    </div>
                </div>
            </div>

            <div id="panel-orders" class="tab-content-panel <?php echo $active_tab == 'orders' ? 'active-panel' : ''; ?>">
                <h2 class="profile-title">My Food Orders</h2>
                <?php
                $order_query = "SELECT * FROM orders WHERE user_id = '$uid' ORDER BY id DESC";
                $order_result = $conn->query($order_query);
                
                if ($order_result && $order_result->num_rows > 0) {
                    while ($orow = $order_result->fetch_assoc()) {
                        echo '<div class="order-card">
                                <div class="order-header">
                                    <div class="order-id">📦 Order Reference #' . htmlspecialchars($orow['order_id']) . '</div>
                                    <div class="order-status status-confirmed">✨ ' . htmlspecialchars($orow['status']) . '</div>
                                </div>
                                <div class="order-details">
                                    <div class="order-meta-row">
                                        <div class="meta-label">🏪 Restaurant:</div>
                                        <div class="meta-value">' . htmlspecialchars($orow['restaurant_name']) . '</div>
                                    </div>
                                    <div class="order-meta-row">
                                        <div class="meta-label">🍽️ Items Packed:</div>
                                        <div class="meta-value" style="color: #4a5568;">' . htmlspecialchars($orow['items']) . '</div>
                                    </div>
                                    <div class="order-footer-strip">
                                        <div style="font-size: 13px; color: #718096; display: flex; align-items: center; gap: 4px;">🕒 ' . htmlspecialchars($orow['created_at']) . '</div>
                                        <div style="font-size: 16px; font-weight: 700; color: #f25c22;">Total Paid: ₹' . htmlspecialchars($orow['total_amount']) . '</div>
                                    </div>
                                </div>
                              </div>';
                    }
                } else {
                    echo '<div style="text-align: center; padding: 40px; color: #718096;">
                            <p style="font-size: 15px;">No corporate catering orders placed from this user account ledger yet.</p>
                          </div>';
                }
                ?>
            </div>

            <div id="panel-journeys" class="tab-content-panel <?php echo $active_tab == 'journeys' ? 'active-panel' : ''; ?>">
                <h2 class="profile-title">Active Booked Journeys</h2>
                <?php if (isset($_SESSION['active_pnr'])): ?>
                    <div style="background: #fffaf8; border: 1px solid #fee2e2; padding: 20px; border-radius: 6px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #e53e3e;">Current Selected Trip</div>
                            <div style="font-size: 20px; font-weight: 700; color: #2d3748; margin-top: 3px;">PNR Number: <?php echo $_SESSION['active_pnr']; ?></div>
                        </div>
                        <div style="display:flex; gap:10px;">
                            <a href="restaurants.php" class="btn-primary-submit" style="text-decoration:none; display:inline-block; font-size:12px; padding:10px 20px;">ORDER FOOD NOW</a>
                            <button onclick="confirmRemovePNR()" style="background:#edf2f7; border:none; padding:10px 15px; border-radius:4px; font-weight:600; font-size:12px; cursor:pointer; color:#4a5568;">Remove PNR</button>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px; color: #718096;">
                        <p style="font-size: 15px; margin-bottom: 15px;">No upcoming journey coordinates selected in current session.</p>
                        <a href="index.php" class="btn-primary-submit" style="text-decoration: none;">Enter PNR to Order Food</a>
                    </div>
                <?php endif; ?>
            </div>

            <div id="panel-winbin" class="tab-content-panel <?php echo $active_tab == 'winbin' ? 'active-panel' : ''; ?>">
                <h2 class="profile-title">WinBin Sustainability Dashboard</h2>
                
                <div class="winbin-wallet-card">
                    <div class="wallet-flex">
                        <div>
                            <div class="wb-logo">♻️ WINBIN ECO SYSTEM WALLET</div>
                            <div style="font-size: 12px; color: #94a3b8; margin-top: 5px;">Linked Profile Mobile: <?php echo htmlspecialchars($user_phone); ?></div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 11px; text-transform: uppercase; color: #00e699; font-weight: 700; letter-spacing: 0.5px;">Available Discount Balance</div>
                            <div class="wb-balance">₹<?php echo $user_points; ?>.00</div>
                        </div>
                    </div>
                </div>

                <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 15px; color: #334155;">Ecosystem Activity History Ledger</h3>
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                    <table class="ledger-table">
                        <thead>
                            <tr>
                                <th>Transaction Node</th>
                                <th>Balance Variant Change</th>
                                <th>Logged Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $history_query = "SELECT action_type, points_changed, timestamp FROM `if0_42253514_winbin_db`.`transaction_history` WHERE mobile = '$user_phone' ORDER BY timestamp DESC";
                            $history_result = $conn->query($history_query);
                            
                            if ($history_result && $history_result->num_rows > 0) {
                                while ($row = $history_result->fetch_assoc()) {
                                    $action = strtolower($row['action_type']);
                                    $badge = ($action === 'added' || $action === 'credit') ? '<span class="badge-added">ADDED</span>' : '<span class="badge-deducted">DEDUCTED</span>';
                                    $sign = ($action === 'added' || $action === 'credit') ? '+' : '-';
                                    $color = ($action === 'added' || $action === 'credit') ? '#15803d' : '#b91c1c';
                                    echo "<tr>
                                            <td>{$badge}</td>
                                            <td style='font-weight:600; color:{$color};'>{$sign}₹{$row['points_changed']}.00</td>
                                            <td style='color:#64748b; font-size:13px;'>".htmlspecialchars($row['timestamp'])."</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' style='text-align:center; padding:20px; color:#64748b;'>No transaction history movements registered yet to this profile.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="panel-faq" class="tab-content-panel <?php echo $active_tab == 'faq' ? 'active-panel' : ''; ?>">
                <h2 class="profile-title">Account Configurations</h2>
                <div class="faq-item">
                    <div class="faq-question">Q1. How can I cancel my food delivery order?</div>
                    <div class="faq-answer">You can cancel your meals up to 2 hours before the scheduled train arrival time.</div>
                </div>
            </div>

            <div id="panel-settings" class="tab-content-panel <?php echo $active_tab == 'settings' ? 'active-panel' : ''; ?>">
                <h2 class="profile-title">Account Configurations</h2>
                <div style="background: transparent; padding: 10px 0; border-radius: 8px;">
                    <div style="display: flex; flex-direction: column; gap: 15px; max-width: 400px; align-items: flex-start;">
                        <button class="btn-update-profile" onclick="alert('Redirecting to update profile layout...')">Update Profile Details</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function switchTab(tabName) {
            const panels = document.querySelectorAll('.tab-content-panel');
            panels.forEach(panel => panel.classList.remove('active-panel'));
            
            const sideBtns = document.querySelectorAll('.sidebar-menu-item');
            sideBtns.forEach(btn => btn.classList.remove('active-tab'));
            
            const targetPanel = document.getElementById('panel-' + tabName);
            if(targetPanel) targetPanel.classList.add('active-panel');
            
            const targetBtn = document.getElementById('tab-btn-' + tabName);
            if(targetBtn) targetBtn.classList.add('active-tab');
            
            window.history.pushState({}, '', '?tab=' + tabName);
        }

        function confirmRemovePNR() {
            if (confirm("Are you sure you want to remove this journey from your profile?")) {
                window.location.href = "dashboard.php?remove_pnr=1";
            }
        }
    </script>
    
</body>
</html>
<?php $conn->close(); ?>