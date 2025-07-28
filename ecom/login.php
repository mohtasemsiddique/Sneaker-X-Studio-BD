<?php
// Start a session at the very beginning of the script
session_start();

// Include database connection. Make sure db.php is in the same directory, or adjust path if it's in 'config/'
require_once 'db.php';

// Define login attempt limits
define('MAX_LOGIN_ATTEMPTS', 3);
define('LOCK_DURATION_HOURS', 1); // Account locked for 1 hour

// --- Handle Sign Up Form Submission ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signup_submit"])) {
    $firstName = htmlspecialchars(trim($_POST["first_name"]));
    $lastName = htmlspecialchars(trim($_POST["last_name"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $password = $_POST["password"]; // Password will be hashed later
    $captchaSignup = htmlspecialchars(trim($_POST["captcha_signup"]));
    
    // Get the expected CAPTCHA value from the session
    $expectedSignupCaptcha = $_SESSION['captcha_signup'] ?? ''; // Use null coalescing for safety

    $errors = [];

    // Basic server-side validation
    if (empty($firstName)) {
        $errors[] = "First name is required.";
    }
    if (empty($lastName)) {
        $errors[] = "Last name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    // IMPORTANT: Changed minimum password length from 3 to 8 for better security
    if (empty($password) || strlen($password) < 3) { 
        $errors[] = "Password must be at least 3 characters long.";
    }
    
    // CAPTCHA validation: Compare user input with the value stored in session
    if (strtolower($captchaSignup) !== strtolower($expectedSignupCaptcha)) {
        $errors[] = "CAPTCHA for signup is incorrect.";
    }


    if (empty($errors)) {
        // --- PHP logic for database interaction, session management, and validation ---
        try {
            // Combine first and last name to create the username for your 'users' table
            $username = $firstName . ' ' . $lastName;
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Check if email already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = "Email already registered. Please use a different email or log in.";
                $_SESSION['errors'] = $errors;
                $_SESSION['old_input'] = $_POST;
                header("Location: login.php");
                exit();
            }

            // Insert new user into the 'users' table (now including 'user_type')
            $user_type = 'customer'; // Default user type for new registrations
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, user_type) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword, $user_type]);

            // Registration successful!
            $_SESSION['message'] = "Registration successful! Please log in.";
            header("Location: login.php?registered=true");
            exit();

        } catch (PDOException $e) {
            $errors[] = "Registration failed: " . $e->getMessage();
            error_log("Registration Error: " . $e->getMessage()); // Log detailed error
            $_SESSION['errors'] = $errors; // Display generic error to user
            $_SESSION['old_input'] = $_POST;
        }
    } else {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = $_POST; // Keep form data for re-population
    }
    // If there are errors or exceptions, make sure to redirect or ensure errors are displayed
    if (!empty($errors)) {
        header("Location: login.php");
        exit();
    }
}

