<?php
// Start the session
session_start();

// Include the database connection file
include("database.php");

// Check if the user is logged in
if (!isset($_SESSION['userEmail'])) {
    // Redirect to home page if no user is logged in
    header("Location: home.php");
    exit();
}

// Handle book return
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['return_borrowed_id'])) {
    $borrowed_id = $_POST['return_borrowed_id'];
    $user_email = $_SESSION['userEmail'];

    // Check if the user has borrowed this book
    $query = "SELECT book_id FROM BorrowedBooks WHERE borrowed_id = '$borrowed_id' AND user_email = '$user_email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $book_id = mysqli_fetch_assoc($result)['book_id'];
        
        // Delete the borrowed book entry
        $deleteQuery = "DELETE FROM BorrowedBooks WHERE borrowed_id = '$borrowed_id' AND user_email = '$user_email'";
        if (mysqli_query($conn, $deleteQuery)) {
            // Update the number of books available in the Books table
            $updateQuery = "UPDATE Books SET no_of_books_available = no_of_books_available + 1 WHERE book_id = '$book_id'";
            if (mysqli_query($conn, $updateQuery)) {
                $message = "Book returned successfully!";
            } else {
                $message = "Error updating book availability.";
            }
        } else {
            $message = "Error processing the return.";
        }
    } else {
        $message = "No record found for this book in your borrowed books.";
    }
}

// Fetch borrowed books for the logged-in user
$user_email = $_SESSION['userEmail'];
$query = "SELECT BorrowedBooks.borrowed_id, Books.book_id, Books.title, Books.author, Books.subject, Books.ifsc_code, BorrowedBooks.borrowed_date 
          FROM BorrowedBooks 
          JOIN Books ON BorrowedBooks.book_id = Books.book_id 
          WHERE BorrowedBooks.user_email = '$user_email'";
$result = mysqli_query($conn, $query);
$borrowedBooks = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Book</title>
    <link rel="stylesheet" href="returnstyles.css">
</head>
<body>
    <header>
        <h1>Return Book</h1>
    </header>
    <main>
        <?php if (!empty($message)): ?>
            <p class="<?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </p>
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
                        <th>Action</th>
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
                            <td class="action">
                                <form method="POST">
                                    <input type="hidden" name="return_borrowed_id" value="<?php echo htmlspecialchars($book['borrowed_id']); ?>">
                                    <button type="submit" class="return-btn">Return</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have no borrowed books.</p>
        <?php endif; ?>

        <!-- Return to Home Button -->
        <div class="home-button-container">
            <a href="home.php" class="home-btn">Return to Home</a>
        </div>
    </main>
</body>
</html>
