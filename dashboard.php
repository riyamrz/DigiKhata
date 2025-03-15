<?php
include 'dbConfig.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Fetch transactions
$stmt = $conn->prepare("SELECT type, amount, details, created_at FROM transactions WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$income = 0;
$expense = 0;
$transactions = [];

while ($row = $result->fetch_assoc()) {
    if ($row['type'] == 'income') {
        $income += $row['amount'];
    } else {
        $expense += $row['amount'];
    }
    $transactions[] = $row;
}

$net_balance = $income - $expense;

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="header-left">
            <h1>ABC Shop Name</h1>
        </div>
        <div class="header-right">
            <span><?php echo $full_name; ?></span>
            <div class="dropdown">
                <button class="dropbtn">▼</button>
                <div class="dropdown-content">
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </header>
    <div class="container">
        <h2><?php echo $_SESSION['shop_name']; ?></h2>
        <div class="filters">
            <select>
                <option>Duration: All Time</option>
                <!-- Add more options as needed -->
            </select>
            <select>
                <option>Types: All</option>
                <!-- Add more options as needed -->
            </select>
            <select>
                <option>Contacts: All</option>
                <!-- Add more options as needed -->
            </select>
            <select>
                <option>Members: All</option>
                <!-- Add more options as needed -->
            </select>
            <select>
                <option>Payment Modes: All</option>
                <!-- Add more options as needed -->
            </select>
            <select>
                <option>Categories: All</option>
                <!-- Add more options as needed -->
            </select>
            <input type="text" placeholder="Search by remark or amount">
        </div>
        <div class="balance-summary">
            <span>Cash In: <?php echo $income; ?></span>
            <span>Cash Out: <?php echo $expense; ?></span>
            <span>Net Balance: <?php echo $net_balance; ?></span>
        </div>
        <div class="actions">
            <button onclick="showEntryForm('credit')">Cash In</button>
            <button onclick="showEntryForm('debit')">Cash Out</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Details</th>
                    <th>Mode</th>
                    <th>Amount</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?php echo $transaction['created_at']; ?></td>
                        <td><?php echo $transaction['details']; ?></td>
                        <td><?php echo $transaction['type'] == 'income' ? 'Cash' : 'Cash'; ?></td>
                        <td><?php echo $transaction['amount']; ?></td>
                        <td><?php echo $transaction['type'] == 'income' ? $income : $expense; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Entry Form Modal -->
    <div id="entryFormModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEntryForm()">&times;</span>
            <form id="entryForm" method="post" action="add_entry.php">
                <input type="hidden" name="type" id="entryType">
                <label for="date">Date:</label>
                <input type="date" name="date" required>
                <label for="time">Time:</label>
                <input type="time" name="time" required>
                <label for="amount">Amount:</label>
                <input type="number" name="amount" required>
                <label for="details">Remarks:</label>
                <input type="text" name="details">
                <button type="submit">Add Entry</button>
            </form>
        </div>
    </div>

    <script>
    function showEntryForm(type) {
        // Set the correct type values
        if (type === 'credit') {
            document.getElementById('entryType').value = 'income';
        } else if (type === 'debit') {
            document.getElementById('entryType').value = 'expense';
        }
        document.getElementById('entryFormModal').style.display = 'block';
    }

    function closeEntryForm() {
        document.getElementById('entryFormModal').style.display = 'none';
    }
</script>
</body>
</html>