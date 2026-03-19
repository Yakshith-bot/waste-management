<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotLoggedIn();

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT daily_score, last_played_date FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$today = date('Y-m-d');
$dailyLimitReached = ($user['last_played_date'] === $today && $user['daily_score'] >= 10000);

// ----- 100 WASTE ITEMS -----
$wasteItems = [
    // Plastic (20 items)
    ['type' => 'plastic', 'emoji' => '🥤', 'label' => 'Plastic Bottle'],
    ['type' => 'plastic', 'emoji' => '🧴', 'label' => 'Shampoo Bottle'],
    ['type' => 'plastic', 'emoji' => '🥛', 'label' => 'Milk Jug'],
    ['type' => 'plastic', 'emoji' => '🧃', 'label' => 'Juice Carton'],
    ['type' => 'plastic', 'emoji' => '🧊', 'label' => 'Plastic Cup'],
    ['type' => 'plastic', 'emoji' => '🧻', 'label' => 'Plastic Wrap'],
    ['type' => 'plastic', 'emoji' => '🪣', 'label' => 'Bucket'],
    ['type' => 'plastic', 'emoji' => '🧺', 'label' => 'Laundry Basket'],
    ['type' => 'plastic', 'emoji' => '🧸', 'label' => 'Toy'],
    ['type' => 'plastic', 'emoji' => '💳', 'label' => 'Credit Card'],
    ['type' => 'plastic', 'emoji' => '🖊️', 'label' => 'Pen'],
    ['type' => 'plastic', 'emoji' => '🧴', 'label' => 'Lotion Bottle'],
    ['type' => 'plastic', 'emoji' => '🧪', 'label' => 'Detergent Bottle'],
    ['type' => 'plastic', 'emoji' => '🧫', 'label' => 'Yogurt Cup'],
    ['type' => 'plastic', 'emoji' => '🥡', 'label' => 'Takeout Container'],
    ['type' => 'plastic', 'emoji' => '🍼', 'label' => 'Baby Bottle'],
    ['type' => 'plastic', 'emoji' => '💧', 'label' => 'Water Jug'],
    ['type' => 'plastic', 'emoji' => '🧂', 'label' => 'Salt Shaker'],
    ['type' => 'plastic', 'emoji' => '☕', 'label' => 'Coffee Lid'],
    ['type' => 'plastic', 'emoji' => '🍶', 'label' => 'Soy Sauce Bottle'],
    
    // Paper (20 items)
    ['type' => 'paper', 'emoji' => '📄', 'label' => 'Newspaper'],
    ['type' => 'paper', 'emoji' => '📦', 'label' => 'Cardboard Box'],
    ['type' => 'paper', 'emoji' => '📚', 'label' => 'Books'],
    ['type' => 'paper', 'emoji' => '📒', 'label' => 'Notebook'],
    ['type' => 'paper', 'emoji' => '✉️', 'label' => 'Envelope'],
    ['type' => 'paper', 'emoji' => '📇', 'label' => 'File Folder'],
    ['type' => 'paper', 'emoji' => '🧾', 'label' => 'Receipt'],
    ['type' => 'paper', 'emoji' => '📰', 'label' => 'Magazine'],
    ['type' => 'paper', 'emoji' => '📃', 'label' => 'Flyer'],
    ['type' => 'paper', 'emoji' => '📋', 'label' => 'Clipboard'],
    ['type' => 'paper', 'emoji' => '📌', 'label' => 'Cork Board'],
    ['type' => 'paper', 'emoji' => '🎫', 'label' => 'Ticket'],
    ['type' => 'paper', 'emoji' => '🏷️', 'label' => 'Sticker'],
    ['type' => 'paper', 'emoji' => '📜', 'label' => 'Scroll'],
    ['type' => 'paper', 'emoji' => '📑', 'label' => 'Bookmark'],
    ['type' => 'paper', 'emoji' => '🗂️', 'label' => 'Divider'],
    ['type' => 'paper', 'emoji' => '📆', 'label' => 'Calendar'],
    ['type' => 'paper', 'emoji' => '📊', 'label' => 'Chart'],
    ['type' => 'paper', 'emoji' => '📈', 'label' => 'Graph'],
    ['type' => 'paper', 'emoji' => '🖼️', 'label' => 'Poster'],
    
    // Glass (20 items)
    ['type' => 'glass', 'emoji' => '🍾', 'label' => 'Glass Bottle'],
    ['type' => 'glass', 'emoji' => '🥃', 'label' => 'Glass Cup'],
    ['type' => 'glass', 'emoji' => '🍷', 'label' => 'Wine Glass'],
    ['type' => 'glass', 'emoji' => '🥂', 'label' => 'Champagne Glass'],
    ['type' => 'glass', 'emoji' => '🍸', 'label' => 'Cocktail Glass'],
    ['type' => 'glass', 'emoji' => '🍶', 'label' => 'Sake Bottle'],
    ['type' => 'glass', 'emoji' => '🧪', 'label' => 'Beaker'],
    ['type' => 'glass', 'emoji' => '🔮', 'label' => 'Crystal Ball'],
    ['type' => 'glass', 'emoji' => '🪞', 'label' => 'Mirror'],
    ['type' => 'glass', 'emoji' => '🪟', 'label' => 'Window Pane'],
    ['type' => 'glass', 'emoji' => '📯', 'label' => 'Horn'],
    ['type' => 'glass', 'emoji' => '🧿', 'label' => 'Evil Eye'],
    ['type' => 'glass', 'emoji' => '💡', 'label' => 'Light Bulb'],
    ['type' => 'glass', 'emoji' => '🕯️', 'label' => 'Glass Candle Holder'],
    ['type' => 'glass', 'emoji' => '🍯', 'label' => 'Honey Jar'],
    ['type' => 'glass', 'emoji' => '🥛', 'label' => 'Milk Bottle'],
    ['type' => 'glass', 'emoji' => '🧂', 'label' => 'Salt Grinder'],
    ['type' => 'glass', 'emoji' => '☕', 'label' => 'Glass Coffee Mug'],
    ['type' => 'glass', 'emoji' => '🍹', 'label' => 'Tropical Drink'],
    ['type' => 'glass', 'emoji' => '🧴', 'label' => 'Glass Jar'],
    
    // Organic (20 items)
    ['type' => 'organic', 'emoji' => '🍌', 'label' => 'Banana Peel'],
    ['type' => 'organic', 'emoji' => '🍎', 'label' => 'Apple Core'],
    ['type' => 'organic', 'emoji' => '🥕', 'label' => 'Carrot Top'],
    ['type' => 'organic', 'emoji' => '🥬', 'label' => 'Lettuce'],
    ['type' => 'organic', 'emoji' => '🍂', 'label' => 'Fallen Leaves'],
    ['type' => 'organic', 'emoji' => '🌿', 'label' => 'Grass Clippings'],
    ['type' => 'organic', 'emoji' => '🥚', 'label' => 'Eggshells'],
    ['type' => 'organic', 'emoji' => '☕', 'label' => 'Coffee Grounds'],
    ['type' => 'organic', 'emoji' => '🍵', 'label' => 'Tea Bag'],
    ['type' => 'organic', 'emoji' => '🥑', 'label' => 'Avocado Pit'],
    ['type' => 'organic', 'emoji' => '🍊', 'label' => 'Orange Peel'],
    ['type' => 'organic', 'emoji' => '🍋', 'label' => 'Lemon Rind'],
    ['type' => 'organic', 'emoji' => '🍉', 'label' => 'Watermelon Rind'],
    ['type' => 'organic', 'emoji' => '🌽', 'label' => 'Corn Cob'],
    ['type' => 'organic', 'emoji' => '🥜', 'label' => 'Peanut Shells'],
    ['type' => 'organic', 'emoji' => '🌰', 'label' => 'Acorns'],
    ['type' => 'organic', 'emoji' => '🍄', 'label' => 'Mushroom'],
    ['type' => 'organic', 'emoji' => '🌻', 'label' => 'Sunflower Stalk'],
    ['type' => 'organic', 'emoji' => '🎃', 'label' => 'Pumpkin'],
    ['type' => 'organic', 'emoji' => '🥔', 'label' => 'Potato Peel'],
    
    // Metal (20 items)
    ['type' => 'metal', 'emoji' => '🥫', 'label' => 'Aluminum Can'],
    ['type' => 'metal', 'emoji' => '🥄', 'label' => 'Spoon'],
    ['type' => 'metal', 'emoji' => '🍴', 'label' => 'Fork'],
    ['type' => 'metal', 'emoji' => '🔪', 'label' => 'Knife'],
    ['type' => 'metal', 'emoji' => '🥤', 'label' => 'Soda Can'],
    ['type' => 'metal', 'emoji' => '🧂', 'label' => 'Salt Can'],
    ['type' => 'metal', 'emoji' => '🔩', 'label' => 'Nut and Bolt'],
    ['type' => 'metal', 'emoji' => '⚙️', 'label' => 'Gear'],
    ['type' => 'metal', 'emoji' => '🔧', 'label' => 'Wrench'],
    ['type' => 'metal', 'emoji' => '🔨', 'label' => 'Hammer'],
    ['type' => 'metal', 'emoji' => '🪛', 'label' => 'Screwdriver'],
    ['type' => 'metal', 'emoji' => '✂️', 'label' => 'Scissors'],
    ['type' => 'metal', 'emoji' => '📎', 'label' => 'Paperclip'],
    ['type' => 'metal', 'emoji' => '📍', 'label' => 'Thumbtack'],
    ['type' => 'metal', 'emoji' => '🔗', 'label' => 'Chain'],
    ['type' => 'metal', 'emoji' => '⛓️', 'label' => 'Link'],
    ['type' => 'metal', 'emoji' => '🛢️', 'label' => 'Oil Can'],
    ['type' => 'metal', 'emoji' => '⏲️', 'label' => 'Timer'],
    ['type' => 'metal', 'emoji' => '🔔', 'label' => 'Bell'],
    ['type' => 'metal', 'emoji' => '⚱️', 'label' => 'Urn'],
];
?>
<?php include '../includes/header.php'; ?>

