<?php
session_start();
include("database.php");

// Check if the user is logged in
if (!isset($_SESSION['userEmail'])) {
    header("Location: home.php");
    exit();
}

$userEmail = $_SESSION['userEmail'];

// Handle book issue
$message = '';
if (isset($_POST['issue_book'])) {
    $book_id = $_POST['book_id'];

    // Check availability
    $query = "SELECT no_of_books_available FROM Books WHERE book_id = '$book_id'";
    $result = mysqli_query($conn, $query);
    $book = mysqli_fetch_assoc($result);

    if ($book && $book['no_of_books_available'] > 0) {
        // Issue the book
        $issueDate = date("Y-m-d H:i:s");
        $issueQuery = "INSERT INTO BorrowedBooks (book_id, user_email, borrowed_date) VALUES ('$book_id', '$userEmail', '$issueDate')";
        mysqli_query($conn, $issueQuery);

        // Update book availability
        $updateQuery = "UPDATE Books SET no_of_books_available = no_of_books_available - 1 WHERE book_id = '$book_id'";
        mysqli_query($conn, $updateQuery);

        $message = "Book issued successfully!";
    } else {
        $message = "Sorry, this book is currently unavailable.";
    }
}

// Pagination setup
$limit = 10; // Number of books per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Build search query with filters
$searchQuery = "SELECT * FROM Books WHERE 1=1";

if (!empty($_GET['title'])) {
    $title = mysqli_real_escape_string($conn, $_GET['title']);
    $searchQuery .= " AND title LIKE '%$title%'";
}

if (!empty($_GET['author'])) {
    $author = mysqli_real_escape_string($conn, $_GET['author']);
    $searchQuery .= " AND author LIKE '%$author%'";
}

if (!empty($_GET['subject'])) {
    $subject = mysqli_real_escape_string($conn, $_GET['subject']);
    $searchQuery .= " AND subject LIKE '%$subject%'";
}

if (!empty($_GET['book_id'])) {
    $book_id = mysqli_real_escape_string($conn, $_GET['book_id']);
    $searchQuery .= " AND book_id LIKE '%$book_id%'";
}

if (!empty($_GET['ifsc_code'])) {
    $ifsc_code = mysqli_real_escape_string($conn, $_GET['ifsc_code']);
    $searchQuery .= " AND ifsc_code LIKE '%$ifsc_code%'";
}

// Get total records for pagination
$totalQuery = "SELECT COUNT(*) AS total FROM ($searchQuery) AS totalBooks";
$totalResult = mysqli_query($conn, $totalQuery);
$totalBooks = mysqli_fetch_assoc($totalResult)['total'];

$searchQuery .= " LIMIT $limit OFFSET $offset";
$booksResult = mysqli_query($conn, $searchQuery);

// Calculate total pages
$totalPages = ceil($totalBooks / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Issue</title>
    <link rel="stylesheet" href="issuestyles.css">
</head>
<body>
    <header>
        <h1>Issue a Book</h1>
    </header>
    <main>
        <form method="GET" action="">
            <input type="text" name="title" placeholder="Search by title" value="<?php echo htmlspecialchars(isset($_GET['title']) ? $_GET['title'] : ''); ?>">
            <input type="text" name="author" placeholder="Search by author" value="<?php echo htmlspecialchars(isset($_GET['author']) ? $_GET['author'] : ''); ?>">
            <input type="text" name="subject" placeholder="Search by subject" value="<?php echo htmlspecialchars(isset($_GET['subject']) ? $_GET['subject'] : ''); ?>">
            <input type="text" name="book_id" placeholder="Search by book ID" value="<?php echo htmlspecialchars(isset($_GET['book_id']) ? $_GET['book_id'] : ''); ?>">
            <input type="text" name="ifsc_code" placeholder="Search by IFSC code" value="<?php echo htmlspecialchars(isset($_GET['ifsc_code']) ? $_GET['ifsc_code'] : ''); ?>">
            <button type="submit">Search</button>
        </form>

        <?php if (isset($message)): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Subject</th>
                    <th>IFSC Code</th>
                    <th>Available Copies</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($book = mysqli_fetch_assoc($booksResult)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($book['book_id']); ?></td>
                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                        <td><?php echo htmlspecialchars($book['subject']); ?></td>
                        <td><?php echo htmlspecialchars($book['ifsc_code']); ?></td>
                        <td><?php echo htmlspecialchars($book['no_of_books_available']); ?></td>
                        <td>
                            <?php if ($book['no_of_books_available'] > 0): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">
                                    <button type="submit" name="issue_book">Issue</button>
                                </form>
                            <?php else: ?>
                                <span>Not Available</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">&laquo; Previous</a>
            <?php endif; ?>

            <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>

            <?php if ($page < $totalPages): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>

        <!-- Return to Home Button -->
        <div class="home-button-container">
            <a href="home.php" class="home-btn">Return to Home</a>
        </div>
    </main>
</body>
</html>
