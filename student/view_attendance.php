<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch attendance records
$stmt = $conn->prepare("
    SELECT a.date, a.status, c.class_name 
    FROM attendance a 
    JOIN classes c ON a.class_id = c.id 
    WHERE a.student_id = ? 
    ORDER BY a.date DESC
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$attendance = [];
while ($row = $result->fetch_assoc()) {
    $attendance[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Attendance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(120deg, #f0f2f5, #dfe9f3);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }
        .logout-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            position: absolute;
            top: 20px;
            right: 20px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }


        .container {
            background-color: white;
            margin-top: 40px;
            width: 90%;
            max-width: 850px;
            border-radius: 15px;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
            padding: 30px;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            text-align: center;
            color: #1a1a1a;
            margin-bottom: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            overflow: hidden;
            border-radius: 10px;
        }

        th, td {
            padding: 14px 16px;
            text-align: center;
            transition: background-color 0.3s;
        }

        th {
            background-color: #0061ff;
            color: white;
            font-size: 16px;
        }

        tr:nth-child(even) {
            background-color: #f6f9ff;
        }

        tr:hover td {
            background-color: #edf3ff;
        }

        .present {
            color: #28a745;
            font-weight: bold;
        }

        .absent {
            color: #dc3545;
            font-weight: bold;
        }

        .status-icon {
            font-size: 18px;
            margin-right: 5px;
        }

        .no-records {
            text-align: center;
            font-size: 18px;
            color: #666;
            margin-top: 30px;
        }

        .back-btn {
            margin-top: 20px;
            display: inline-block;
            background-color: #0061ff;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .back-btn:hover {
            background-color: #004ed6;
        }
    </style>
</head>
<body>
<a href="../auth/logout.php" class="logout-btn">Logout</a>

    <div class="container">
        
        <h2>📋 My Attendance Record</h2>

        <?php if (count($attendance) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Class</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['date']); ?></td>
                            <td><?php echo htmlspecialchars($record['class_name']); ?></td>
                            <td class="<?php echo $record['status'] === 'Present' ? 'present' : 'absent'; ?>">
                                <i class="fa-solid <?php echo $record['status'] === 'Present' ? 'fa-circle-check' : 'fa-circle-xmark'; ?> status-icon"></i>
                                <?php echo $record['status']; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-records">No attendance records found.</p>
        <?php endif; ?>

        <a href="dashboard.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
    </div>

</body>
</html>
