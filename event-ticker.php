<?php
// Ensure database connection is available
include_once('./includes/wertyuytre.php');

if (!isset($pdo)) {
    // Check if file exists relative to this file's location
    if (file_exists(__DIR__ . '/includes/db.php')) {
        require_once __DIR__ . '/includes/db.php';
    } elseif (file_exists('includes/db.php')) {
        require_once 'includes/db.php';
    }
}

// Fetch next 5 upcoming or ongoing events
$tickerEvents = [];
if (isset($pdo)) {
    try {
        $tStmt = $pdo->query("
            SELECT title, start_datetime, end_datetime 
            FROM events 
            WHERE end_datetime >= NOW() 
            ORDER BY start_datetime ASC 
            LIMIT 5
        ");
        $tickerEvents = $tStmt->fetchAll();
    } catch (Exception $e) {
        // Silent fail for ticker to avoid breaking page layout
    }
}
?> 
<!-- Event Ticker / Top Banner -->
<div id="top-ticker">
    <div class="container h-100 position-relative">
        <?php if (count($tickerEvents) > 0): ?>
            <?php 
            $isFirst = true;
            foreach ($tickerEvents as $tEvent): 
                // Determine Status Logic
                $now = new DateTime();
                $start = new DateTime($tEvent['start_datetime']);
                $end = new DateTime($tEvent['end_datetime']);
                
                $statusLabel = 'UPCOMING';
                $badgeClass = 'bg-primary';

                if ($now >= $start && $now <= $end) {
                    $statusLabel = 'LIVE';
                    $badgeClass = 'bg-danger';
                } elseif ($start > $now) {
                    // Check if starting within 24 hours
                    $diff = $now->diff($start);
                    if ($diff->days < 1) {
                        $statusLabel = 'SOON';
                        $badgeClass = 'bg-warning text-dark';
                    } else {
                        $statusLabel = 'UPCOMING';
                        $badgeClass = 'bg-primary';
                    }
                }
                
                // Format date for JS countdown
                $dataDate = $start->format('Y-m-d\TH:i:s');
                
                // Active class for first item
                $activeClass = $isFirst ? 'active' : '';
                $isFirst = false;
            ?>
                <div class="ticker-item <?= $activeClass ?>" data-date="<?= $dataDate ?>">
                    <span class="badge <?= $badgeClass ?> me-2"><?= $statusLabel ?></span> 
                    <?= htmlspecialchars($tEvent['title']) ?> 
                    <span class="d-none d-md-inline ms-2 text-muted">starts in:</span> 
                    <span class="countdown-timer">00d 00h 00m 00s</span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Fallback if no events found -->
            <div class="ticker-item active">
                <span class="badge bg-secondary me-2">INFO</span> Welcome to Tech Media Directory - Discover the latest tech events!
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tickerItems = document.querySelectorAll('.ticker-item');
    let currentTickerIndex = 0;

    function updateTicker() {
        // Hide all items
        tickerItems.forEach(item => item.classList.remove('active'));
        
        // Show current item
        const currentItem = tickerItems[currentTickerIndex];
        currentItem.classList.add('active');
        
        // Setup countdown for the active item
        const dateString = currentItem.getAttribute('data-date');
        if (dateString) {
            startCountdown(currentItem, dateString);
        }

        // Move to next item index
        currentTickerIndex = (currentTickerIndex + 1) % tickerItems.length;
    }

    let countdownInterval;

    function startCountdown(element, dateString) {
        const targetDate = new Date(dateString).getTime();
        const timerElement = element.querySelector('.countdown-timer');
        
        if (!timerElement) return;

        // Clear any existing interval
        if (countdownInterval) clearInterval(countdownInterval);

        function updateTimer() {
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance < 0) {
                timerElement.innerHTML = "Event Started";
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            timerElement.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;
        }

        updateTimer(); // Run once immediately
        countdownInterval = setInterval(updateTimer, 1000);
    }

    // Initialize first ticker item immediately
    if (tickerItems.length > 0) {
        const firstItem = tickerItems[0];
        const dateString = firstItem.getAttribute('data-date');
        if (dateString) {
            startCountdown(firstItem, dateString);
        }
        
        // Start rotation if more than one item
        if (tickerItems.length > 1) {
            setInterval(updateTicker, 5000); // Change every 5 seconds
        }
    }
});
</script>