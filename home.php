<?php
session_start();

// Include database connection
include("database.php");

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['userEmail']);
$userEmail = $isLoggedIn ? $_SESSION['userEmail'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Library</title>
    <link rel="stylesheet" href="homestyles.css">
    <style>
        /* CSS for slideshow */
        .slideshow-container {
            position: relative;
            max-width: 100%;
            margin: auto;
            overflow: hidden;
            border-bottom: 3px solid #007bff;
        }

        .slide {
            display: none;
            padding: 20px;
            text-align: center;
            background-color: #f4f4f4;
            color: #333;
            font-size: 1.2em;
            font-style: italic;
            border-radius: 8px;
        }

        .fade {
            animation: fade 3s ease-in-out infinite;
        }

        @keyframes fade {
            0% { opacity: 0; }
            20% { opacity: 1; }
            80% { opacity: 1; }
            100% { opacity: 0; }
        }

        /* CSS for account icon and dropdown menu */
        .account-icon {
            position: absolute;
            display: inline-block;
            top: 20px;
            right: 20px;
        }

        #accountImg {
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #ccc;
            transition: border-color 0.3s;
        }
        
        #accountImg:hover {
            border-color: #007bff;
        }

        .dropdown {
            display: none;
            position: absolute;
            background-color: #ffffff;
            border-radius: 8px;
            min-width: 200px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            top: 50px;
            right: 0;
            padding: 10px;
            box-sizing: border-box;
        }

        .dropdown-content {
            display: contents;
            color: black;
        }

        .dropdown-content a {
            color: #007bff;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            border-radius: 5px;
            margin-bottom: 5px;
            background-color: #1db1a6;
            transition: background-color 0.3s;
        }
        a.btn {
            background-color: #007bff;
            color: #333;
            font-family: 'Times New Roman', Times, serif;
        }
        .dropdown-content a:hover {
            background-color: green;
        }

        .dropdown-content p {
            padding: 10px 15px;
            margin: 0;
            font-weight: bold;
            text-align: center;
            color: #333;
        }

        .dropdown.show {
            display: block;
        }

        h3 {
            color: #333;
            margin: 10px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    

    <header>
        <h1>Welcome to Library</h1>
        <div class="account-icon" id="accountIcon">
            <img src="account-icon.jpg" alt="Account Icon" id="accountImg" onclick="toggleDropdown()">
            <div class="dropdown" id="dropdownMenu">
                <?php if ($isLoggedIn): ?>
                    <div class="dropdown-content">
                        <p>Logged in as: <?php echo htmlspecialchars($userEmail); ?></p>
                        <a href="profile.php" class="btn">Profile</a>
                        <a href="logout.php" class="btn">Logout</a>
                    </div>
                <?php else: ?>
                    <div class="dropdown-content">
                        <h3>Student</h3>
                        <a href="student-login.php" class="btn">Login</a>
                        <a href="student-registration.php" class="btn">Sign Up</a>
                        <h3>Staff</h3>
                        <a href="staff-login.php" class="btn">Login</a>
                        <a href="staff-registration.php" class="btn">Sign Up</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main>
        <!-- Slideshow Container -->
    <div class="slideshow-container">
        <div class="slide fade">"The only way to do great work is to love what you do." – Steve Jobs</div>
        <div class="slide fade">"Success is not final, failure is not fatal: It is the courage to continue that counts." – Winston Churchill</div>
        <div class="slide fade">"Life is what happens when you're busy making other plans." – John Lennon</div>
        <div class="slide fade">"The best way to predict the future is to invent it." – Alan Kay</div>
    </div>
        <p>Select an option below to manage your books:</p>
        <div class="options-container">
            <a href="book-issue.php" class="main-btn">Book Issue</a>
            <a href="book-return.php" class="main-btn">Book Return</a>
        </div>
    </main>

    <script>
        // Function to toggle the dropdown
        function toggleDropdown() {
            var dropdown = document.getElementById("dropdownMenu");
            dropdown.classList.toggle("show");
        }

        // Close the dropdown if the user clicks outside of it
        window.onclick = function(event) {
            if (!event.target.matches('#accountImg')) {
                var dropdown = document.getElementById("dropdownMenu");
                if (dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            }
        }

        // Slideshow Script
        let slideIndex = 0;
        function showSlides() {
            let slides = document.getElementsByClassName("slide");
            for (let i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";  
            }
            slideIndex++;
            if (slideIndex > slides.length) {slideIndex = 1}    
            slides[slideIndex-1].style.display = "block";  
            setTimeout(showSlides, 3000); // Change slide every 3 seconds
        }
        showSlides(); // Initialize the slideshow
    </script>
</body>
</html>
