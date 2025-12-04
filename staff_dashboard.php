<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fingerprint_db";

$conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        $error = "Database connection failed";
    }

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Staff';

$staffDetails = null;
if (isset($_SESSION['staff_id'])) {
    $result = $conn->query("SELECT username FROM users WHERE id = " . intval($_SESSION['staff_id']));
    if ($result && $row = $result->fetch_assoc()) {
        $username = $row['username'];
    }
}

$total_senior = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM senior_list");
if ($result && $row = $result->fetch_assoc()) {
    $total_senior = $row['count'] ?? 0;
}

$new_seniors_today = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM senior_list WHERE DATE(created_at) = CURDATE()");
if ($result && $row = $result->fetch_assoc()) {
    $new_seniors_today = $row['count'] ?? 0;
}

$recentActivities = [];
$sql = "
  SELECT
    id,
    username,
    COALESCE(details,
      CONCAT(COALESCE(username, 'Staff'), ' ', action)
    ) AS activity,
    created_at AS timestamp
  FROM user_activities
  ORDER BY created_at DESC
  LIMIT 5
";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ( $row = $result->fetch_assoc()) {
        $recentActivities[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BBSAISC</title>
    <link rel="stylesheet" href="assets/style/admin_dashboard.css" />
    <link rel="stylesheet" href="assets/style/staff_dashboard.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="icon" type="webp" href="assets/images/logo.webp" />
</head>

<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="assets/images/logo.webp" alt="Logo" class="sidebar-logo" />
            <p>
                Biometric Based Authentication Identification for Senior Citizen
            </p>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="#" class="active" data-target="dashboard"><i class="fas fa-home"></i> Home</a>
            </li>
            <li>
                <a href="#" data-target="process"><i class="fas fa-receipt"></i> Process Discount</a>
            </li>
            <li>
                <a href="#" data-target="logs"><i class="fas fa-history"></i> Staff Logs</a>
            </li>
            <li>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="topbar">
            <button class="hamburger" id="hamburger">
                <i class="fas fa-bars"></i>
            </button>
            <h1>Staff Dashboard</h1>
        </div>

        <div class="content" id="dashboard">
             <h2>Welcome, <strong><?= htmlspecialchars($username); ?></strong></h2>
            <p>
                This dashboard allows you to process senior citizen discounts and manage transactions.
            </p>
            <div class="hero-banner">
                <h1>Senior Citizen Discount</h1>
                <p>5% off BNPC • Weekly Cap: PHP 125.00</p>
            </div>

            <div class="stats-cards">
                <div class="card">
                    <i class="fas fa-money-bill-wave"></i>
                    <div>
                        <h3>PHP 0.00</h3>
                        <p>Today's Sales</p>
                    </div>
                </div>
                <div class="card">
                    <i class="fas fa-gift"></i>
                    <div>
                        <h3>PHP 0.00</h3>
                        <p>Discounts Given</p>
                    </div>
                </div>
                <div class="card">
                    <i class="fas fa-users"></i>
                    <div>
                        <h3><?php echo $total_senior; ?></h3>
                        <p>Seniors Served</p>
                    </div>
                </div>
                <div class="card">
                    <i class="fas fa-file-invoice"></i>
                    <div>
                        <h3>0</h3>
                        <p>Transactions</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="content" id="process">
            <h2>Process Senior Discount</h2>
            
            <div class="discount-form-section">
                <h3>Enter Discount Amount</h3>
                <form id="discountForm">
                    <div class="form-group">
                        <label for="discountAmount">Discount Amount (PHP)</label>
                        <input type="number" id="discountAmount" name="discountAmount" placeholder="Enter discount amount" min="0" step="0.01" required>
                        <small>Mininum: PHP 50.00</small>
                    </div>
                    <button type="submit" class="btn-primary">Proceed</button>
                </form>
            </div>

            <div id="verificationSection" style="display:none;">
                <h3>Verification Type</h3>
                <p>Who is claiming the discount?</p>
                
                <div class="verification-options">
                    <button id="btnSeniorVerify" class="verification-btn">
                        <i class="fas fa-user"></i>
                        <span>Senior Citizen<br><small>Verify Fingerprint</small></span>
                    </button>
                    <button id="btnGuardianVerify" class="verification-btn">
                        <i class="fas fa-user-shield"></i>
                        <span>Guardian<br><small>Check Information</small></span>
                    </button>
                </div>

                <div id="seniorVerificationSection" style="display:none; margin-top:30px;">
                    <h3>Senior Citizen Fingerprint Verification</h3>
                    <form id="seniorVerifyForm">
                        <div class="form-group">
                            <label for="seniorFullName">Senior Citizen Name</label>
                            <input type="text" id="seniorFullName" name="seniorFullName" placeholder="Enter senior citizen full name" required>
                        </div>
                        <div class="verification-content">
                            <p>Please have the senior citizen scan their fingerprint for verification.</p>
                            <button type="button" id="btnStartFingerprint" class="btn-scan">
                                <i class="fas fa-fingerprint"></i> Start Fingerprint Scan
                            </button>
                            <div id="fingerprintResult" style="display:none; margin-top:15px; padding:15px; border-radius:5px;"></div>
                        </div>
                    </form>
                </div>

                <div id="guardianVerificationSection" style="display:none; margin-top:30px;">
                    <h3>Guardian Information Verification</h3>
                    <form id="guardianForm">
                        <div class="form-group">
                            <label for="guardianName">Guardian Name</label>
                            <input type="text" id="guardianName" name="guardianName" placeholder="Enter guardian full name" required>
                        </div>
                        <div class="form-group">
                            <label for="guardianContact">Guardian Contact Number</label>
                            <input type="tel" id="guardianContact" name="guardianContact" placeholder="Enter contact number" required>
                        </div>
                        <div class="form-group">
                            <label for="seniorName">Senior Citizen Name</label>
                            <input type="text" id="seniorName" name="seniorName" placeholder="Enter senior citizen name" required>
                        </div>
                        <div class="form-group">
                            <label for="relationship">Relationship to Senior</label>
                            <select id="relationship" name="relationship" required>
                                <option value="">Select relationship</option>
                                <option value="Son/Daughter">Son/Daughter</option>
                                <option value="Spouse">Spouse</option>
                                <option value="Grandchild">Grandchild</option>
                                <option value="Sibling">Sibling</option>
                                <option value="Caregiver">Caregiver</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary">Verify Guardian & Process Discount</button>
                    </form>
                </div>

                <div style="margin-top:20px;">
                    <button id="btnBackToForm" class="btn-secondary">Back</button>
                </div>
            </div>
        </div>

        <div class="content" id="logs">
            <h2>Staff Logs</h2>
            <div class="recent-activity">
                <h3>Recent Activity</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Activity</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentActivities)): ?>
                        <?php foreach ($recentActivities as $activity): ?>
                        <tr>
                            <td><?= htmlspecialchars($activity['activity']); ?></td>
                            <td><?= date('F d, Y', strtotime($activity['timestamp'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="2">No recent activities found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="assets/script/admin_dashboard.js"></script>
    <script>
        // Discount Form Logic
        const discountForm = document.getElementById('discountForm');
        const verificationSection = document.getElementById('verificationSection');
        const btnSeniorVerify = document.getElementById('btnSeniorVerify');
        const btnGuardianVerify = document.getElementById('btnGuardianVerify');
        const btnBackToForm = document.getElementById('btnBackToForm');
        const seniorVerificationSection = document.getElementById('seniorVerificationSection');
        const guardianVerificationSection = document.getElementById('guardianVerificationSection');
        const btnStartFingerprint = document.getElementById('btnStartFingerprint');
        const guardianForm = document.getElementById('guardianForm');

        let currentDiscount = 0;

        // Step 1: Submit discount amount
        discountForm.addEventListener('submit', (e) => {
            e.preventDefault();
            currentDiscount = parseFloat(document.getElementById('discountAmount').value);
            
            if (currentDiscount > 125.00) {
                alert('Discount exceeds weekly cap of PHP 125.00');
                return;
            }
            
            discountForm.parentElement.style.display = 'none';
            verificationSection.style.display = 'block';
        });

        // Step 2: Choose verification type
        btnSeniorVerify.addEventListener('click', () => {
            seniorVerificationSection.style.display = 'block';
            guardianVerificationSection.style.display = 'none';
        });

        btnGuardianVerify.addEventListener('click', () => {
            guardianVerificationSection.style.display = 'block';
            seniorVerificationSection.style.display = 'none';
        });

        // Step 3: Back button
        btnBackToForm.addEventListener('click', () => {
            discountForm.parentElement.style.display = 'block';
            verificationSection.style.display = 'none';
            seniorVerificationSection.style.display = 'none';
            guardianVerificationSection.style.display = 'none';
            discountForm.reset();
        });

        // Step 4: Fingerprint verification
        btnStartFingerprint.addEventListener('click', () => {
            const seniorName = document.getElementById('seniorFullName').value;
            
            if (!seniorName.trim()) {
                alert('Please enter the senior citizen name');
                return;
            }
            
            alert('Fingerprint scan initiated. (Integration with biometric hardware needed)');
            // TODO: Integrate with actual fingerprint scanner API
            const resultDiv = document.getElementById('fingerprintResult');
            resultDiv.style.display = 'block';
            resultDiv.className = 'success';
            resultDiv.innerHTML = '<strong>✓ Fingerprint Verified</strong><br>Senior: ' + seniorName + '<br>Discount: PHP ' + currentDiscount.toFixed(2);
            
            setTimeout(() => {
                alert('Discount of PHP ' + currentDiscount.toFixed(2) + ' applied successfully to ' + seniorName + '!');
                btnBackToForm.click();
            }, 2000);
        });

        // Step 5: Guardian verification
        guardianForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const guardianName = document.getElementById('guardianName').value;
            const seniorName = document.getElementById('seniorName').value;
            
            alert('Guardian Information Verified:\n\nGuardian: ' + guardianName + '\nSenior: ' + seniorName + '\n\nDiscount of PHP ' + currentDiscount.toFixed(2) + ' applied successfully!');
            
            // TODO: Send data to backend for processing
            guardianForm.reset();
            btnBackToForm.click();
        });
    </script>
