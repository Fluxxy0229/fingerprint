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

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

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
    COALESCE(details,
      CONCAT(COALESCE(username, 'Admin'), ' ', action)
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="icon" type="webp" href="assets/images/logo.webp" />
</head>

<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="assets/images/logo.webp" alt="BBSAISC Logo" class="sidebar-logo" />
            <p>
                Biometric Based Authentication Identification for Senior Citizen
            </p>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="#" class="active" data-target="home"><i class="fas fa-home"></i> Home</a>
            </li>
            <li>
                <a href="#" data-target="add"><i class="fas fa-user-plus"></i> Add Senior Citizen</a>
            </li>
            <li>
                <a href="#" data-target="list"><i class="fas fa-list"></i> View List</a>
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
            <h1>Admin Dashboard</h1>
        </div>

        <div class="content" id="home">
            <h2>Welcome, <strong><?= htmlspecialchars($username); ?></strong></h2>
            <p>
                This dashboard allows you to register new senior citizens, update
                records, and monitor the current list.
            </p>
            <div class="stats-cards">
                <div class="card">
                    <i class="fas fa-user"></i>
                    <div>
                        <h3><?php echo $total_senior; ?></h3>
                        <p>Total Seniors</p>
                    </div>
                </div>
                <div class="card">
                    <i class="fas fa-user-plus"></i>
                    <div>
                        <h3><?php echo $new_seniors_today; ?></h3>
                        <p>New Seniors Today</p>
                    </div>
                </div>
            </div>
            <div class="quick-actions">
                <button id="btnAdd">
                    <i class="fas fa-user-plus"></i> Add New Senior Citizen
                </button>
                <button id="btnViewList"><i class="fas fa-list"></i> View List</button>
            </div>
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
                            <td>Admin added <?= htmlspecialchars($activity['activity']); ?></td>
                            <td><?= date('F d, Y', strtotime($activity['timestamp'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="2" style="text-align:center;">No recent activities found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="content" id="add" style="display: none">
            <h2>Add New Senior Citizen</h2>
            <form id="seniorForm" method="POST" action="assets/backend/add_senior.php">
                <input type="text" name="full_name" placeholder="Full Name" required />
                <input type="text" name="age" placeholder="Age" required />
                <input type="text" name="address" placeholder="Address" required />
                <input type="tel" name="contact" placeholder="Contact Number" required />
                <input type="date" name="birthdate" placeholder="Date of Birth" required />

                <select name="gender" required>
                    <option value="">Choose Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Prefer not to say">Prefer not to say</option>
                </select>

                <select name="civil_status" required>
                    <option value="">Choose Civil Status</option>
                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                    <option value="Separated">Separated</option>
                    <option value="Widowed">Widowed</option>
                </select>

                <button type="button" id="enrollBio">Register Fingerprint</button>

                <button type="submit">Register</button>
                </form>
                <div id="confirmModal" class="modal" style="display:none;">
                    <div class="modal-content">
                        <h3>Confirm Registration</h3>
                        <p>Are you sure you want to register this senior citizen?</p>
                        <div class="modal-actions">
                            <button id="confirmSubmit" class="confirm">Yes, Register</button>
                            <button id="cancelSubmit" class="cancel">Cancel</button>
                        </div>
                    </div>
                </div>
        </div>

        <div class="content" id="list" style="display: none">
            <h2>Senior Citizens List</h2>
            <div class="product-tools">
                <input type="text" id="searchSeniorInput" placeholder="Search senior...">
            </div>
            <br>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="seniorTableBody">
                </tbody>
            </table>
        </div>
    </div>

    <script src="assets/script/admin_dashboard.js"></script>

    <script>
    async function fetchSeniors(search = '') {
        const response = await fetch(`assets/backend/view_senior_list.php?search=${encodeURIComponent(search)}`, {
            credentials: 'same-origin'
        });
        if (response.status === 401) {
            const tbody = document.getElementById('seniorTableBody');
            tbody.innerHTML =
                '<tr><td colspan="5" style="text-align:center;">Not authenticated. Please login.</td></tr>';
            return;
        }
        const seniors = await response.json();
        const tbody = document.getElementById('seniorTableBody');
        tbody.innerHTML = '';
        if (!seniors || seniors.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No seniors found.</td></tr>';
            return;
        }
        seniors.forEach(senior => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                    <td>${senior.id}</td>
                    <td>${senior.fullname}</td>
                    <td>${senior.age}</td>
                    <td>${senior.address}</td>
                    <td>
                        <a href="assets/backend/edit_senior.php?id=${senior.id}" class="editBtn"><i class="fa fa-edit" aria-hidden="true"></i> Edit</a>
                        <a href="assets/backend/delete_senior.php?id=${senior.id}" class="deleteBtn"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>
                    </td>
                `;
            tbody.appendChild(tr);
        });
    }

    document.getElementById('searchSeniorInput').addEventListener('input', (e) => {
        fetchSeniors(e.target.value);
    });

    fetchSeniors();
    </script>
</body>

</html>