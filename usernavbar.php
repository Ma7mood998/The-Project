<nav>
        <div class="nav-links">
            <label for="theme-toggle">
                <input type="checkbox" id="theme-toggle">
                Dark Mode
            </label>
            <a href="admin.php">Home</a>
            <a href="my_bookings.php">My Bookings</a>
        </div>
        <div class="dropdown">
            <button><?php echo htmlspecialchars($firstName); ?> ▼</button>
            <div class="dropdown-content">
                <a href="profile.php">View Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
</nav>