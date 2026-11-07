<?php if (!empty($_SESSION['user_id'])): ?>
    <li><a class="dropdown-item" href="profile.php">Profile</a></li>
    <li><a class="dropdown-item" href="order.php">Order History</a></li>
    <li><a class="dropdown-item" href="checkout.php">Checkout</a></li>
    <li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a></li>
<?php else: ?>
    <li><a class="dropdown-item" href="register.php">Register</a></li>
    <li><a class="dropdown-item" href="login.php">Login</a></li>
    <li><a class="dropdown-item" href="checkout.php">Checkout</a></li>
<?php endif; ?>
