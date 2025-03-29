<?php
include 'dbConfig.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];
$shop_name = $_SESSION['shop_name'] ?? '';

$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Get current page number, default to 1
$limit = 5; // Number of records per page
$offset = ($page - 1) * $limit; // Calculate offset

$sql = "SELECT id, type, amount, details, created_at FROM transactions WHERE user_id = ?";
$parameters = [$user_id]; // Start with user_id as a parameter
$types = "i"; // Initialize types with 'i' for integer

if (!empty($search)) {
    if (is_numeric($search)) {
        $sql .= " AND amount = ?";
        $types .= "d"; // Add 'double' type for amount
        $parameters[] = (float) $search;
    } else {
        $sql .= " AND details LIKE ?";
        $types .= "s"; // Add 'string' type for details
        $parameters[] = "%" . $search . "%";
    }
}

// Modify SQL to limit results
$sql .= " ORDER BY created_at DESC LIMIT ?, ?";
$parameters[] = $offset; // Offset
$parameters[] = $limit; // Limit
$types .= "ii"; // Add 'i' for offset and 'i' for limit

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$parameters); // Bind all parameters dynamically
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];
$income = $expense = 0;

while ($row = $result->fetch_assoc()) {
    if ($row['type'] === 'income') {
        $income += $row['amount'];
    } else {
        $expense += $row['amount'];
    }
    $transactions[] = $row;
}

$balance = $income - $expense;

$stmt->close();

// Get total number of records for pagination
$totalQuery = "SELECT COUNT(*) as total FROM transactions WHERE user_id = ?";
$totalStmt = $conn->prepare($totalQuery);
$totalStmt->bind_param("i", $user_id);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit); // Calculate total pages

$totalStmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= htmlspecialchars($shop_name) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <div class="header-left">
        <h1>DigiKhata</h1>
    </div>
    <div class="header-right">
        <a href="dashboard.php">Home</a>
        <a href="logout.php">Logout</a>
    </div>
</header>

<div class="container">
    <h2><?= htmlspecialchars($shop_name) ?></h2>

    <div class="search-container">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search Records" value="<?= htmlspecialchars($search) ?>" required>
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="balance-summary">
        <span>Cash In: <?= number_format($income, 2) ?></span>
        <span>Cash Out: <?= number_format($expense, 2) ?></span>
        <span>Net Balance: <?= number_format($balance, 2) ?></span>
    </div>

    <div class="actions">
        <button class="green" onclick="showEntryForm('income')">Cash In</button>
        <button class="green" onclick="showEntryForm('expense')">Cash Out</button>
    </div>

    <table>
        <thead>
        <tr>
            <th>Date & Time</th>
            <th>Details</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Balance</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php $runningBalance = 0; ?>
        <?php foreach ($transactions as $transaction): ?>
            <?php $runningBalance += ($transaction['type'] == 'income' ? $transaction['amount'] : -$transaction['amount']); ?>
            <tr>
                <td><?= htmlspecialchars($transaction['created_at']) ?></td>
                <td><?= htmlspecialchars($transaction['details']) ?></td>
                <td><?= ucfirst($transaction['type']) ?></td>
                <td><?= number_format($transaction['amount'], 2) ?></td>
                <td><?= number_format($runningBalance, 2) ?></td>
                <td>
                    <form method="post" action="delete_entry.php" onsubmit="return confirm('Are you sure?');">
                        <input type="hidden" name="transaction_id" value="<?= $transaction['id'] ?>">
                        <button class="delete-btn" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&search=<?= htmlspecialchars($search) ?>">Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>" class="<?= ($i === $page) ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>&search=<?= htmlspecialchars($search) ?>">Next</a>
        <?php endif; ?>
    </div>
</div>

<div id="entryFormModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEntryForm()">&times;</span>
        <h3 id="modal-title">Add Entry</h3>
        <form id="entryForm" method="post" action="add_entry.php">
            <input type="hidden" name="type" id="entryType">
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" name="date" id="entryDate" required>
            </div>
            <div class="form-group">
                <label for="time">Time:</label>
                <input type="time" name="time" id="entryTime" required>
            </div>
            <div class="form-group">
                <label for="amount">Amount:</label>
                <input type="number" name="amount" id="entryAmount" required>
            </div>
            <div class="form-group">
                <label for="details">Remarks:</label>
                <input type="text" name="details">
            </div>
            <button type="submit">Add Entry</button>
        </form>
    </div>
</div>

<footer>
    <div class="footer-content">
        <p>&copy; <?= date("Y") ?> DigiKhata. All rights reserved.</p>
    </div>
</footer>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById('entryFormModal').style.display = 'none';
});

function showEntryForm(type) {
    document.getElementById('entryType').value = type;

    const now = new Date();
    const formattedDate = now.toISOString().split('T')[0];
    const formattedTime = now.getHours().toString().padStart(2, '0') + ":" + 
                          now.getMinutes().toString().padStart(2, '0');

    document.getElementById('entryDate').value = formattedDate;
    document.getElementById('entryTime').value = formattedTime;
    document.getElementById('entryFormModal').style.display = 'flex';

    setTimeout(() => document.getElementById('entryAmount').focus(), 100);
}

function closeEntryForm() {
    document.getElementById('entryFormModal').style.display = 'none';
}
</script>

</body>
</html>
