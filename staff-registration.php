<?php
// Include the database connection
include("database.php");

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture and sanitize form data
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $staffId = mysqli_real_escape_string($conn, $_POST['staffId']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);  // Added name field
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirmPassword']);
    
    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match. Please try again.');</script>";
    } else {
        // Check if the staff ID already exists
        $checkQuery = "SELECT * FROM Staff WHERE staffId = '$staffId'";
        $result = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($result) > 0) {
            echo "<script>alert('Staff ID already exists. Please use a different Staff ID.');</script>";
        } else {
            // Insert data into the Staff table
            $sql = "INSERT INTO Staff (email, staffId, department, name, password) 
                    VALUES ('$email', '$staffId', '$department', '$name', '$password')";

            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Registration successful!');</script>";
                // Redirect to the login page after successful registration
                echo "<script>window.location.href = 'staff-login.php';</script>";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Registration</title>
    <link rel="stylesheet" href="registrationstyles.css">
</head>
<body>
    <header>
        <h1>Staff Registration</h1>
    </header>
    <main>
        <form id="staffRegistrationForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="staffId">Staff ID:</label>
            <input type="text" id="staffId" name="staffId" required>

            <label for="department">Department:</label>
            <input type="text" id="department" name="department" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required>

            <button type="submit">Register</button>
        </form>
        <p id="message"></p>
        <p>Already have an account? <a href="staff-login.php">Login here</a></p>
    </main>
</body>
</html>
