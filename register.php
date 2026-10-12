<?php
require_once 'includes/bootstrap.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$indian_states = [
    "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chhattisgarh", "Goa", "Gujarat", 
    "Haryana", "Himachal Pradesh", "Jharkhand", "Karnataka", "Kerala", "Madhya Pradesh", 
    "Maharashtra", "Manipur", "Meghalaya", "Mizoram", "Nagaland", "Odisha", "Punjab", 
    "Rajasthan", "Sikkim", "Tamil Nadu", "Telangana", "Tripura", "Uttar Pradesh", "Uttarakhand", 
    "West Bengal", "Delhi NCR", "Jammu and Kashmir", "Ladakh"
];

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

require_once 'includes/header.php';
?>

<div class="container">
    <div class="auth-split-wrapper" style="max-width: 1040px;">
        <!-- Left: Brand Perks & Trust Highlights -->
        <div class="auth-brand-side">
            <p class="eyebrow">THE CLOTH HUB</p>
            <h2 class="auth-brand-title">Your everyday<br>wardrobe starts here.</h2>
            <p class="auth-brand-desc">Save your delivery details, place an order and find your order history in one place.</p>
            <img src="assets/images/men-formal-white.jpg" alt="White formal shirt" class="auth-cloth-image">
            <a href="shop.php" class="auth-collection-link">Explore the collection &rarr;</a>
        </div>
        <!-- Right: Registration Form -->
        <div class="auth-form-side" style="padding: 40px 44px;">
            <div class="auth-form-header">
                <h1 class="auth-form-title">Create Account</h1>
                <p class="auth-form-subtitle">Fill in your delivery address and contact information below.</p>
            </div>

            <?php if(isset($error)): ?>
                <div style="background: #fee2e2; color: #991b1b; padding: 12px 16px; margin-bottom: 20px; border-radius: 0; border: 1px solid #fecaca; font-weight: 500; font-size: 13px; display: flex; align-items: center; gap: 8px;">
                    <span></span>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST" id="regForm">
                <!-- Section 1: Personal & Contact -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <div class="input-with-icon">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Rahul Sharma" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Mobile Number *</label>
                        <div class="input-with-icon">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                            <input type="tel" name="phone" class="form-control" placeholder="10-digit mobile" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" pattern="[0-9]{10}" maxlength="10" required>
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label>Email Address *</label>
                    <div class="input-with-icon">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <input type="email" name="email" class="form-control" placeholder="name@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                </div>

                <!-- Password & Confirm -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group">
                        <label>Password *</label>
                        <div class="input-with-icon">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <input type="password" id="regPassword" name="password" class="form-control" placeholder="Min. 6 characters" minlength="6" required>
                            <button type="button" class="toggle-password" onclick="togglePasswordVisibility('regPassword', this)">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password *</label>
                        <div class="input-with-icon">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <input type="password" id="regConfirmPassword" name="confirm_password" class="form-control" placeholder="Re-type password" minlength="6" required>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Address Fields -->
                <div style="background: var(--bg-page); border: 1px solid var(--border-color); border-radius: 0; padding: 18px; margin: 16px 0 20px;">
                    <div style="font-size: 13px; font-weight: 700; color: var(--dark); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                        <span></span>
                        <span>Delivery Address Details</span>
                    </div>

                    <div class="form-group">
                        <label style="font-size: 13px;">Flat, House No., Building, Street *</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="e.g. 402, Royal Residency, Opp. City Center Mall, CG Road" required style="font-size: 13px;"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 120px; gap: 10px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-size: 12px;">District / City *</label>
                            <input type="text" name="district" class="form-control" placeholder="e.g. Ahmedabad" value="<?= htmlspecialchars($_POST['district'] ?? '') ?>" required style="padding: 9px 12px; font-size: 13px;">
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-size: 12px;">State *</label>
                            <select name="state" class="form-control" required style="padding: 9px 12px; font-size: 13px;">
                                <option value="">Select State</option>
                                <?php foreach($indian_states as $st): ?>
                                    <option value="<?= $st ?>" <?= (isset($_POST['state']) && $_POST['state'] === $st) ? 'selected' : '' ?>>
                                        <?= $st ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-size: 12px;">PIN Code *</label>
                            <input type="text" name="pincode" class="form-control" placeholder="6 Digits" value="<?= htmlspecialchars($_POST['pincode'] ?? '') ?>" pattern="[0-9]{6}" maxlength="6" required style="padding: 9px 12px; font-size: 13px;">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="padding: 13px; font-size: 15px;">
                    <span>Create Account & Start Shopping</span>
                    <span>&rarr;</span>
                </button>
            </form>

            <div style="margin-top: 20px; text-align: center; font-size: 14px; color: var(--text-muted);">
                Already have an account? <a href="login.php" style="color: var(--primary); font-weight: 700; text-decoration: none;">Sign in here &rarr;</a>
            </div>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(fieldId, btn) {
    const input = document.getElementById(fieldId);
    if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
    } else {
        input.type = 'password';
        btn.innerHTML = `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
