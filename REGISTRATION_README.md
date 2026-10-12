# Registration — HTML and PHP

Full working page: [register.php](register.php). Frontend below shows only the form fields; backend below is the current application handler. These are documentation excerpts, not separate runnable pages. Bootstrap starts the session and connects to MySQL. Both pages use the `users` table.

## Frontend form

```html
<form action="register.php" method="POST">
  <label>Full name <input name="name" required></label>
  <label>Email <input type="email" name="email" required></label>
  <label>Mobile <input type="tel" name="phone" pattern="[0-9]{10}" required></label>
  <label>Password <input type="password" name="password" minlength="6" required></label>
  <label>Confirm password <input type="password" name="confirm_password" minlength="6" required></label>
  <label>Address <textarea name="address" required></textarea></label>
  <label>District / City <input name="district" required></label>
  <label>State <select name="state" required>
    <option value="">Choose state</option>
    <?php foreach ($indian_states as $state): ?>
      <option value="<?= htmlspecialchars($state) ?>"><?= htmlspecialchars($state) ?></option>
    <?php endforeach; ?>
  </select></label>
  <label>PIN <input name="pincode" pattern="[0-9]{6}" required></label>
  <button type="submit">Create Account</button>
</form>
```

## PHP backend

```php
<?php
require_once 'includes/bootstrap.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);
    $district = sanitize_input($_POST['district']);
    $state = sanitize_input($_POST['state']);
    $pincode = sanitize_input($_POST['pincode']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $error = "Passwords do not match. Please re-type your password.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif (!empty($phone) && !preg_match('/^[0-9]{10}$/', preg_replace('/[^0-9]/', '', $phone))) {
        $error = "Please enter a valid 10-digit Indian mobile number.";
    } elseif (!empty($pincode) && !preg_match('/^[0-9]{6}$/', trim($pincode))) {
        $error = "Please enter a valid 6-digit postal PIN code.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "An account with this email address already exists. Please sign in.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, address, district, state, pincode, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $phone, $address, $district, $state, $pincode, $hashed_password])) {
                $_SESSION['user_id'] = $conn->lastInsertId();
                $_SESSION['user_name'] = $name;
                $_SESSION['user_role'] = 'customer';
                header("Location: index.php");
                exit;
            } else {
                $error = "Registration failed due to a server error. Please try again.";
            }
        }
    }
}
```

The real page defines `$indian_states` before rendering the form. It hashes passwords with `password_hash`; login verifies the hash using `password_verify`.