// --- Handle Log In Form Submission ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login_submit"])) {
    $email = htmlspecialchars(trim($_POST["login_email"]));
    $password = $_POST["login_password"];
    $captchaLogin = htmlspecialchars(trim($_POST["captcha_login"]));
    
    // Get the expected CAPTCHA value from the session for login validation
    $expectedLoginCaptcha = $_SESSION['captcha_login'] ?? '';

    $errors = [];

    // Basic server-side validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required for login.";
    }
    if (empty($password)) {
        $errors[] = "Password is required for login.";
    }
    // CAPTCHA validation: Compare user input with the value stored in session
    if (strtolower($captchaLogin) !== strtolower($expectedLoginCaptcha)) {
        $errors[] = "CAPTCHA for login is incorrect.";
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT user_id, username, email, password_hash, user_type, failed_logins, account_locked_until FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if user exists
            if ($user) {
                // Check if account is locked
                if ($user['account_locked_until'] && strtotime($user['account_locked_until']) > time()) {
                    $lock_time = date('g:i A', strtotime($user['account_locked_until']));
                    $errors[] = "Account is locked. Please try again after " . $lock_time . ".";
                    // Do NOT increment failed logins if account is already locked
                } else {
                    // Account is not locked or lock has expired, proceed with password verification

                    // If lock has expired, reset failed_logins and account_locked_until
                    if ($user['account_locked_until'] && strtotime($user['account_locked_until']) <= time()) {
                        $reset_stmt = $pdo->prepare("UPDATE users SET failed_logins = 0, account_locked_until = NULL WHERE user_id = ?");
                        $reset_stmt->execute([$user['user_id']]);
                        $user['failed_logins'] = 0; // Update in memory too
                    }

                    if (password_verify($password, $user['password_hash'])) {
                        // Login successful!
                        // Reset failed login attempts on successful login
                        $stmt_reset_attempts = $pdo->prepare("UPDATE users SET failed_logins = 0, account_locked_until = NULL WHERE user_id = ?");
                        $stmt_reset_attempts->execute([$user['user_id']]);

                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['user_type'] = $user['user_type'];
                        $_SESSION['loggedin'] = true;

                        // Redirect based on user type
                        if ($user['user_type'] === 'admin') {
                            header("Location: admin.php");
                        } else {
                            header("Location: index.php");
                        }
                        exit();
                    } else {
                        // Invalid password
                        $errors[] = "Invalid email or password.";

                        // Increment failed login attempts
                        $new_failed_logins = $user['failed_logins'] + 1;
                        $update_stmt = $pdo->prepare("UPDATE users SET failed_logins = ? WHERE user_id = ?");
                        $update_stmt->execute([$new_failed_logins, $user['user_id']]);

                        if ($new_failed_logins >= MAX_LOGIN_ATTEMPTS) {
                            // Lock the account if attempts exceed limit
                            $lock_time = date('Y-m-d H:i:s', strtotime('+' . LOCK_DURATION_HOURS . ' hour'));
                            $lock_stmt = $pdo->prepare("UPDATE users SET account_locked_until = ? WHERE user_id = ?");
                            $lock_stmt->execute([$lock_time, $user['user_id']]);
                            $errors[] = "You have exceeded the maximum login attempts. Your account is locked for " . LOCK_DURATION_HOURS . " hour(s).";
                        } else {
                            $remaining_attempts = MAX_LOGIN_ATTEMPTS - $new_failed_logins;
                            $errors[] .= " You have " . $remaining_attempts . " attempt(s) remaining before your account is locked.";
                        }
                    }
                }
            } else {
                // User does not exist (invalid email)
                $errors[] = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $errors[] = "Login failed: " . $e->getMessage();
            error_log("Login Error: " . $e->getMessage()); // Log detailed error
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_input_login'] = ['email' => $email];
        header("Location: login.php"); // Redirect to display login errors
        exit();
    }
}

