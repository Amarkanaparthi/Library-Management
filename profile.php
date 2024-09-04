<?php
session_start();
include("database.php");

// Check if the user is logged in
if (!isset($_SESSION['userEmail'])) {
    header("Location: home.html");
    exit();
}

$userEmail = $_SESSION['userEmail'];

// Initialize user data
$userData = null;
$userType = null;

// Check in the Student table
$studentQuery = "SELECT * FROM Student WHERE email = '$userEmail'";
$studentResult = mysqli_query($conn, $studentQuery);

if (mysqli_num_rows($studentResult) > 0) {
    $userData = mysqli_fetch_assoc($studentResult);
    $userType = "Student";
} else {
    // Check in the Staff table
    $staffQuery = "SELECT * FROM Staff WHERE email = '$userEmail'";
    $staffResult = mysqli_query($conn, $staffQuery);

    if (mysqli_num_rows($staffResult) > 0) {
        $userData = mysqli_fetch_assoc($staffResult);
        $userType = "Staff";
    }
}

if (!$userData) {
    // If no user data is found in either table, redirect to home
    header("Location: home.html");
    exit();
}

// Fetch borrowed books for the logged-in user
$borrowedBooksQuery = "SELECT Books.book_id, Books.title, Books.author, Books.subject, Books.ifsc_code, BorrowedBooks.borrowed_date 
                       FROM BorrowedBooks 
                       JOIN Books ON BorrowedBooks.book_id = Books.book_id 
                       WHERE BorrowedBooks.user_email = '$userEmail'";
$borrowedBooksResult = mysqli_query($conn, $borrowedBooksQuery);
$borrowedBooks = mysqli_fetch_all($borrowedBooksResult, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="profilestyles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #333;
            color: white;
            padding: 15px 20px;
            text-align: center;
        }
        .home-btn {
            color: #fff;
            text-decoration: none;
            background-color: #007bff;
            padding: 10px 15px;
            border-radius: 5px;
            float: right;
            margin-right: 20px;
            margin-top: -35px;
        }
        main {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .no-data {
            text-align: center;
            font-style: italic;
        }
    </style>
</head>
<body>
    <header>
        <h1>User Profile</h1>
        <!-- Home Button -->
        <a href="home.php" class="home-btn">Home</a>
    </header>
    <main>
        <h2>Welcome, <?php echo htmlspecialchars($userData['name']); ?>!</h2>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($userData['email']); ?></p>
        <p><strong>User Type:</strong> <?php echo htmlspecialchars($userType); ?></p>

        <?php if ($userType === 'Staff'): ?>
            <p><strong>Staff ID:</strong> <?php echo htmlspecialchars($userData['staffid']); ?></p>
            <p><strong>Department:</strong> <?php echo htmlspecialchars($userData['department']); ?></p>
        <?php elseif ($userType === 'Student'): ?>
            <p><strong>Roll Number:</strong> <?php echo htmlspecialchars($userData['rollNumber']); ?></p>
            <p><strong>Department:</strong> <?php echo htmlspecialchars($userData['department']); ?></p>
            <p><strong>Year:</strong> <?php echo htmlspecialchars($userData['year']); ?></p>
        <?php endif; ?>

        <h2>Your Borrowed Books</h2>
        <?php if (count($borrowedBooks) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Book ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Subject</th>
                        <th>IFSC Code</th>
                        <th>Borrowed Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($borrowedBooks as $book): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['book_id']); ?></td>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td><?php echo htmlspecialchars($book['subject']); ?></td>
                            <td><?php echo htmlspecialchars($book['ifsc_code']); ?></td>
                            <td><?php echo htmlspecialchars($book['borrowed_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">You have no borrowed books.</p>
        <?php endif; ?>
    </main>
</body>
</html>
