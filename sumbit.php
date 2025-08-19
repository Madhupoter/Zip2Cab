<?php
// Enhanced Booking Processor
header('Content-Type: application/json');
error_reporting(E_ALL); ini_set('display_errors', 0);

// Configuration
define('ADMIN_EMAIL', 'zip2cab@gmail.com');
define('COMPANY_NAME', 'Zip2Cab');
define('COMPANY_PHONE', '+91 91641 09403');
define('COMPANY_ADDRESS', 'Whitefield, Bengaluru-560066, India');
define('RECAPTCHA_SECRET', '6Ldo_KErAAAAAKnjExK0x7rjuEmoj-to6_6hlxwy');

// Utility Functions
function clean($data) { return htmlspecialchars(trim(stripslashes($data))); }
function isValidEmail($email) { return filter_var($email, FILTER_VALIDATE_EMAIL); }
function isValidPhone($phone) { return preg_match('/^[6-9]\d{9}$/', preg_replace('/[\s\-\+\(\)]/', '', $phone)); }

function verifyRecaptcha($response) {
    if (!$response) return false;
    $data = http_build_query([
        'secret' => RECAPTCHA_SECRET,
        'response' => $response,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
    ]);
    $context = stream_context_create(['http' => [
        'method' => 'POST',
        'header' => 'Content-type: application/x-www-form-urlencoded',
        'content' => $data
    ]]);
    $result = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
    return $result ? json_decode($result, true)['success'] ?? false : false;
}

function getDisplayNames($type, $value) {
    $names = [
        'plan' => ['airport' => 'Airport Taxi', 'local' => 'Local Taxi', 'outstation' => 'Out Station'],
        'route' => ['city-to-airport' => 'City to Airport', 'airport-to-city' => 'Airport to City', 'pickup-drop' => 'Pickup & Drop', 'one-way' => 'One Way'],
        'vehicle' => ['sedan' => 'Sedan', 'suv' => 'SUV', 'luxury' => 'Luxury Car', 'minibus' => 'Mini Bus', 'tt' => 'TT (Tempo Traveller)']
    ];
    return $names[$type][$value] ?? ucfirst($value);
}

