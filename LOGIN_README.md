# Login — HTML and PHP

Full working page: [login.php](login.php). Frontend below shows only the form fields; backend below is the current application handler. These are documentation excerpts, not separate runnable pages. Bootstrap starts the session and connects to MySQL. Both pages use the `users` table.

## Frontend form

```html
<form action="login.php" method="POST">
  <label>Email <input type="email" name="email" required autocomplete="email"></label>
  <label>Password <input type="password" name="password" required autocomplete="current-password"></label>
  <button type="submit">Sign In</button>
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
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];
        
        $redirect = isset($_GET['redirect']) ? sanitize_input($_GET['redirect']) : 'index.php';
        header("Location: " . $redirect);
        exit;
    } else {
        $error = "Invalid email or password. Please verify your credentials.";
    }
}
```
