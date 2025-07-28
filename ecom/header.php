<?php
// Prevent session_start() if already started (important for included files)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection. Make sure db.php is in the same directory.
require_once 'db.php';

// Check if user is logged in for header display
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$username = $isLoggedIn ? htmlspecialchars($_SESSION['username']) : '';
$userType = $isLoggedIn ? ($_SESSION['user_type'] ?? 'customer') : '';
// Get theme preference from session, fallback to 'light'
$themePreference = $isLoggedIn ? ($_SESSION['theme_preference'] ?? 'light') : 'light'; 

// Apply theme class to HTML tag based on session preference
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo $themePreference === 'dark' ? 'dark' : ''; ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SneakersxStudio</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"/>
  <!-- Tailwind CSS CDN - Now included in header for all pages -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Custom styles for dark mode transition and general elements -->
  <style>
      body {
          transition: background-color 0.3s ease, color 0.3s ease;
          font-family: 'Inter', sans-serif;
      }
      .dark body {
          background-color: #1a202c; /* Dark background */
          color: #e2e8f0; /* Light text */
      }
      .dark .bg-white {
          background-color: #2d3748; /* Darker card background */
      }
      .dark .text-gray-900 {
          color: #e2e8f0;
      }
      .dark .text-gray-700 {
          color: #cbd5e0;
      }
      .dark input, .dark textarea, .dark select {
          background-color: #4a5568;
          color: #e2e8f0;
          border-color: #6b7280;
      }
      .dark input:focus, .dark textarea:focus, .dark select:focus {
          border-color: #63b3ed;
          box-shadow: 0 0 0 3px rgba(99, 179, 237, 0.5);
      }
      .password-box {
          position: relative;
      }
      .password-box .toggle-password {
          position: absolute;
          right: 12px;
          top: 50%;
          transform: translateY(-50%);
          cursor: pointer;
          color: #9ca3af;
      }
      .dark .password-box .toggle-password {
          color: #cbd5e0;
      }
      /* Additional styles for dropdown menu for profile icon */
      .dropdown {
          position: relative;
          display: inline-block;
      }

      .dropdown-content {
          display: none;
          position: absolute;
          background-color: #f9f9f9;
          min-width: 160px;
          box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
          z-index: 1;
          border-radius: 5px;
          top: 100%; /* Position below the profile icon */
          left: 0; /* Align left with the icon */
      }

      .dropdown-content a {
          color: black;
          padding: 12px 16px;
          text-decoration: none;
          display: block;
          text-align: left;
      }

      .dropdown-content a:hover {
          background-color: #f1f1f1;
          border-radius: 5px;
      }

      .dropdown:hover .dropdown-content {
          display: block; /* Show on hover */
      }

      .profile-link {
          display: flex;
          align-items: center;
          gap: 5px;
          color: #333;
          padding: 0 20px;
          height: 80px;
          line-height: 80px;
          text-decoration: none;
          transition: 0.3s ease;
      }

      .profile-link i {
          font-size: 1.2em;
      }

      #navbar li {
          padding: 0 20px;
          position: relative; /* Needed for dropdown positioning */
      }

      /* Style for theme toggle button */
      #theme-toggle {
          background: none;
          border: none;
          cursor: pointer;
          font-size: 1.5em; /* Adjust size as needed */
          color: #333; /* Default light mode icon color */
          transition: color 0.3s ease;
          padding: 0 10px; /* Adjust padding for spacing */
      }
      .dark #theme-toggle {
          color: #f0f0f0; /* Dark mode icon color */
      }
  </style>
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen">
    <!-- Header -->
    <section id="header">
      <a href="index.php"><img src="img/Logo.jpg" alt="Logo"></a>
      <div>
        <ul id="navbar">
          <li><a  href="index.php">Home</a></li>
          <li><a href="shop.php">Shop</a></li>
          <li><a href="blog.php">Blog</a></li>
          <li><a href="exclusive.php">Exclusive</a></li>
          <li><a href="aboutUs.php">About</a></li>
          <?php if ($userType === 'admin'): // Show admin link only if user is admin ?>
          <li><a href="admin.php">Admin</a></li>
          <?php endif; ?>
          
          <?php if ($isLoggedIn): // If logged in, show profile icon and logout ?>
          <li class="dropdown">
            <a href="profile.php" class="profile-link">
              <i class="fa fa-user"></i> <?php echo $username; ?>
            </a>
            <div class="dropdown-content">
              <a href="profile.php">My Profile</a>
              <a href="logout.php">Logout</a>
            </div>
          </li>
          <?php else: // If not logged in, show login link ?>
          <li><a href="login.php">Log In</a></li>
          <?php endif; ?>
          
          <li><a href="cart.php"><i class="far fa-shopping-bag"></i></a></li>
          <li>
              <!-- Theme Toggle Button -->
              <button id="theme-toggle" aria-label="Toggle dark mode">
                  <i class="fas fa-sun" id="sun-icon" style="display: <?php echo $themePreference === 'light' ? 'inline' : 'none'; ?>;"></i>
                  <i class="fas fa-moon" id="moon-icon" style="display: <?php echo $themePreference === 'dark' ? 'inline' : 'none'; ?>;"></i>
              </button>
          </li>
        </ul>
      </div>
    </section>

    <main class="py-10"> <!-- Main content area starts here -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const themeToggleBtn = document.getElementById('theme-toggle');
    const sunIcon = document.getElementById('sun-icon');
    const moonIcon = document.getElementById('moon-icon');
    const htmlElement = document.documentElement;

    themeToggleBtn.addEventListener('click', function() {
        const isCurrentlyDark = htmlElement.classList.contains('dark');
        if (isCurrentlyDark) {
            htmlElement.classList.remove('dark');
            sunIcon.style.display = 'inline';
            moonIcon.style.display = 'none';
            // Optionally, send an AJAX request to save preference immediately
            // For now, rely on profile page's form submission for persistence
        } else {
            htmlElement.classList.add('dark');
            sunIcon.style.display = 'none';
            moonIcon.style.display = 'inline';
            // Optionally, send an AJAX request to save preference immediately
        }
    });
});
</script>