function generateEmailHTML($isAdmin, $data, $bookingId) {
    $headerColor = $isAdmin ? '#2c3e50' : '#27ae60';
    $title = $isAdmin ? '🚖 New Taxi Booking' : ' Booking Confirmed!';
    $greeting = $isAdmin ? '🚖 New Booking Alert' : "Dear {$data['name']},";
    
    $locationDetails = '';
    if ($data['plan'] === 'airport') {
        $label = ($data['route'] === 'city-to-airport') ? 'Pickup' : 'Drop';
        $locationDetails = "$label: {$data['pickup_location']}";
    } elseif ($data['plan'] === 'local') {
        $locationDetails = "Pickup: {$data['pickup_location']}<br>Drop: {$data['drop_location']}";
    } elseif ($data['plan'] === 'outstation') {
        $locationDetails = "Destination: {$data['destination']}";
    }
    
    $specialReq = $data['message'] ? "<h3>Special Requirements</h3><div style='background:#ecf0f1;padding:15px;border-radius:5px;margin:10px 0'>{$data['message']}</div>" : '';
    
    $actionAlert = $isAdmin ? "<div style='background:#e74c3c;color:white;padding:15px;border-radius:5px;margin:20px 0'><strong> ACTION REQUIRED:</strong> Contact customer within 30 minutes</div>" : 
                              "<div style='background:#3498db;color:white;padding:15px;border-radius:5px;margin:20px 0'><h3>  What's Next?</h3><ul><li>  We'll contact you within 30 minutes</li><li>  Driver details 1 hour before journey</li><li>  Call " . COMPANY_PHONE . " for assistance</li></ul></div>";
    
    return "<!DOCTYPE html><html><head><meta charset='UTF-8'><style>
    body{font-family:Arial,sans-serif;line-height:1.6;color:#333;margin:0;padding:0}
    .container{max-width:600px;margin:0 auto;background:#f9f9f9;padding:20px}
    .header{background:$headerColor;color:white;padding:20px;text-align:center;border-radius:5px 5px 0 0}
    .content{background:white;padding:20px;margin:20px 0;border-radius:5px}
    .booking-id{background:#e74c3c;color:white;padding:10px;border-radius:5px;font-weight:bold;display:inline-block}
    .table{width:100%;border-collapse:collapse;margin:15px 0}
    .table th,.table td{padding:10px;border:1px solid #ddd;text-align:left}
    .table th{background:#34495e;color:white}
    .highlight{background:#f39c12;color:white;padding:5px 10px;border-radius:3px}
    .footer{background:#34495e;color:white;padding:15px;text-align:center;font-size:12px;border-radius:0 0 5px 5px}
    </style></head><body><div class='container'>
    <div class='header'><h1>$title</h1><p>$greeting</p><div class='booking-id'>Booking ID: $bookingId</div></div>
    <div class='content'>
    <h2>Customer Details</h2>
    <table class='table'><tr><th>Name</th><td>{$data['name']}</td></tr><tr><th>Email</th><td>{$data['email']}</td></tr><tr><th>Phone</th><td>{$data['phone']}</td></tr></table>
    <h2>Booking Details</h2>
    <table class='table'>
    <tr><th>Service</th><td>" . getDisplayNames('plan', $data['plan']) . "</td></tr>
    <tr><th>Route</th><td>" . getDisplayNames('route', $data['route']) . "</td></tr>
    <tr><th>Vehicle</th><td>" . getDisplayNames('vehicle', $data['vehicle']) . "</td></tr>
    <tr><th>Date</th><td>{$data['formatted_date']}</td></tr>
    <tr><th>Time</th><td>{$data['booking_time']}</td></tr>
    <tr><th>Amount</th><td class='highlight'>₹{$data['price']}</td></tr>
    </table>
    <h2>Location</h2><p><strong>$locationDetails</strong></p>
    $specialReq $actionAlert
    </div>
    <div class='footer'><p>Booking processed: " . date('Y-m-d H:i:s') . "</p><p>" . COMPANY_NAME . " - " . COMPANY_ADDRESS . "</p></div>
    </div></body></html>";
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method');
    
    // Verify reCAPTCHA
    if (!verifyRecaptcha($_POST['g-recaptcha-response'] ?? '')) {
        throw new Exception('reCAPTCHA verification failed');
    }
    
    // Collect and validate data
    $data = [
        'name' => clean($_POST['name'] ?? ''),
        'email' => clean($_POST['email'] ?? ''),
        'phone' => clean($_POST['phone'] ?? ''),
        'message' => clean($_POST['message'] ?? ''),
        'plan' => clean($_POST['plan'] ?? ''),
        'route' => clean($_POST['route'] ?? ''),
        'vehicle' => clean($_POST['vehicle'] ?? ''),
        'price' => clean($_POST['price'] ?? ''),
        'booking_date' => clean($_POST['booking_date'] ?? ''),
        'booking_time' => clean($_POST['booking_time'] ?? '')
    ];
    
    // Location data based on plan
    if ($data['plan'] === 'airport') {
        $data['pickup_location'] = clean($_POST['pickup_location'] ?? '');
    } elseif ($data['plan'] === 'local') {
        $data['pickup_location'] = clean($_POST['local_pickup'] ?? '');
        $data['drop_location'] = clean($_POST['local_drop'] ?? '');
    } elseif ($data['plan'] === 'outstation') {
        $data['destination'] = clean($_POST['outstation_destination'] ?? '');
    }
    
    // Validation
    $errors = [];
    if (strlen($data['name']) < 2) $errors[] = 'Name too short';
    if (!isValidEmail($data['email'])) $errors[] = 'Invalid email';
    if (!isValidPhone($data['phone'])) $errors[] = 'Invalid phone number';
    if (!$data['plan'] || !$data['route'] || !$data['vehicle'] || !$data['price']) $errors[] = 'Incomplete booking details';
    if (!$data['booking_date'] || !$data['booking_time']) $errors[] = 'Date/time required';
    
    // Location validation
    if ($data['plan'] === 'airport' && !$data['pickup_location']) $errors[] = 'Location required';
    if ($data['plan'] === 'local' && (!$data['pickup_location'] || !$data['drop_location'])) $errors[] = 'Pickup/drop locations required';
    if ($data['plan'] === 'outstation' && !$data['destination']) $errors[] = 'Destination required';
    
    if ($errors) throw new Exception(implode(', ', $errors));
    
    // Generate booking ID and format date
    $bookingId = strtoupper(substr($data['plan'], 0, 3)) . date('Ymd') . rand(1000, 9999);
    $data['formatted_date'] = date('l, F j, Y', strtotime($data['booking_date']));
    
    // Send Emails
    $adminHtml = generateEmailHTML(true, $data, $bookingId);
    $customerHtml = generateEmailHTML(false, $data, $bookingId);
    
    $headers = "From: " . COMPANY_NAME . " <" . ADMIN_EMAIL . ">\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";
    
    $adminSent = mail(ADMIN_EMAIL, " 🚖 New Booking - $bookingId", $adminHtml, $headers);
    $customerSent = mail($data['email'], "🚖 Booking Confirmed - $bookingId | " . COMPANY_NAME, $customerHtml, $headers);
    
    // Log booking with exact timestamp
    $logEntry = date('Y-m-d H:i:s') . "|$bookingId|{$data['name']}|{$data['email']}|{$data['phone']}|" . getDisplayNames('plan', $data['plan']) . "|â‚¹{$data['price']}\n";
    file_put_contents('bookings.log', $logEntry, FILE_APPEND | LOCK_EX);
    
    // Response
    $response = [
        'success' => true,
        'message' => 'Booking confirmed successfully!',
        'booking_id' => $bookingId,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    if (!$adminSent || !$customerSent) {
        $response['warning'] = 'Email delivery issue';
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>