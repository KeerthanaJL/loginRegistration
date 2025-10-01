<?php
session_start();
include('../database/db_connect.php'); 

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch current user details
$stmt = $conn->prepare("SELECT name, age, dob, contact FROM userdata WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $dob = $_POST['dob'];
    $contact = $_POST['contact'];

    // Backend validation for contact number (only digits, 10 length)
    if (!preg_match("/^[0-9]{10}$/", $contact)) {
        echo "<script>alert('Invalid contact number. Must be 10 digits only.');</script>";
    } else {
        $update = $conn->prepare("UPDATE userdata SET name=?, age=?, dob=?, contact=? WHERE email=?");
        $update->bind_param("sisss", $name, $age, $dob, $contact, $email);
        
        if ($update->execute()) {
            echo "<script>alert('Profile updated successfully!');</script>";
            header("Refresh:0");
        } else {
            echo "<script>alert('Error updating profile');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
</head>
<body>
    <h2>Update Profile</h2>
    <form method="POST" onsubmit="return validateForm()">
        <label>Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required><br><br>

        <label>Age:</label>
        <input type="number" name="age" value="<?php echo htmlspecialchars($user['age']); ?>"><br><br>

        <label>Date of Birth:</label>
        <input type="date" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>"><br><br>

        <label>Contact:</label>
        <input type="text" name="contact" id="contact" value="<?php echo htmlspecialchars($user['contact']); ?>" required><br><br>

        <button type="submit">Update</button>
    </form>

    <br>
    <a href="dashboard.php">Back to Dashboard</a>

    <script>
    function validateForm() {
        let contact = document.getElementById("contact").value;
        let regex = /^[0-9]{10}$/;
        if (!regex.test(contact)) {
            alert("Contact number must be exactly 10 digits.");
            return false;
        }
        return true;
    }
    </script>
</body>
</html>
