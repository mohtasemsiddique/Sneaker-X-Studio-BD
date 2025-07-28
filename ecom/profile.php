<?php
// Include the common header file which handles session_start(), db.php, and initial HTML/CSS
require_once 'header.php';

// Redirect if user is not logged in (redundant check as header.php includes db.php and sets session variables)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$errors = [];
$message = '';

// Define the upload directory for profile images
$uploadDir = 'img/profile/';

// Ensure the upload directory exists and is writable
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true); // Create directory with read/write permissions
}

// Fetch user data
$user = null;
try {
    // $pdo is available from header.php
    $stmt = $pdo->prepare("SELECT user_id, username, email, fullname, shippingaddress, profile_image, theme_preference FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // User not found, possibly tampered session or deleted account
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }
} catch (PDOException $e) {
    error_log("Profile fetch error: " . $e->getMessage());
    $errors[] = "Error fetching profile data. Please try again later.";
}

// --- Handle Profile Update Submission ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_profile"])) {
    $newFullname = htmlspecialchars(trim($_POST["fullname"] ?? ''));
    $newShippingAddress = htmlspecialchars(trim($_POST["shippingaddress"] ?? ''));
    $newThemePreference = htmlspecialchars(trim($_POST["theme_preference"] ?? 'light'));
    
    $newProfileImageDbPath = $user['profile_image']; // Default to current image path

    // Basic validation
    if (empty($newFullname)) {
        $errors[] = "Full name cannot be empty.";
    }
    if (!in_array($newThemePreference, ['light', 'dark'])) {
        $errors[] = "Invalid theme preference. Must be 'light' or 'dark'.";
        $newThemePreference = 'light'; // Fallback
    }

    // Handle profile image upload
    if (isset($_FILES['profile_image_file']) && $_FILES['profile_image_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_image_file'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        $fileType = $file['type'];

        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif']; // Allowed image extensions

        if (in_array($fileExt, $allowed)) {
            if ($fileError === 0) {
                if ($fileSize < 5000000) { // Max 5MB file size
                    $newFileName = uniqid('', true) . "." . $fileExt;
                    $targetFilePath = $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmpName, $targetFilePath)) {
                        $newProfileImageDbPath = $targetFilePath; // Path to store in DB
                        // Optionally, delete old image if it exists and is not the default placeholder
                        if (!empty($user['profile_image']) && strpos($user['profile_image'], 'placehold.co') === false && file_exists($user['profile_image'])) {
                            unlink($user['profile_image']); // Delete the old image file
                        }
                    } else {
                        $errors[] = "Failed to upload profile image.";
                    }
                } else {
                    $errors[] = "Your file is too large (max 5MB).";
                }
            } else {
                $errors[] = "There was an error uploading your file.";
            }
        } else {
            $errors[] = "You cannot upload files of this type. Only JPG, JPEG, PNG, GIF are allowed.";
        }
    }

    // Only proceed with database update if there are no errors from validation or file upload
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET fullname = ?, shippingaddress = ?, theme_preference = ?, profile_image = ? WHERE user_id = ?");
            $stmt->execute([$newFullname, $newShippingAddress, $newThemePreference, $newProfileImageDbPath, $userId]);

            // Update session variables immediately to reflect changes
            $_SESSION['fullname'] = $newFullname;
            $_SESSION['shippingaddress'] = $newShippingAddress;
            $_SESSION['theme_preference'] = $newThemePreference;
            $_SESSION['profile_image'] = $newProfileImageDbPath; 

            $message = "Profile updated successfully!";
            // Update the $user array in memory for this request to show updated data
            $user['fullname'] = $newFullname;
            $user['shippingaddress'] = $newShippingAddress;
            $user['theme_preference'] = $newThemePreference;
            $user['profile_image'] = $newProfileImageDbPath;

        } catch (PDOException $e) {
            error_log("Profile update error: " . $e->getMessage());
            $errors[] = "Failed to update profile: " . $e->getMessage();
        }
    }
}

// --- Handle Password Reset Submission ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reset_password"])) {
    $currentPassword = $_POST["current_password"] ?? '';
    $newPassword = $_POST["new_password"] ?? '';
    $confirmNewPassword = $_POST["confirm_new_password"] ?? '';

    // Fetch current hashed password from DB for verification
    try {
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $storedHash = $stmt->fetchColumn();

        if (!password_verify($currentPassword, $storedHash)) {
            $errors[] = "Current password is incorrect.";
        }
        // Consistent with login.php minimum length (3 characters recommended for security)
        if (empty($newPassword) || strlen($newPassword) < 3) { 
            $errors[] = "New password must be at least 3 characters long.";
        }
        if ($newPassword !== $confirmNewPassword) {
            $errors[] = "New password and confirmation do not match.";
        }

        if (empty($errors)) {
            $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
            $stmt->execute([$hashedNewPassword, $userId]);
            $message = "Password reset successfully!";
        }
    } catch (PDOException $e) {
        error_log("Password reset error: " . $e->getMessage());
        $errors[] = "Failed to reset password: " . $e->getMessage();
    }
}

