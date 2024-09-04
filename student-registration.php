<?php
// Include the database connection
include("database.php");

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $rollNumber = mysqli_real_escape_string($conn, $_POST['rollNumber']);
    $branch = mysqli_real_escape_string($conn, $_POST['branch']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirmPassword']);
    
    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match. Please try again.');</script>";
    } else {
        // Check if the roll number already exists
        $checkQuery = "SELECT * FROM Student WHERE rollNumber = '$rollNumber'";
        $result = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($result) > 0) {
            echo "<script>alert('Roll Number already exists. Please use a different Roll Number.');</script>";
        } else {
            // Insert data into the Student table
            $sql = "INSERT INTO Student (rollNumber, name, email, branch, year, password) 
                    VALUES ('$rollNumber', '$name', '$email', '$branch', '$year', '$password')";
            
            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Registration successful!');</script>";
                // Redirect to the home page after successful registration
                echo "<script>window.location.href = 'home.html';</script>";
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
    <title>Student Registration</title>
    <link rel="stylesheet" href="registrationstyles.css">
</head>
<body>
    <header>
        <h1>Student Registration</h1>
    </header>
    <main>
        <form id="studentRegistrationForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="rollNumber">Roll Number:</label>
            <input type="text" id="rollNumber" name="rollNumber" required>

            <label for="branch">Branch:</label>
            <input type="text" id="branch" name="branch" required>

            <label for="year">Year:</label>
            <input type="number" id="year" name="year" min="1" max="4" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required>

            <button type="submit">Register</button>
        </form>
        <p id="message"></p>
        <p>Already have an account? <a href="student-login.php">Login</a></p>
    </main>
</body>
</html>
