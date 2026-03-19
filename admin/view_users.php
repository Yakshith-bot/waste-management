<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotAdmin();

// Fetch all users with role 'user'
$stmt = $pdo->query("SELECT id, username, email, total_score, monthly_score, daily_score, last_played_date FROM users WHERE role='user' ORDER BY total_score DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate summary stats
$totalUsers = count($users);
$totalPoints = array_sum(array_column($users, 'total_score'));
$avgDaily = $totalUsers > 0 ? round(array_sum(array_column($users, 'daily_score')) / $totalUsers) : 0;
?>
<?php include '../includes/header.php'; ?>

<!-- Font Awesome Icons (optional, but adds nice polish) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap;">
    <h1 style="color: var(--primary-dark); margin: 0;">
        <i class="fas fa-users" style="margin-right: 0.5rem;"></i> User Management
    </h1>
    <div style="display: flex; gap: 1rem; margin-top: 0.5rem;">
        <div class="search-wrapper">
            <i class="fas fa-search" style="position: absolute; margin-left: 1rem; margin-top: 0.8rem; color: #666;"></i>
            <input type="text" id="userSearch" placeholder="Search by name or email..." style="padding: 0.7rem 1rem 0.7rem 2.8rem; border-radius: 50px; border: 1px solid rgba(0,0,0,0.1); width: 250px; background: white; box-shadow: var(--shadow-sm);">
        </div>
    </div>
</div>

<!-- Summary Stats Cards -->
<div class="admin-cards" style="margin-bottom: 2rem;">
    <div class="card">
        <i class="fas fa-user-circle" style="font-size: 2rem; color: var(--primary); margin-bottom: 0.5rem;"></i>
        <h3 style="margin-bottom: 0.2rem;"><?= $totalUsers ?></h3>
        <p style="color: #666;">Total Users</p>
    </div>
    <div class="card">
        <i class="fas fa-trophy" style="font-size: 2rem; color: var(--primary); margin-bottom: 0.5rem;"></i>
        <h3 style="margin-bottom: 0.2rem;"><?= number_format($totalPoints) ?></h3>
        <p style="color: #666;">Total Points Earned</p>
    </div>
    <div class="card">
        <i class="fas fa-chart-line" style="font-size: 2rem; color: var(--primary); margin-bottom: 0.5rem;"></i>
        <h3 style="margin-bottom: 0.2rem;"><?= $avgDaily ?></h3>
        <p style="color: #666;">Avg Daily Score</p>
    </div>
</div>

<!-- Users Table (Glass Card) -->
<div class="glass-card" style="padding: 1.5rem; overflow-x: auto;">
    <h2 style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.2rem;">
        <i class="fas fa-database"></i> Registered Users
        <span style="background: var(--primary-light); color: white; padding: 0.2rem 0.8rem; border-radius: 50px; font-size: 0.9rem; margin-left: 0.5rem;">
            <?= $totalUsers ?> total
        </span>
    </h2>
    
    <table class="leaderboard-table" id="userTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Total Score</th>
                <th>Monthly</th>
                <th>Daily (max 10000)</th>
                <th>Last Played</th>
            </tr>
        </thead>
        <tbody id="userTableBody">
            <?php foreach ($users as $user): ?>
            <tr class="user-row">
                <td><strong>#<?= $user['id'] ?></strong></td>
                <td>
                    <i class="fas fa-user" style="color: var(--primary); margin-right: 0.3rem;"></i>
                    <?= htmlspecialchars($user['username']) ?>
                </td>
                <td>
                    <i class="fas fa-envelope" style="color: #666; margin-right: 0.3rem;"></i>
                    <?= htmlspecialchars($user['email']) ?>
                </td>
                <td><span style="font-weight: 700; color: var(--primary-dark);"><?= number_format($user['total_score']) ?></span></td>
                <td><?= number_format($user['monthly_score']) ?></td>
                <td>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span><?= number_format($user['daily_score']) ?></span>
                        <?php 
                        $percentage = min(100, ($user['daily_score'] / 10000) * 100);
                        ?>
                        <div style="width: 50px; height: 6px; background: #e0e0e0; border-radius: 10px; overflow: hidden;">
                            <div style="width: <?= $percentage ?>%; height: 100%; background: var(--primary);"></div>
                        </div>
                    </div>
                </td>
                <td>
                    <?php if ($user['last_played_date']): ?>
                        <i class="fas fa-calendar-alt" style="color: #666; margin-right: 0.3rem;"></i>
                        <?= date('M j, Y', strtotime($user['last_played_date'])) ?>
                    <?php else: ?>
                        <span style="color: #999;">Never</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Empty state (hidden by default) -->
    <div id="noResults" style="display: none; text-align: center; padding: 3rem;">
        <i class="fas fa-search" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
        <h3 style="color: #666;">No users found</h3>
        <p style="color: #999;">Try adjusting your search term.</p>
    </div>
</div>

<!-- Live Search Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('userSearch');
        const tableBody = document.getElementById('userTableBody');
        const rows = document.querySelectorAll('.user-row');
        const noResults = document.getElementById('noResults');
        
        function filterUsers() {
            const searchTerm = searchInput.value.toLowerCase();
            let visibleCount = 0;
            
            rows.forEach(row => {
                const username = row.cells[1].innerText.toLowerCase();
                const email = row.cells[2].innerText.toLowerCase();
                
                if (username.includes(searchTerm) || email.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Show/hide empty state
            if (visibleCount === 0) {
                noResults.style.display = 'block';
                tableBody.style.display = 'none';
            } else {
                noResults.style.display = 'none';
                tableBody.style.display = 'table-row-group';
            }
        }
        
        searchInput.addEventListener('keyup', filterUsers);
    });
</script>

<!-- Back to Dashboard -->
<div style="margin-top: 2rem; text-align: right;">
    <a href="dashboard.php" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

<?php include '../includes/footer.php'; ?>