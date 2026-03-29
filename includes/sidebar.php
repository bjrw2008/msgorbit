<div class="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-robot"></i>
        <h3>BJ AUTO MSG</h3>
    </div>
    <ul class="sidebar-menu">
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <a href="dashboard.php">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'bots.php' ? 'active' : ''; ?>">
            <a href="bots.php">
                <i class="fas fa-robot"></i>
                <span>Bot Management</span>
            </a>
        </li>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'channels.php' ? 'active' : ''; ?>">
            <a href="channels.php">
                <i class="fab fa-telegram"></i>
                <span>Channels/Groups</span>
            </a>
        </li>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'schedule.php' ? 'active' : ''; ?>">
            <a href="schedule.php">
                <i class="fas fa-calendar-alt"></i>
                <span>Schedule Messages</span>
            </a>
        </li>
    </ul>
</div>