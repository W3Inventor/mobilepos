<div class="content-sidebar content-sidebar-md" data-scrollbar-target="#psScrollbarInit">
    <div class="content-sidebar-header bg-white sticky-top hstack justify-content-between">
        <h4 class="fw-bolder mb-0">Settings</h4>
        <a href="javascript:void(0);" class="app-sidebar-close-trigger d-flex">
            <i class="feather-x"></i>
        </a>
    </div>
    <div class="content-sidebar-body">
        <ul class="nav flex-column nxl-content-sidebar-item">
            <?php
            // Get the current page name
            $currentPage = basename($_SERVER['PHP_SELF']);
            ?>
            
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'settings-general.php') ? 'active' : ''; ?>" href="settings-general.php">
                    <i class="feather-airplay"></i>
                    <span>General</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'settings-email.php') ? 'active' : ''; ?>" href="settings-email.php">
                    <i class="feather-mail"></i>
                    <span>SMTP Setup</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'sms-gateways.php') ? 'active' : ''; ?>" href="sms-gateways.php">
                    <i class="feather-git-branch"></i>
                    <span>SMS Gateway Setup</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'payment-methods.php') ? 'active' : ''; ?>" href="payment-methods.php">
                    <i class="feather-credit-card"></i>
                    <span>Payment Method</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'warranty.php') ? 'active' : ''; ?>" href="warranty.php">
                    <i class="feather-award"></i>
                    <span>Warranty Setup</span>
                </a>
            </li>
        </ul>
    </div>
</div>