// Generate a new CAPTCHA for each page load or form display
function generateCaptcha() {
    $chars = 'abcdefghijkmnpqrstuvwxyz23456789';
    $captcha = '';
    for ($i = 0; $i < 6; $i++) {
        $captcha .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $captcha;
}

$signupCaptcha = generateCaptcha();
$_SESSION['signup_captcha'] = $signupCaptcha; // Store it for verification

$loginCaptcha = generateCaptcha();
$_SESSION['login_captcha'] = $loginCaptcha; // Store it for verification

// Display any messages or errors
$displayMessage = '';
$displayErrors = [];

if (isset($_SESSION['message'])) {
    $displayMessage = $_SESSION['message'];
    unset($_SESSION['message']); // Clear message after displaying
}
if (isset($_SESSION['errors'])) {
    $displayErrors = $_SESSION['errors'];
    unset($_SESSION['errors']); // Clear errors after displaying
}

// Retrieve old input for repopulating forms
$oldSignupInput = isset($_SESSION['old_input']) ? $_SESSION['old_input'] : [];
unset($_SESSION['old_input']);

$oldLoginInput = isset($_SESSION['old_input_login']) ? $_SESSION['old_input_login'] : [];
unset($_SESSION['old_input_login']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sneaker'X Studio Login</title>
  <link rel="stylesheet" href="css/login.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
  <?php if (!empty($displayMessage)): ?>
      <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin-bottom: 15px; text-align: center;">
          <?php echo $displayMessage; ?>
      </div>
  <?php endif; ?>

  <?php if (!empty($displayErrors)): ?>
      <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 15px;">
          <ul>
              <?php foreach ($displayErrors as $error): ?>
                  <li><?php echo htmlspecialchars($error); ?></li>
              <?php endforeach; ?>
          </ul>
      </div>
  <?php endif; ?>

  <script src="js/script.js"></script>
  <script src="js/login.js"></script>
  <div class="container">
    <h1 class="logo">Sneaker'X Studio </h1>

    <div class="form-box">
      <div class="tabs">
        <button id="show-signup">Sign Up</button>
        <button id="show-login" class="active">Log In</button>
      </div>

      <form class="signup-form auth-form hidden" method="POST" action="login.php">
        <h2>Sign Up</h2>
        <input type="text" name="first_name" placeholder="First Name*" required value="<?php echo htmlspecialchars($oldSignupInput['first_name'] ?? ''); ?>" />
        <input type="text" name="last_name" placeholder="Last Name*" required value="<?php echo htmlspecialchars($oldSignupInput['last_name'] ?? ''); ?>" />
        <input type="email" name="email" placeholder="Email Address*" required value="<?php echo htmlspecialchars($oldSignupInput['email'] ?? ''); ?>" />

        <div class="password-box">
          <input type="password" name="password" placeholder="Password*" required />
          <i class="fas fa-eye-slash toggle-password"></i>
        </div>

        <div class="captcha-box">
          <label for="captcha-signup">Solve CAPTCHA:</label>
          <div class="captcha-display" id="captcha-signup-text"><?php echo htmlspecialchars($signupCaptcha); ?></div>
          <input type="text" id="captcha-signup" name="captcha_signup" placeholder="Enter CAPTCHA here" required />
        </div>

        <button type="submit" name="signup_submit" class="submit-btn">Sign Up</button>
        <div class="divider">OR</div>
        
        <a href="index.php" name="homebtn" class="home-btn">Home</a>
        <p class="social-label">Sign up with</p>
        <div class="social-icons">
          <button><i class="fab fa-google"></i></button>
          <button><i class="fab fa-apple"></i></button>
          <button><i class="fab fa-facebook-f"></i></button>
        </div>
        <p class="login-link">Already have an account? <a href="#" id="switch-login">Log In</a></p>
      </form>

      <form class="login-form auth-form" method="POST" action="login.php">
        <h2>Log In</h2>
        <input type="email" name="login_email" placeholder="Email Address*" required value="<?php echo htmlspecialchars($oldLoginInput['email'] ?? ''); ?>" />
        <div class="password-box">
          <input type="password" name="login_password" placeholder="Password*" required />
          <i class="fas fa-eye-slash toggle-password"></i>
        </div>

        <div class="captcha-box">
          <label for="captcha-login">Solve CAPTCHA:</label>
          <div class="captcha-display" id="captcha-login-text"><?php echo htmlspecialchars($loginCaptcha); ?></div>
          <input type="text" id="captcha-login" name="captcha_login" placeholder="Enter CAPTCHA here" required />
        </div>

        <button type="submit" name="login_submit" class="submit-btn">Log In</button>

        <div class="divider">OR</div>
        <a href="index.php" name="homebtn" class="home-btn">Home</a>
        <p class="social-label">Log in with</p>
        <div class="social-icons">
          <button><i class="fab fa-google"></i></button>
          <button><i class="fab fa-apple"></i></button>
          <button><i class="fab fa-facebook-f"></i></button>
        </div>
        <p class="login-link">Don't have an account? <a href="#" id="switch-signup">Sign Up</a></p>
      </form>
    </div>
  </div>
</body>
</html>

