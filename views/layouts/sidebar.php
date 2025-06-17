<aside class="sidebar">
    <!-- <div class="sidebar-header">
        <h3>Menu</h3>
    </div> -->

    <div class="sidebar-brand">
        <a href="/dashboard">CuanTrack</a>
    </div>

    <ul class="sidebar-menu">
        <li><a href="/dashboard" class="<?= isCurrentPage('/dashboard') ? 'active' : '' ?>">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a></li>
        <li><a href="/wallets" class="<?= isCurrentPage('/wallets') ? 'active' : '' ?>">
                <i class="bi bi-wallet2"></i>
                <span>Wallets</span>
            </a></li>
        <!-- Removed Transaction link from sidebar as it will be accessed through Wallets page -->
        <li><a href="/categories" class="<?= isCurrentPage('/categories') ? 'active' : '' ?>">
                <i class="bi bi-tag"></i>
                <span>Categories</span>
            </a></li>
        <li><a href="/budget" class="<?= isCurrentPage('/budget') ? 'active' : '' ?>">
                <i class="bi bi-pie-chart"></i>
                <span>Budget</span>
            </a></li>
        <li><a href="/goals" class="<?= isCurrentPage('/goals') ? 'active' : '' ?>">
                <i class="bi bi-flag"></i>
                <span>Goals</span>
            </a></li>
        <li><a href="/subscription" class="<?= isCurrentPage('/subscription') ? 'active' : '' ?>">
                <i class="bi bi-calendar-check"></i>
                <span>Subscription</span>
            </a>
        </li>
    </ul>

    <ul class="sidebar-footer">
        <li>
            <?php
            // Get user data
            if (isset($_SESSION['user_id'])) {
                require_once 'models/UserModel.php';
                $userModel = new UserModel();
                $user = $userModel->getById($_SESSION['user_id']);

                // Default avatar if user has no image
                $defaultAvatar = '/public/images/default-avatar.png';
                $profileImage = !empty($user['image']) && file_exists($user['image']) ? '/' . $user['image'] : $defaultAvatar;
            } else {
                $profileImage = '/public/images/default-avatar.png';
            }
            ?>
            <a href="/users/profile" class="profile-link">
                <div class="sidebar-profile-image">
                    <img src="<?= $profileImage ?>" alt="Profile">
                </div>
                <span>Profile</span>
            </a>
        </li>
        <li>
            <a href="/logout">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</aside>