<!-- Confetti Canvas -->
<canvas id="confetti-canvas" style="display: none;"></canvas>

<h1 style="text-align: center; color: var(--primary-dark); margin-bottom: 0.5rem;">
    🗑️ Waste Segregation Challenge
</h1>
<p style="text-align: center; font-size: 1.2rem; margin-bottom: 2rem;">
    Drag each waste item into the correct bin!
</p>

<?php if ($dailyLimitReached): ?>
    <div class="glass-card" style="background: #ffccbc; text-align: center; max-width: 600px; margin: 0 auto;">
        <h3>⚠️ Daily Limit Reached</h3>
        <p style="font-size: 1.2rem;">You've earned 10000 points today. Come back tomorrow for more!</p>
    </div>
<?php else: ?>
    <!-- Score & Controls -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 1.5rem;">
        <div class="score-badge">
            🏆 Score: <span id="current-score">0</span> / 5
        </div>
        <div>
            <button id="reset-game" class="btn btn-reset">🔄 New Items</button>
        </div>
    </div>

    <!-- Main Game Area (CSS Grid) -->
    <div class="game-container">
        <!-- Waste Items Column -->
        <div id="waste-items" class="glass-card">
            <h3 style="display: flex; align-items: center; gap: 0.8rem;">
                <span style="font-size: 2rem;">📦</span> Waste Items
            </h3>
            <!-- Waste grid – initially empty, populated by JavaScript -->
            <div class="waste-grid" id="waste-grid"></div>
        </div>

        <!-- Bins Column (unchanged) -->
        <div id="bins" class="glass-card">
            <h3 style="display: flex; align-items: center; gap: 0.8rem;">
                <span style="font-size: 2rem;">🗑️</span> Recycling Bins
            </h3>
            <div class="bin-grid">
                <div class="bin-item" data-accept="plastic">
                    <span class="bin-emoji">♻️</span>
                    <span class="bin-label">Plastic</span>
                </div>
                <div class="bin-item" data-accept="paper">
                    <span class="bin-emoji">📦</span>
                    <span class="bin-label">Paper</span>
                </div>
                <div class="bin-item" data-accept="glass">
                    <span class="bin-emoji">🍶</span>
                    <span class="bin-label">Glass</span>
                </div>
                <div class="bin-item" data-accept="organic">
                    <span class="bin-emoji">🌱</span>
                    <span class="bin-label">Organic</span>
                </div>
                <div class="bin-item" data-accept="metal">
                    <span class="bin-emoji">🔩</span>
                    <span class="bin-label">Metal</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit & Message -->
    <div style="text-align: center; margin: 2.5rem 0;">
        <button id="submit-score" class="btn btn-large btn-pulse">🏆 Submit Score</button>
        <div id="score-message" style="margin-top: 1.2rem; min-height: 50px; font-size: 1.2rem;"></div>
    </div>
<?php endif; ?>

<script>
    // Pass PHP variables to JavaScript
    window.userId = <?= $userId ?>;
    window.dailyLimitReached = <?= $dailyLimitReached ? 'true' : 'false' ?>;
    window.BASE_URL = '<?= BASE_URL ?>';
    
    // The complete pool of 100 waste items
    window.wasteItems = <?= json_encode($wasteItems) ?>;
</script>
<script src="<?= BASE_URL ?>assets/js/dragdrop.js"></script>

<?php include '../includes/footer.php'; ?>