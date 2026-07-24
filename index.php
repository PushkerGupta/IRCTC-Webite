<?php
session_start();

// Database Connection
$host = "sql108.infinityfree.com";
$db_user = "if0_42253514";       
$db_pass = "12345678DCsac";           
$db_name = "if0_42253514_irctc";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Database Connection Error: " . $conn->connect_error);
}

// PNR Submission Logic
if (isset($_POST['search_pnr'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Please login to your account first before entering PNR!'); window.location.href='index.php';</script>";
        exit();
    }

    $pnr_input = trim($conn->real_escape_string($_POST['pnr_number']));
    
    if (empty($pnr_input)) {
        echo "<script>alert('Please enter a PNR number!'); window.location.href='index.php';</script>";
        exit();
    }

    $pnr_check_sql = "SELECT * FROM pnr_master WHERE pnr = '$pnr_input' LIMIT 1";
    $pnr_result = $conn->query($pnr_check_sql);

    if ($pnr_result && $pnr_result->num_rows > 0) {
        $_SESSION['active_pnr'] = $pnr_input;
        header("Location: restaurants.php");
        exit();
    } else {
        echo "<script>alert('Invalid PNR Number! No journey found.'); window.location.href='index.php';</script>";
        exit();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IRCTC eCatering - Food Delivery in Train</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* CSS FOR FOOTER */
        .site-footer {
            background-color: #1c2438; 
            color: #ffffff;
            padding: 50px 20px 20px 20px;
            font-family: Arial, sans-serif;
            margin-top: 60px;
        }
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 40px;
        }
        .footer-brand-column .footer-logo {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .footer-brand-column .footer-about {
            font-size: 14px;
            line-height: 1.6;
            color: #ffffffb3;
            margin-bottom: 20px;
        }
        .footer-socials {
            display: flex;
            gap: 12px;
        }
        .footer-socials .social-icon {
            background: rgba(255, 255, 255, 0.1);
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            text-decoration: none;
            font-size: 16px;
            transition: background 0.3s ease;
        }
        .footer-socials .social-icon:hover {
            background: rgba(255, 255, 255, 0.25);
        }
        .footer-links-column h4 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 20px;
            position: relative;
            color: #ffffff;
        }
        .footer-links-column h4::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -6px;
            width: 35px;
            height: 2px;
            background-color: #ff9800;
        }
        .footer-links-column ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .footer-links-column ul li {
            margin-bottom: 12px;
        }
        .footer-links-column ul li a {
            color: #ffffffb3;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease, padding-left 0.3s ease;
        }
        .footer-links-column ul li a:hover {
            color: #ff9800;
            padding-left: 5px;
        }
        .footer-bottom {
            max-width: 1200px;
            margin: 0 auto;
            padding-top: 20px;
            text-align: center;
            font-size: 13px;
            color: #ffffff80;
        }

        /* 6 BRANDS GRID - NO WHITE BOXES */
        .brand-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 30px;
            margin: 35px 0 55px 0;
            align-items: center;
            justify-items: center;
        }
        .brand-card {
            background: transparent;
            border: none;
            box-shadow: none;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease;
            cursor: pointer;
            width: 100%;
            max-width: 160px;
            box-sizing: border-box;
        }
        .brand-card:hover {
            transform: scale(1.06);
        }
        .brand-logo-svg {
            width: 100%;
            height: auto;
            max-height: 45px;
            object-fit: contain;
            display: block;
        }

        /* PARTNERS LAYOUT */
        .partners-grid-container {
            display: flex;
            flex-direction: column;
            gap: 24px;
            align-items: center;
            justify-content: center;
            margin-top: 30px;
            background-color: #f7f9fa;
            padding: 30px 20px;
            border-radius: 8px;
        }
        .partners-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 28px;
            align-items: center;
            width: 100%;
        }
        .partner-btn {
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            user-select: none;
        }
        
        /* Brand Specific Typography/Buttons */
        .btn-dominos { color: #0054a6; font-family: 'Arial Black', sans-serif; font-weight: 900; font-size: 19px; }
        .btn-zomato { background-color: #cb202d; color: white; border-radius: 6px; font-weight: bold; padding: 8px 20px; font-family: Arial, sans-serif; font-size: 16px; }
        .btn-swiggy { background-color: #fc6011; color: white; border-radius: 6px; font-weight: bold; padding: 8px 20px; font-family: Arial, sans-serif; font-size: 16px; }
        .btn-faasos { color: #4a148c; font-style: italic; font-weight: bold; font-size: 18px; font-family: Georgia, serif; }
        .btn-lunchbox { color: #ff6d00; font-weight: 900; font-family: 'Arial Black', sans-serif; }
        .btn-behrouz { color: #bfa15f; font-weight: bold; letter-spacing: 0.5px; font-family: Arial, sans-serif; text-transform: uppercase; }
        .btn-railyatri { background-color: #fff59d; color: #0d47a1; font-weight: bold; border-radius: 4px; padding: 8px 22px; font-family: Arial, sans-serif; letter-spacing: 0.5px; }

        @media (max-width: 768px) {
            .footer-container {
                grid-template-columns: 1fr;
                gap: 30px;
            }
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="nav-left">
            <strong style="color:#0d47a1; font-size: 18px; letter-spacing: 1px; cursor: pointer;" onclick="window.location.href='index.php'">IRCTC</strong>
            <span style="color:#ccc">|</span>
            <span style="font-size: 12px; color: #666; font-weight: 500;">Food on Track</span>
        </div>
        <div class="nav-right">
            <a href="#" class="nav-item">📱 Download App</a>
            <a href="#" class="nav-item">👥 Group Orders</a>
            
            <?php 
            if (isset($_SESSION['user_name'])) { 
            ?>
                <div class="user-logged-in">
                    <a href="dashboard.php" class="nav-item-user" style="text-decoration: none;">👤 <?php echo htmlspecialchars($_SESSION['user_name']); ?></a>
                </div>
            <?php 
            } else { 
            ?>
                <a href="javascript:void(0)" class="nav-item" onclick="openLoginModal()">👤 Account</a>
            <?php 
            } 
            ?>
        </div>
    </div>

    <div class="hero-section">
        <div class="center-logo">
            <div class="inner-logo-box">🚂</div>
        </div>
        <h1>Food delivery for your train journeys</h1>
        
        <div class="search-wrapper">
            <div class="search-input-box">
                <span class="search-icon">🔍</span>
                <input type="text" placeholder="Search food, brand, station, etc.">
            </div>
            
            <form action="index.php" method="POST" class="pnr-box">
                <span class="train-icon">🚂</span>
                <input type="text" name="pnr_number" placeholder="Enter PNR to order" maxlength="10" required oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                <button type="submit" name="search_pnr" class="submit-btn">SUBMIT</button>
            </form>
        </div>
    </div>

    <div class="main-container">

        <div class="row-boxes-container">
            <div class="equal-row-card">
                <div class="card-left-identity">
                    <div class="card-logo-slot" style="background: #eef2f7;">
                        <span style="font-size: 30px;">🚂</span>
                    </div>
                    <div class="card-text-side">
                        <h4>Indian Railway Catering and Tourism Corporation Limited</h4>
                        <p>Official website of Indian Railways and IRCTC to order fresh and hygienic food on trains during your journey.</p>
                    </div>
                </div>
                <button class="card-action-btn btn-blue" onclick="window.open('https://www.irctc.co.in', '_blank')">OFFICIAL SITE</button>
            </div>

            <div class="equal-row-card">
                <div class="card-left-identity">
                    <div class="card-logo-slot" style="background: #fff3e0;">
                        <span style="font-size: 30px;">👥</span>
                    </div>
                    <div class="card-text-side">
                        <h4>Group Orders</h4>
                        <p>Ordering for a group of more than 15 people? Easily place group orders, get assisted ordering and custom prices when ordering in bulk quantities.</p>
                    </div>
                </div>
                <button class="card-action-btn" onclick="alert('Group Booking Engine Module Loaded')">GROUP BOOKING</button>
            </div>

            <div class="equal-row-card">
                <div class="card-left-identity">
                    <div class="card-logo-slot" style="background: #e8f5e9;">
                        <span style="font-size: 30px;">♻️</span>
                    </div>
                    <div class="card-text-side">
                        <h4>WIN_BIN Waste Management</h4>
                        <p>Recycle plastic bottles at our station smart bins to earn eco-points! Enter your details, drop your waste, and claim exclusive discounts on your next eCatering order.</p>
                    </div>
                </div>
                <button class="card-action-btn btn-blue" onclick="alert('WIN-BIN System Profile Details Activated')">KNOW MORE</button>
            </div>
        </div>

        <!-- 6 BRANDS WITHOUT BOX BACKGROUNDS -->
        <div class="section-heading">Order from the best brands</div>
        <div class="brand-grid">
            <!-- 1. Domino's -->
            <div class="brand-card">
                <svg class="brand-logo-svg" viewBox="0 0 400 120" xmlns="http://www.w3.org/2000/svg">
                    <rect x="10" y="10" width="100" height="100" rx="15" fill="#006494" transform="rotate(-45 60 60)"/>
                    <circle cx="42" cy="42" r="10" fill="white"/>
                    <circle cx="42" cy="78" r="10" fill="white"/>
                    <rect x="85" y="10" width="100" height="100" rx="15" fill="#E31B23" transform="rotate(-45 135 60)"/>
                    <circle cx="135" cy="60" r="10" fill="white"/>
                    <text x="195" y="72" font-family="'Arial Black', sans-serif" font-size="44" font-weight="900" fill="#0054A6">Domino's</text>
                </svg>
            </div>
            
            <!-- 2. Zomato -->
            <div class="brand-card">
                <div style="background-color: #cb202d; padding: 10px 22px; border-radius: 8px; width: 100%; text-align: center; box-sizing: border-box;">
                    <span style="color: white; font-family: Arial, sans-serif; font-weight: bold; font-size: 22px; letter-spacing: -0.5px;">zomato</span>
                </div>
            </div>

            <!-- 3. Swiggy -->
            <div class="brand-card">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <svg width="26" height="32" viewBox="0 0 40 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M36 18C33 11 26 2 18 2C9 2 2 10 2 20C2 31 15 46 19 48C20 48 36 31 36 18Z" fill="#FC6011"/>
                        <path d="M14 16C15 22 24 18 23 26" stroke="white" stroke-width="4" stroke-linecap="round"/>
                    </svg>
                    <span style="color: #FC6011; font-family: 'Arial Black', sans-serif; font-size: 22px; font-weight: 800; letter-spacing: -0.5px;">SWIGGY</span>
                </div>
            </div>

            <!-- 4. Faasos -->
            <div class="brand-card">
                <span style="color: #4a148c; font-family: 'Georgia', serif; font-style: italic; font-weight: bold; font-size: 28px; letter-spacing: -1px;">Faasos</span>
            </div>

            <!-- 5. Lunch Box -->
            <div class="brand-card">
                <div style="border: 2px solid #ff6d00; padding: 6px 10px; border-radius: 6px; background: #fff8e1; text-align: center; width: 100%; max-width: 150px; box-sizing: border-box;">
                    <strong style="color:#ff6d00; font-family: 'Arial Black', sans-serif; font-size: 13px; letter-spacing: 0.2px; display: block; white-space: nowrap;">LUNCH BOX</strong>
                </div>
            </div>

            <!-- 6. Behrouz Biryani -->
            <div class="brand-card">
                <div style="background: #111; text-align: center; padding: 6px 16px; border-radius: 6px; width: 100%; box-sizing: border-box;">
                    <div style="color: #bfa15f; font-family: 'Times New Roman', serif; font-weight: bold; font-size: 15px; letter-spacing: 2px;">BEHROUZ</div>
                    <div style="color: #bfa15f; font-size: 7px; letter-spacing: 1px; border-top: 1px solid #bfa15f; margin-top: 2px; padding-top: 2px; white-space: nowrap;">THE ROYAL BIRYANI</div>
                </div>
            </div>
        </div>

        <div class="how-it-works-section">
            <h2>How it works</h2>
            <div class="steps-wrapper">
                <div class="step-item">
                    <div class="step-graphic-slot">🔍</div>
                    <div class="step-badge-row">
                        <span class="badge-num">1</span>
                        <span class="badge-text">Choose an outlet</span>
                    </div>
                    <p>Enter PNR & explore restaurants for your journey</p>
                </div>
                <div class="step-item">
                    <div class="step-graphic-slot">📋</div>
                    <div class="step-badge-row">
                        <span class="badge-num">2</span>
                        <span class="badge-text">Complete Order</span>
                    </div>
                    <p>Choose your food & schedule your order paying online or COD</p>
                </div>
                <div class="step-item">
                    <div class="step-graphic-slot">🍱</div>
                    <div class="step-badge-row">
                        <span class="badge-num">3</span>
                        <span class="badge-text">Enjoy Tasty Food</span>
                    </div>
                    <p>Enjoy your meal delivered to you</p>
                </div>
            </div>
        </div>

        <!-- AUTHORISED PARTNERS SECTION (OVEN STORY COMPLETELY REMOVED) -->
        <div class="partners-section">
            <h3 style="text-align: center; color: #455a64; font-size: 16px; margin-bottom: 5px;">Our authorised partners</h3>
            <div class="partners-grid-container">
                <!-- Row 1: Primary Outlets -->
                <div class="partners-row">
                    <span class="partner-btn btn-dominos">Domino's</span>
                    <span class="partner-btn btn-zomato">zomato</span>
                    <span class="partner-btn btn-swiggy">Swiggy</span>
                    <span class="partner-btn btn-faasos">Faasos</span>
                    <span class="partner-btn btn-lunchbox">LUNCH BOX</span>
                    <span class="partner-btn btn-behrouz">BEHROUZ</span>
                </div>
                
                <!-- Row 2: Isolated Center Aggregator -->
                <div class="partners-row">
                    <span class="partner-btn btn-railyatri">RAILYATRI</span>
                </div>
            </div>
        </div>
    </div>

    <!-- DARK BLUE FOOTER SECTION -->
    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-brand-column">
                <div class="footer-logo">
                    <strong style="color:#ffffff; font-size: 22px; letter-spacing: 1px;">IRCTC</strong>
                    <span style="color:#ffffff90; margin: 0 8px;">|</span>
                    <span style="font-size: 14px; color: #ffffffb3; font-weight: 500;">Food on Track</span>
                </div>
                <p class="footer-about">
                    Indian Railway Catering and Tourism Corporation Limited (IRCTC) brings you fresh, hygienic, and delicious food straight to your train berth. Partnering with the best brands to make your journey delightful.
                </p>
                <div class="footer-socials">
                    <a href="#" class="social-icon">🌐</a>
                    <a href="#" class="social-icon">📱</a>
                    <a href="#" class="social-icon">💬</a>
                </div>
            </div>

            <div class="footer-links-column">
                <h4>Useful Links</h4>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#" onclick="openLoginModal()">My Account</a></li>
                    <li><a href="#">Track Order</a></li>
                    <li><a href="#">Group Booking</a></li>
                </ul>
            </div>

            <div class="footer-links-column">
                <h4>Our Services</h4>
                <ul>
                    <li><a href="#">E-Catering</a></li>
                    <li><a href="#">WIN_BIN Waste Management</a></li>
                    <li><a href="https://www.irctc.co.in" target="_blank">Official Train Booking</a></li>
                    <li><a href="#">Station Lounges</a></li>
                </ul>
            </div>

            <div class="footer-links-column">
                <h4>Help & Support</h4>
                <ul>
                    <li><a href="#">FAQs</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms & Conditions</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> IRCTC eCatering. All Rights Reserved. Designed for Green & Smart Journeys.</p>
        </div>
    </footer>

    <div class="modal-overlay" id="loginModal">
        <div class="modal-box">
            <span class="close-modal" onclick="closeAllModals()">&times;</span>
            <div class="modal-header">Login</div>
            <form class="modal-body" action="auth.php" method="POST">
                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" placeholder="Enter your mobile number" required name="mobile" 
                           maxlength="10" pattern="[6-9][0-9]{9}" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" placeholder="Enter your email address" required name="email">
                </div>
                <p class="modal-note">You must enter the mobile number & email address previously used.</p>
                <div class="modal-footer-btns">
                    <button type="button" class="btn-signup-toggle" onclick="switchToSignup()">SIGN UP</button>
                    <button type="submit" name="login_action" class="btn-primary-submit">LOGIN</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="signupModal">
        <div class="modal-box">
            <span class="close-modal" onclick="closeAllModals()">&times;</span>
            <div class="modal-header">Signup</div>
            <form class="modal-body" action="auth.php" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" placeholder="Enter your full name" required name="fullname">
                </div>
                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" placeholder="Enter your mobile number" required name="mobile" 
                           maxlength="10" pattern="[6-9][0-9]{9}" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <div style="position: relative;">
                        <input type="email" placeholder="Enter your email address" required name="email">
                    </div>
                </div>
                <div class="privacy-tip-box">
                    <div class="tip-icon">🛡️</div>
                    <div class="tip-text">
                        <strong>Privacy Tip</strong>
                        <p>Never disclose your email address to restaurants, delivery partners, or our support team.</p>
                    </div>
                </div>
                <div class="modal-footer-btns" style="margin-top: 25px;">
                    <button type="button" class="btn-signup-toggle" onclick="switchToLogin()">LOGIN</button>
                    <button type="submit" name="register_action" class="btn-primary-submit">CREATE ACCOUNT</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openLoginModal() { document.getElementById('loginModal').style.display = 'flex'; }
        function switchToSignup() { document.getElementById('loginModal').style.display = 'none'; document.getElementById('signupModal').style.display = 'flex'; }
        function switchToLogin() { document.getElementById('signupModal').style.display = 'none'; document.getElementById('loginModal').style.display = 'flex'; }
        function closeAllModals() { document.getElementById('loginModal').style.display = 'none'; document.getElementById('signupModal').style.display = 'none'; }
    </script>
</body>
</html>