// Initialize form values from $user array to display current data (if user data was fetched)
$currentFullname = $user['fullname'] ?? '';
$currentEmail = $user['email'] ?? '';
$currentShippingAddress = $user['shippingaddress'] ?? '';
// Use a placeholder image if profile_image is empty or null, otherwise use the stored path
$currentProfileImage = !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'https://placehold.co/150x150/cccccc/000000?text=No+Image';
$currentThemePreference = $user['theme_preference'] ?? 'light';

// Check for messages/errors from previous POST requests stored in session (if redirects occurred)
if (isset($_SESSION['profile_message'])) {
    $message = $_SESSION['profile_message'];
    unset($_SESSION['profile_message']);
}
if (isset($_SESSION['profile_errors'])) {
    // Merge errors to display all
    $errors = array_merge($errors, $_SESSION['profile_errors']); 
    unset($_SESSION['profile_errors']);
}
?>

    <div class="container mx-auto p-4 md:p-8 bg-white rounded-lg shadow-xl max-w-3xl w-full">
        <h1 class="text-3xl font-bold text-center mb-6 text-indigo-700 dark:text-indigo-400">Your Profile</h1>

        <!-- Message and Error Display -->
        <?php if (!empty($message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline"><?php echo htmlspecialchars($message); ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <ul class="mt-2 list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="flex flex-col md:flex-row gap-8">
            <!-- Profile Picture Section -->
            <div class="w-full md:w-1/3 flex flex-col items-center">
                <img src="<?php echo $currentProfileImage; ?>" alt="Profile Picture" class="w-32 h-32 rounded-full object-cover border-4 border-indigo-500 mb-4 shadow-lg">
                <h2 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($currentFullname); ?></h2>
                <p class="text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($currentEmail); ?></p>
            </div>

            <!-- Profile Update Forms Section -->
            <div class="w-full md:w-2/3">
                <!-- Update Profile Details Form -->
                <form method="POST" action="profile.php" enctype="multipart/form-data" class="mb-8 p-6 bg-gray-50 dark:bg-gray-700 rounded-lg shadow-md">
                    <h3 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Update Profile Details</h3>
                    
                    <div class="mb-4">
                        <label for="fullname" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Full Name:</label>
                        <input type="text" id="fullname" name="fullname" value="<?php echo $currentFullname; ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600" required>
                    </div>

                    <div class="mb-4">
                        <label for="shippingaddress" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Shipping Address:</label>
                        <textarea id="shippingaddress" name="shippingaddress" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600" required><?php echo $currentShippingAddress; ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="profile_image_file" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Upload Profile Image:</label>
                        <input type="file" id="profile_image_file" name="profile_image_file" accept="image/*" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600">
                        <p class="text-xs text-gray-500 mt-1 dark:text-gray-400">Max 5MB. Allowed: JPG, PNG, GIF. Leave empty to keep current image.</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Theme Preference:</label>
                        <div class="mt-2">
                            <label class="inline-flex items-center mr-4">
                                <input type="radio" name="theme_preference" value="light" class="form-radio text-indigo-600" <?php echo ($currentThemePreference === 'light' || empty($currentThemePreference)) ? 'checked' : ''; ?>>
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Light</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="theme_preference" value="dark" class="form-radio text-indigo-600" <?php echo ($currentThemePreference === 'dark') ? 'checked' : ''; ?>>
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Dark</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" name="update_profile" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 w-full transition duration-300">
                        Update Profile
                    </button>
                </form>

                <!-- Reset Password Form -->
                <form method="POST" action="profile.php" class="p-6 bg-gray-50 dark:bg-gray-700 rounded-lg shadow-md">
                    <h3 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Reset Password</h3>

                    <div class="mb-4 password-box">
                        <label for="current_password" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Current Password:</label>
                        <input type="password" id="current_password" name="current_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600" required>
                        <i class="fas fa-eye-slash toggle-password"></i>
                    </div>

                    <div class="mb-4 password-box">
                        <label for="new_password" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">New Password:</label>
                        <input type="password" id="new_password" name="new_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600" required>
                        <i class="fas fa-eye-slash toggle-password"></i>
                    </div>

                    <div class="mb-6 password-box">
                        <label for="confirm_new_password" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Confirm New Password:</label>
                        <input type="password" id="confirm_new_password" name="confirm_new_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600" required>
                        <i class="fas fa-eye-slash toggle-password"></i>
                    </div>

                    <button type="submit" name="reset_password" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 w-full transition duration-300">
                        Reset Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript for password toggle and theme switching -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password Toggle Logic (re-used from login.js concept)
            const passwordToggleIcons = document.querySelectorAll('.toggle-password');
            passwordToggleIcons.forEach(icon => {
                icon.addEventListener('click', function() {
                    const passwordInput = this.previousElementSibling;
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        this.classList.remove('fa-eye-slash');
                        this.classList.add('fa-eye');
                    } else {
                        passwordInput.type = 'password';
                        this.classList.remove('fa-eye');
                        this.classList.add('fa-eye-slash');
                    }
                });
            });

            // Theme Switching Logic
            const themeRadios = document.querySelectorAll('input[name="theme_preference"]');
            themeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'dark') {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                    // Note: Theme change is saved to DB on form submission.
                    // This JavaScript just provides immediate visual feedback.
                });
            });
        });
    </script>
<?php
// Include the common footer file
require_once 'footer.php';
?>
