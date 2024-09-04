<?php
// Start the session
session_start();

// Include the database connection file
include("database.php");

// Initialize variables
$message = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query the database to check if the staff exists
    $query = "SELECT * FROM Staff WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $staff = mysqli_fetch_assoc($result);

        // Verify the password
        if ($staff['password'] === $password) {
            // Store email in session
            $_SESSION['userEmail'] = $email;

            // Redirect to home page
            header("Location: home.php");
            exit();
        } else {
            $message = "Incorrect password. Please try again.";
        }
    } else {
        $message = "Email not found. Please contact the library for assistance.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login</title>
    <link rel="stylesheet" href="loginstyles.css">
</head>
<body>
    <header>
        <h1>Staff Login</h1>
    </header>
    <main>
        <form id="staffLoginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <p id="message"><?php echo $message; ?></p>
        <p>Don't have an account? <a href="staff-registration.php">Register here</a></p>
    </main>
</body>
</html>
