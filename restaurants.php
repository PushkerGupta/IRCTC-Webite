<?php
session_start();

if (!isset($_SESSION['active_pnr'])) {
    header("Location: index.php");
    exit();
}

$active_pnr = $_SESSION['active_pnr'];

// Database Connection to fetch live user points dynamically
$host = "sql108.infinityfree.com";
$db_user = "if0_42253514";       
$db_pass = "12345678DCsac";           
$db_name = "if0_42253514_irctc";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

$user_points = 0; 
$user_mobile = '';

if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $pts_query = "SELECT mobile FROM users WHERE id = '$uid' LIMIT 1";
    $pts_result = $conn->query($pts_query);
    if ($pts_result && $pts_result->num_rows > 0) {
        $pts_row = $pts_result->fetch_assoc();
        $user_mobile = $pts_row['mobile'];
        
        // Remote cross-database retrieval of available real points balance
        $wb_query = "SELECT points FROM `if0_42253514_winbin_db`.`users` WHERE mobile = '$user_mobile' LIMIT 1";
        $wb_result = $conn->query($wb_query);
        if ($wb_result && $wb_result->num_rows > 0) {
            $wb_row = $wb_result->fetch_assoc();
            $user_points = intval($wb_row['points']);
        }
    }
}

// FULLY EXPANDED MASTER DATA: All Stations, Restaurants, and Extensive Menus (Capped under ₹250)
$stations_data = [
    [
        "code" => "JBP",
        "name" => "Jabalpur Junction",
        "time" => "Departure 15:30",
        "restaurants" => [
            [
                "id" => "res_jbp_1",
                "name" => "Rishi Regency Rajbhog", 
                "rating" => "4.3 ⭐", 
                "type" => "Veg / Non-Veg", 
                "cuisines" => "North Indian, Thalis, Punjabi", 
                "image" => "https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=150&auto=format&fit=crop&q=60",
                "menu" => [
                    ["id" => "jbp_m1", "name" => "Deluxe Veg Thali", "price" => 180, "is_veg" => true, "desc" => "Paneer Sabji, Dal Fry, 4 Roti, Rice, Sweet, Salad"],
                    ["id" => "jbp_m2", "name" => "Kadhai Paneer Mini", "price" => 160, "is_veg" => true, "desc" => "Fresh cottage cheese cooked in spicy tomato gravy"],
                    ["id" => "jbp_m3", "name" => "Butter Naan Combo", "price" => 120, "is_veg" => true, "desc" => "2 Butter Naan served with rich Dal Makhani"],
                    ["id" => "jbp_m3_extra", "name" => "Chana Masala Box", "price" => 140, "is_veg" => true, "desc" => "Spicy chickpea curry cooked in authentic punjabi style"]
                ]
            ],
            [
                "id" => "res_jbp_2",
                "name" => "Indian Coffee House (ICH)", 
                "rating" => "4.1 ⭐", 
                "type" => "Veg / Non-Veg", 
                "cuisines" => "South Indian, Dosas, Chinese", 
                "image" => "https://images.unsplash.com/photo-1668236543090-82eba5ee5976?w=150&auto=format&fit=crop&q=60",
                "menu" => [
                    ["id" => "jbp_m4", "name" => "Masala Dosa Rail Pack", "price" => 110, "is_veg" => true, "desc" => "Crispy rice crepe filled with spiced potatoes, sambhar & chutney"],
                    ["id" => "jbp_m5", "name" => "Veg Hakka Noodles", "price" => 140, "is_veg" => true, "desc" => "Classic stir-fried noodles with fresh chopped veggies"],
                    ["id" => "jbp_m6", "name" => "Filter Coffee with Idli", "price" => 90, "is_veg" => true, "desc" => "Authentic South Indian Filter Coffee with 2 pcs soft Idli"],
                    ["id" => "jbp_m6_extra", "name" => "Mendu Vada Plate", "price" => 100, "is_veg" => true, "desc" => "2 pcs crispy fried lentil donuts served with hot sambhar"]
                ]
            ],
            [
                "id" => "res_jbp_3",
                "name" => "Jabalpur Chat Chatori", 
                "rating" => "4.4 ⭐", 
                "type" => "Pure Veg", 
                "cuisines" => "Local Street Food, Snacks", 
                "image" => "https://images.unsplash.com/photo-1626132647523-66f5bf380027?w=150&auto=format&fit=crop&q=60",
                "menu" => [
                    ["id" => "jbp_m7", "name" => "Special Chole Bhature", "price" => 120, "is_veg" => true, "desc" => "2 Large fluffy bhature served with spicy delhi style chole and pickle"],
                    ["id" => "jbp_m8", "name" => "Premium Pav Bhaji", "price" => 110, "is_veg" => true, "desc" => "Butter loaded thick vegetable gravy served with 2 soft pavs"],
                    ["id" => "jbp_m8_extra", "name" => "Aloo Tikki Chaat Box", "price" => 80, "is_veg" => true, "desc" => "2 Crispy aloo tikkis topped with curd, green & sweet chutney"]
                ]
            ],
            [
                "id" => "res_jbp_4",
                "name" => "Hotel Kalchuri (MP Tourism)", 
                "rating" => "4.5 ⭐", 
                "type" => "Veg / Non-Veg", 
                "cuisines" => "Mughlai, North Indian, Continental", 
                "image" => "https://images.unsplash.com/photo-1631452180519-c014fe946bc7?w=150&auto=format&fit=crop&q=60",
                "menu" => [
                    ["id" => "jbp_m9", "name" => "Shahi Paneer Meal", "price" => 190, "is_veg" => true, "desc" => "Rich tomato-cashew gravy paneer with 3 butter rotis and rice"],
                    ["id" => "jbp_m10", "name" => "Chicken Biryani Box", "price" => 220, "is_veg" => false, "desc" => "Fragrant basmati rice layered with juicy chicken pieces and spices"],
                    ["id" => "jbp_m11", "name" => "Egg Curry Combo", "price" => 150, "is_veg" => false, "desc" => "2 Eggs in home-style savory gravy with steamed rice"]
                ]
            ]
        ]
    ],
    [
        "code" => "NU",
        "name" => "Narsinghpur",
        "time" => "Arrival 16:43",
        "restaurants" => [
            [
                "id" => "res_nu_1",
                "name" => "Shree Maya Restaurant", 
                "rating" => "4.0 ⭐", 
                "type" => "Pure Veg", 
                "cuisines" => "Traditional Indian, Thalis, Sweets", 
                "image" => "https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=150&auto=format&fit=crop&q=60",
                "menu" => [
                    ["id" => "nu_m1", "name" => "Special Narsinghpur Thali", "price" => 160, "is_veg" => true, "desc" => "Seasonal Veg, Dal Tadka, Rice, 4 Butter Roti, Gulabjamun"],
                    ["id" => "nu_m2", "name" => "Shahi Paneer Combo", "price" => 190, "is_veg" => true, "desc" => "Rich creamy shahi paneer served with choice of Rice or 3 Lachha Paratha"]]
            ],
            [
                "id" => "res_nu_2",
                "name" => "Ma Narmada Rasoi", 
                "rating" => "4.2 ⭐", 
                "type" => "Pure Veg", 
                "cuisines" => "Dhaba Style, North Indian", 
                "image" => "https://images.unsplash.com/photo-1626777552726-4a6b54c97e46?w=150&auto=format&fit=crop&q=60",
                "menu" => [
                    ["id" => "nu_m3", "name" => "Sev Tamatar Combo", "price" => 140, "is_veg" => true, "desc" => "Famous Malwa style spicy Sev Tamatar Sabji served with 4 Tandoori Rotis"],
                    ["id" => "nu_m4", "name" => "Jeera Rice Dal Fry Pack", "price" => 130, "is_veg" => true, "desc" => "Arhar Dal Tadka with aromatic Jeera Rice and roasted papad"]]
            ],
            [
                "id" => "res_nu_3",
                "name" => "Sai Kirpa Bhojnalaya", 
                "rating" => "3.9 ⭐", 
                "type" => "Pure Veg", 
                "cuisines" => "Local Bundelkhandi, Simple Meals", 
                "image" => "https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=150&auto=format&fit=crop&q=60",
                "menu" => [
                    ["id" => "nu_m5", "name" => "Standard Mini Thali", "price" => 100, "is_veg" => true, "desc" => "Dal, Aloo Jeera Sabji, 4 Plain Rotis, Rice, Salad"],
                    ["id" => "nu_m6", "name" => "Baingan Bharta Pack", "price" => 120, "is_veg" => true, "desc" => "Smoked roasted eggplant mashed cooked with peas & 3 tawa parathas"]]
            ]
        ]
    ],
    [
        "code" => "PPI",
        "name" => "Pipariya",
        "time" => "Arrival 17:55",
        "restaurants" => [
            [
                "id" => "res_ppi_1",
                "name" => "Satpura Dhaba & Hotel", 
                "rating" => "4.2 ⭐", 
                "type" => "Pure Veg", 
                "cuisines" => "Maharashtrian, North Indian, Jain Food", 
                "image" => "https://images.unsplash.com/photo-1613292443284-8d10ef9383fe?w=150&auto=format&fit=crop&q=60",
                "menu" => [
                    ["id" => "ppi_m1", "name" => "Sev Tamatar Sabji", "price" => 130, "is_veg" => true, "desc" => "Spicy and tangy tomato curry topped with crispy sev"],
                    ["id" => "ppi_m2", "name" => "Jain Special Khichdi", "price" => 120, "is_veg" => true, "desc" => "Light and healthy yellow lentil rice prepared without onion & garlic"]]
            ],
            [
                "id" => "res_ppi_2",
                "name" => "Pachmarhi Foothills Restro", 
                "rating" => "4.4 ⭐", 
                "type" => "Veg / Non-Veg", 
                "cuisines" => "Fast Food, Chinese, Tandoor", 
                "image" => "https://images.unsplash.com/photo-1512058564366-18510be2db19?w=150&auto=format&fit=crop&q=60",
                "menu" => [
                    ["id" => "ppi_m3", "name" => "Veg Manchurian Dry", "price" => 140, "is_veg" => true, "desc" => "Deep fried mixed veg balls tossed in flavorful sweet and spicy sauces"],
                    ["id" => "ppi_m4", "name" => "Butter Chicken Mini Box", "price" => 230, "is_veg" => false, "desc" => "Boneless chicken chunks cooked in rich buttery tomato gravy + 2 butter rotis"]]
            ],
            [
                "id" => "res_ppi_3",
                "name" => "Hotel Shrinath", 
                "rating" => "4.1 ⭐", 
                "type" => "Pure Veg", 
                "cuisines" => "South Indian, Gujrati Snacks", 
                "image" => "https://images.unsplash.com/photo-1668236543090-82eba5ee5976?w=150&auto=format&fit=crop&q=60",
                "menu" => [
                    ["id" => "ppi_m5", "name" => "Idli Sambhar Plate", "price" => 80, "is_veg" => true, "desc" => "3 Steamed puffy rice cakes served with steaming hot thick sambhar"],
                    ["id" => "ppi_m6", "name" => "Gujarati Farsan Combo", "price" => 95, "is_veg" => true, "desc" => "Fresh nylon khaman dhokla served with green mint chutney and fried chillies"]]
            ]
        ]
    ],
    [
        "code" => "ET",
        "name" => "Itarsi Junction",
        "time" => "Arrival 19:20",
        "restaurants" => [
            [
                "id" => "res_et_1",
                "name" => "Hotel Raj Bhoj & Caterers", 
                "rating" => "4.6 ⭐", 
                "type" => "Pure Veg", 
                "cuisines" => "Premium High-Class Thalis, Punjabi", 
                "image" => "https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=150&auto=format&fit=crop&q=60",
                "menu" => [
                    ["id" => "et_m1", "name" => "Maharaja Special Thali", "price" => 220, "is_veg" => true, "desc" => "2 Premium Veg, Special Dal, Jeera Rice, Sweets, Raita, Papad"],
                    ["id" => "et_m2", "name" => "Chole Bhature Rail Pack", "price" => 120, "is_veg" => true, "desc" => "Spicy Delhi style chole served with 2 fluffy golden bhature"]]
            ],
            [
                "id" => "res_et_2",
                "name" => "Haldiram's Express", 
                "rating" => "4.5 ⭐", 
                "type" => "Pure Veg Store", 
                "cuisines" => "Sweets, Snacks, Chaat", 
                "image" => "https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=150&auto=format&fit=crop&q=60",
                "menu" => [
                    ["id" => "et_m3", "name" => "Raj Kachori Chaat", "price" => 95, "is_veg" => true, "desc" => "Big crispy kachori stuffed with sprouts, yogurt, sweet & sour chutneys"],
                    ["id" => "et_m4", "name" => "Soan Papdi Box (250g)", "price" => 110, "is_veg" => true, "desc" => "Classic flaky sweet made from gram flour, ghee and dryfruits"]]
            ],
            [
                "id" => "res_et_3",
                "name" => "Sudarshan Bhojnalaya", 
                "rating" => "4.3 ⭐", 
                "type" => "Pure Veg", 
                "cuisines" => "North Indian, Rajasthani Food", 
                "image" => "https://images.unsplash.com/photo-1626777552726-4a6b54c97e46?w=150&auto=format&fit=crop&q=60",
                "menu" => [
                    ["id" => "et_m5", "name" => "Dal Baati Churma Combo", "price" => 170, "is_veg" => true, "desc" => "3 Baked clay oven baatis dipped in pure ghee, punchmel dal and sweet churma pocket"],
                    ["id" => "et_m6", "name" => "Mix Veg Handi", "price" => 140, "is_veg" => true, "desc" => "Assorted seasonal vegetables simmered inside a rich medium spicy semi-gravy"]]
            ],
            [
                "id" => "res_et_4",
                "name" => "Itarsi Food Plaza", 
                "rating" => "4.0 ⭐", 
                "type" => "Veg / Non-Veg", 
                "cuisines" => "Fast Food, Burgers, Egg Meals", 
                "image" => "https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=150&auto=format&fit=crop&q=60",
                "menu" => [
                    ["id" => "et_m7", "name" => "Crispy Veg Burger Combo", "price" => 110, "is_veg" => true, "desc" => "Potato herb patty burger with crispy salted golden french fries"],
                    ["id" => "et_m8", "name" => "Egg Bhurji (3 Eggs) + Roti", "price" => 130, "is_veg" => false, "desc" => "Scrambled spicy egg bhurji served along with 4 fresh whole wheat rotis"]]
            ]
        ]
    ],
    [
        "code" => "BPL",
        "name" => "Bhopal Junction",
        "time" => "Arrival 21:15",
        "restaurants" => [
            [
                "id" => "res_bpl_1",
                "name" => "Manohar Dairy & Restaurant", 
                "rating" => "4.6 ⭐", 
                "type" => "Pure Veg", 
                "cuisines" => "Premium Sweets, North Indian, Snacks", 
                "image" => "https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=150&auto=format&fit=crop&q=60",
                "menu" => [
                    ["id" => "bpl_m1", "name" => "Manohar Special Thali", "price" => 240, "is_veg" => true, "desc" => "Paneer Butter Masala, Dal Fry, Seasonal Sabji, Rice, 3 Butter Roti, Gulabjamun"],
                    ["id" => "bpl_m2", "name" => "Premium Raj Kachori", "price" => 110, "is_veg" => true, "desc" => "Crispy royal kachori stuffed with namkeen, dahi, and sweet imli chutneys"],
                    ["id" => "bpl_m3", "name" => "Special Paneer Kulcha", "price" => 160, "is_veg" => true, "desc" => "2 pcs soft tandoori baked kulchas loaded with grated paneer and spicy chole masala"]]
            ],
            [
                "id" => "res_bpl_2",
                "name" => "Sagar Gaire Fast Food", 
                "rating" => "4.5 ⭐", 
                "type" => "Pure Veg", 
                "cuisines" => "Bhopali Special Sandwiches, Pastas", 
                "image" => "https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=150&auto=format&fit=crop&q=60",
                "menu" => [
                    ["id" => "bpl_m4", "name" => "Veg Cheese Sandwich", "price" => 120, "is_veg" => true, "desc" => "Gaire's iconic liquid cheese dripping double decker grilled sandwich"],
                    ["id" => "bpl_m5", "name" => "Special Veg Soup Pasta", "price" => 140, "is_veg" => true, "desc" => "Italian penne tossed in rich desified spicy red gravy with veggies"]]
            ],
            [
                "id" => "res_bpl_3",
                "name" => "Bapu Ki Kutia", 
                "rating" => "4.4 ⭐", 
                "type" => "Pure Veg", 
                "cuisines" => "Traditional North Indian, Economy Thalis", 
                "image" => "https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=150&auto=format&fit=crop&q=60",
                "menu" => [
                    ["id" => "bpl_m6", "name" => "Kutia Special Khichdi", "price" => 130, "is_veg" => true, "desc" => "Butter fried classic comfort moong dal khichdi served with curd and green chutney"],
                    ["id" => "bpl_m7", "name" => "Paneer Do Pyaza", "price" => 170, "is_veg" => true, "desc" => "Fresh soft paneer cubes cooked in double sweet-sour onion rich semi-dry gravy"]]
            ]
        ]
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Station & Restaurants - IRCTC eCatering</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .split-booking-container { max-width: 1200px; margin: 25px auto; display: flex; gap: 25px; padding: 0 20px; }
        .station-sidebar { flex: 0 0 300px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; align-self: flex-start; }
        .sidebar-title-header { background-color: #0d47a1; color: #ffffff; padding: 16px 20px; font-size: 14px; font-weight: bold; }
        .station-list-wrapper { display: flex; flex-direction: column; }
        .station-clickable-row { padding: 16px 20px; border-bottom: 1px solid #edf2f7; cursor: pointer; transition: all 0.2s; background: #ffffff; display: flex; justify-content: space-between; align-items: center; }
        .station-clickable-row:hover { background: #f7fafc; }
        .station-clickable-row.active-stop { background: #fff3e0; border-left: 5px solid #f25c22; }
        .station-info-meta h4 { font-size: 14px; color: #2d3748; margin-bottom: 4px; }
        .station-info-meta p { font-size: 12px; color: #718096; }
        .station-code-badge { background: #edf2f7; color: #4a5568; padding: 2px 6px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .active-stop .station-code-badge { background: #f25c22; color: #ffffff; }
        
        .restaurant-list-view { flex: 1; }
        .route-summary-strip { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .hotel-card-item { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; transition: transform 0.2s; }
        .hotel-card-item:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .hotel-left-details { display: flex; gap: 18px; align-items: center; }
        
        .hotel-icon-box { width: 70px; height: 70px; background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .hotel-icon-box img { width: 100%; height: 100%; object-fit: cover; }
        
        .hotel-text-block h3 { font-size: 16px; color: #2d3748; margin-bottom: 4px; }
        .hotel-tag-line { font-size: 12px; color: #718096; margin-bottom: 6px; }
        .badge-rating { background: #4caf50; color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: bold; }
        .btn-order-food { background-color: #f25c22; color: white; border: none; padding: 10px 20px; border-radius: 4px; font-weight: bold; font-size: 13px; cursor: pointer; }
        .btn-order-food:hover { background-color: #d84315; }
        .station-panel-content { display: none; }
        .station-panel-content.active-panel { display: block; }
        
        .menu-cart-split-window { display: none; gap: 20px; margin-top: 15px; }
        .menu-items-list-block { flex: 1; background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; }
        .menu-header-box { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #edf2f7; padding-bottom: 12px; margin-bottom: 15px; }
        .menu-header-box h2 { font-size: 18px; color: #2d3748; }
        .dish-row-item { display: flex; justify-content: space-between; align-items: center; padding: 16px 0; border-bottom: 1px solid #edf2f7; }
        .dish-details-side { flex: 1; padding-right: 15px; }
        .veg-indicator-dot { color: #38a169; font-size: 11px; font-weight: bold; margin-bottom: 4px; }
        .dish-name-txt { font-size: 15px; font-weight: bold; color: #2d3748; }
        .dish-price-txt { font-size: 14px; font-weight: bold; color: #4a5568; margin: 4px 0; }
        .dish-desc-txt { font-size: 12px; color: #718096; line-height: 1.4; }
        .btn-add-item-to-cart { background: white; color: #f25c22; border: 1px solid #f25c22; padding: 6px 18px; font-weight: bold; border-radius: 4px; cursor: pointer; transition: all 0.2s; }
        
        .cart-sidebar-preview { width: 340px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; align-self: flex-start; position: sticky; top: 90px; }
        .cart-title-row { font-size: 16px; font-weight: bold; color: #2d3748; margin-bottom: 15px; display: flex; justify-content: space-between; }
        .cart-items-container { max-height: 220px; overflow-y: auto; margin-bottom: 15px; display: flex; flex-direction: column; gap: 8px; }
        .cart-line-item { display: flex; justify-content: space-between; align-items: center; font-size: 13px; padding: 8px 12px; background: #f8fafc; border-radius: 4px; }
        .cart-pricing-summary { border-top: 1px solid #edf2f7; padding-top: 12px; }
        .price-summary-row { display: flex; justify-content: space-between; font-size: 13px; color: #718096; margin-bottom: 6px; }
        .grand-total-row { display: flex; justify-content: space-between; font-size: 15px; font-weight: bold; color: #2d3748; margin-top: 10px; border-top: 1px solid #edf2f7; padding-top: 10px; }
        .empty-cart-msg { text-align: center; color: #a0aec0; padding: 20px 0; font-size: 13px; }
        
        .checkbox-container { display: flex; align-items: center; gap: 10px; background: #f0fdf4; border: 1px solid #bbf7d0; padding: 12px; border-radius: 6px; margin: 15px 0; cursor: pointer; }
        .checkbox-container input { width: 18px; height: 18px; cursor: pointer; accent-color: #16a34a; }
        .btn-checkout-order { width: 100%; background-color: #38a169; color: white; border: none; padding: 12px; border-radius: 4px; font-weight: bold; font-size: 14px; margin-top: 15px; cursor: pointer; text-align: center; }
        .btn-checkout-order:hover { background-color: #2f855a; }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="nav-left" onclick="window.location.href='index.php'" style="cursor:pointer; display:flex; align-items:center; gap:10px;">
            <img src="https://www.ecatering.irctc.co.in/assets/images/logo.png" alt="IRCTC Logo" style="height: 42px;">
            <div style="border-left: 2px solid #e2e8f0; padding-left: 12px;">
                <h1 style="font-size: 15px; color: #2d3748; font-weight: 700; margin:0;">eCatering</h1>
                <p style="font-size: 10px; color: #718096; margin:0;">Food Delivery on Track</p>
            </div>
        </div>
        <div class="nav-right">
            <a href="dashboard.php" class="nav-item">👤 Dashboard Panel</a>
            <a href="logout.php" class="nav-item" style="color: #f25c22; font-weight: bold;">Logout</a>
        </div>
    </div>

    <div class="split-booking-container">
        <!-- Sidebar Navigation Map -->
        <div class="station-sidebar">
            <div class="sidebar-title-header">STATIONS ROUTE MAP</div>
            <div class="station-list-wrapper">
                <?php foreach ($stations_data as $index => $station) { ?>
                    <div class="station-clickable-row <?php echo $index === 0 ? 'active-stop' : ''; ?>" 
                         onclick="switchStationTab(this, 'panel-<?php echo $station['code']; ?>')">
                        <div class="station-info-meta">
                            <h4><?php echo $station['name']; ?></h4>
                            <p>⏱️ <?php echo $station['time']; ?></p>
                        </div>
                        <span class="station-code-badge"><?php echo $station['code']; ?></span>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- Main Restaurant Panels and Menu Window Views -->
        <div class="restaurant-list-view">
            <div class="route-summary-strip">
                <span>Active Journey Route for PNR: <strong><?php echo htmlspecialchars($active_pnr); ?></strong></span>
                <button id="back-to-res-btn" onclick="closeMenuWindow()" style="display: none; background: none; border: none; color: #f25c22; font-weight: bold; cursor: pointer; font-size: 13px;">← Back to Restaurants</button>
            </div>

            <div id="restaurants-master-container">
                <?php foreach ($stations_data as $index => $station) { ?>
                    <div id="panel-<?php echo $station['code']; ?>" class="station-panel-content <?php echo $index === 0 ? 'active-panel' : ''; ?>">
                        <h2 style="font-size: 16px; color: #2d3748; margin-bottom: 15px;">Available Outlets at <?php echo $station['name']; ?> (<?php echo $station['code']; ?>)</h2>
                        
                        <?php foreach ($station['restaurants'] as $hotel) { 
                            $jsonMenu = htmlspecialchars(json_encode($hotel['menu']), ENT_QUOTES, 'UTF-8');
                        ?>
                            <div class="hotel-card-item">
                                <div class="hotel-left-details">
                                    <div class="hotel-icon-box">
                                        <img src="<?php echo $hotel['image']; ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>">
                                    </div>
                                    <div class="hotel-text-block">
                                        <h3><?php echo $hotel['name']; ?></h3>
                                        <p class="hotel-tag-line"><?php echo $hotel['cuisines']; ?></p>
                                        <span class="badge-rating"><?php echo $hotel['rating']; ?></span>
                                    </div>
                                </div>
                                <button class="btn-order-food" onclick="openMenuWindow('<?php echo addslashes($hotel['name']); ?>', '<?php echo $jsonMenu; ?>')">VIEW MENU</button>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>

            <!-- Single Split Basket Window Frame -->
            <div id="menu-cart-split-window" class="menu-cart-split-window">
                <div class="menu-items-list-block">
                    <div class="menu-header-box">
                        <h2 id="target-restaurant-name">Restaurant Menu</h2>
                    </div>
                    <div id="dish-items-injector"></div>
                </div>

                <!-- Sticky Summary Right Basket Block -->
                <div class="cart-sidebar-preview">
                    <div class="cart-title-row">
                        <span>Your Basket 🛒</span>
                        <span id="cart-count-badge" style="color:#f25c22; font-weight:bold;">0 Items</span>
                    </div>
                    
                    <div id="cart-items-injector" class="cart-items-container">
                        <div class="empty-cart-msg">Your food selection basket is empty.</div>
                    </div>

                    <div class="cart-pricing-summary">
                        <div class="price-summary-row"><span>Basket Subtotal</span><span id="summary-subtotal">₹0.00</span></div>
                        <div class="price-summary-row"><span>Catering IRCTC Tax (5%)</span><span id="summary-tax">₹0.00</span></div>
                        
                        <?php if (isset($_SESSION['user_id']) && $user_points > 0): ?>
                            <label class="checkbox-container" id="winbin-display-row">
                                <input type="checkbox" id="use_winbin_checkbox" onchange="calculateCartTotals()">
                                <div style="flex:1;">
                                    <span style="font-weight:600; font-size:13px; color:#16a34a;">♻️ Apply WinBin Discount</span>
                                    <div style="font-size:11px; color:#15803d; margin-top:1px;">Available: <?php echo $user_points; ?> Points</div>
                                </div>
                            </label>
                            <div class="price-summary-row" id="winbin-info-row" style="display:none; color:#16a34a; font-weight:600;">
                                <span>WinBin Wallet Discount</span><span id="summary-winbin">-₹0.00</span>
                            </div>
                        <?php else: ?>
                            <div class="checkbox-container" style="background:#f8fafc; border-color:#e2e8f0; cursor:default;">
                                <span style="font-size:12px; color:#718096;">♻️ No available WinBin reward balance.</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="grand-total-row"><span>Grand Payable Total</span><span id="summary-grandtotal">₹0.00</span></div>
                    </div>
                    
                    <form action="process_order.php" method="POST" style="margin-top:20px;">
                        <input type="hidden" id="points_deducted_input" name="points_deducted" value="0">
                        <input type="hidden" id="order_items_input" name="order_items_json" value="[]">
                        <input type="hidden" id="restaurant_name_input" name="restaurant_name" value="">
                        <input type="hidden" id="total_amount_input" name="total_amount" value="0">
                        <button type="submit" class="btn-checkout-order">CONFIRM & PLACE ORDER</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let cart = [];
        const availablePoints = <?php echo $user_points; ?>;

        function switchStationTab(clickedRow, targetPanelId) {
            closeMenuWindow(); 
            document.querySelectorAll('.station-clickable-row').forEach(row => row.classList.remove('active-stop'));
            document.querySelectorAll('.station-panel-content').forEach(panel => panel.classList.remove('active-panel'));
            clickedRow.classList.add('active-stop');
            document.getElementById(targetPanelId).classList.add('active-panel');
        }

        function openMenuWindow(restaurantName, menuJsonData) {
            document.getElementById('restaurants-master-container').style.display = 'none';
            document.getElementById('menu-cart-split-window').style.display = 'flex';
            document.getElementById('back-to-res-btn').style.display = 'block';
            document.getElementById('target-restaurant-name').innerText = restaurantName;

            const menuItems = JSON.parse(menuJsonData);
            const injector = document.getElementById('dish-items-injector');
            injector.innerHTML = ""; 

            menuItems.forEach(item => {
                const vegBadge = item.is_veg ? '🟢 Veg Item' : '🔴 Non-Veg Item';
                const colorStyle = item.is_veg ? 'color: #38a169;' : 'color: #e53e3e;';
                injector.innerHTML += `
                    <div class="dish-row-item">
                        <div class="dish-details-side">
                            <div class="veg-indicator-dot" style="${colorStyle}">${vegBadge}</div>
                            <div class="dish-name-txt">${item.name}</div>
                            <div class="dish-price-txt">₹${item.price}.00</div>
                            <div class="dish-desc-txt">${item.desc || 'Freshly cooked premium standard train meal box.'}</div>
                        </div>
                        <button type="button" class="btn-add-item-to-cart" style="padding:6px 14px; font-size:12px; background:white; color:#f25c22; border:1px solid #f25c22; font-weight:bold; border-radius:4px; cursor:pointer;" onclick="addToBasket('${item.id}', '${item.name.replace(/'/g, "\\'")}', ${item.price}, '${restaurantName.replace(/'/g, "\\'")}')">+ ADD</button>
                    </div>`;
            });
        }

        function closeMenuWindow() {
            document.getElementById('menu-cart-split-window').style.display = 'none';
            document.getElementById('back-to-res-btn').style.display = 'none';
            document.getElementById('restaurants-master-container').style.display = 'block';
        }

        function addToBasket(itemId, name, price, restaurant) {
            const currentRestaurantInput = document.getElementById('restaurant_name_input');
            if (cart.length > 0 && currentRestaurantInput.value !== restaurant) {
                if (!confirm("Your cart contains food from another restaurant. Empty cart and add this item instead?")) {
                    return;
                }
                cart = [];
            }
            
            currentRestaurantInput.value = restaurant;
            
            const existing = cart.find(i => i.id === itemId);
            if (existing) {
                existing.qty++;
            } else {
                cart.push({ id: itemId, name: name, price: price, qty: 1 });
            }
            
            renderCartUI();
        }

        function renderCartUI() {
            const container = document.getElementById('cart-items-injector');
            container.innerHTML = '';
            
            if (cart.length === 0) {
                container.innerHTML = '<div class="empty-cart-msg">Your food selection basket is empty.</div>';
                document.getElementById('restaurant_name_input').value = '';
                calculateCartTotals();
                return;
            }
            
            cart.forEach(item => {
                container.innerHTML += `
                    <div class="cart-line-item">
                        <span style="font-weight:500; font-size:13px;">${item.name} (x${item.qty})</span>
                        <span style="font-weight:600; font-size:13px; color:#2d3748;">₹${(item.price * item.qty).toFixed(2)}</span>
                    </div>`;
            });
            
            calculateCartTotals();
        }

        function calculateCartTotals() {
            let subtotal = 0;
            let totalItemsCount = 0;
            
            cart.forEach(item => {
                subtotal += (item.price * item.qty);
                totalItemsCount += item.qty;
            });
            
            let taxAmount = subtotal * 0.05;
            let finalDiscount = 0;
            
            const checkbox = document.getElementById('use_winbin_checkbox');
            if (checkbox && checkbox.checked) {
                if(document.getElementById('winbin-info-row')) {
                    document.getElementById('winbin-info-row').style.display = 'flex';
                }
                let rawPayable = subtotal + taxAmount;
                
                if (availablePoints >= rawPayable) {
                    finalDiscount = rawPayable;
                } else {
                    finalDiscount = availablePoints;
                }
                
                document.getElementById('summary-winbin').innerText = "-₹" + finalDiscount.toFixed(2);
                document.getElementById('points_deducted_input').value = Math.floor(finalDiscount);
            } else {
                if(document.getElementById('winbin-info-row')) {
                    document.getElementById('winbin-info-row').style.display = 'none';
                }
                finalDiscount = 0;
                document.getElementById('points_deducted_input').value = 0;
            }
            
            let grandTotal = (subtotal + taxAmount) - finalDiscount;
            
            document.getElementById('cart-count-badge').innerText = totalItemsCount + " Items";
            document.getElementById('summary-subtotal').innerText = "₹" + subtotal.toFixed(2);
            document.getElementById('summary-tax').innerText = "₹" + taxAmount.toFixed(2);
            document.getElementById('summary-grandtotal').innerText = "₹" + grandTotal.toFixed(2);
            
            document.getElementById('total_amount_input').value = grandTotal.toFixed(2);
            document.getElementById('order_items_input').value = JSON.stringify(cart);
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>