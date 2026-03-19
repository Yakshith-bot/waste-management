document.addEventListener('DOMContentLoaded', function() {
    if (window.dailyLimitReached) return;

    // ===== ELEMENTS =====
    const wasteGrid = document.getElementById('waste-grid');
    const binItems = document.querySelectorAll('.bin-item');
    const submitBtn = document.getElementById('submit-score');
    const resetBtn = document.getElementById('reset-game');
    const scoreMsg = document.getElementById('score-message');
    const scoreSpan = document.getElementById('current-score');
    
    let correctDrops = 0;
    const TOTAL_ITEMS = 5; // items per game

    // ===== UTILITIES =====
    // Shuffle array (Fisher–Yates)
    function shuffleArray(arr) {
        for (let i = arr.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [arr[i], arr[j]] = [arr[j], arr[i]];
        }
        return arr;
    }

    // Pick 5 random UNIQUE items from the global pool
    function getRandomWasteItems(count = TOTAL_ITEMS) {
        const shuffled = shuffleArray([...window.wasteItems]);
        return shuffled.slice(0, count);
    }

    // Render waste items into the grid
    function renderWasteItems(items) {
        wasteGrid.innerHTML = items.map(item => `
            <div class="waste-item" draggable="true" data-type="${item.type}">
                <span class="waste-emoji">${item.emoji}</span>
                <span class="waste-label">${item.label}</span>
            </div>
        `).join('');
        attachDragListeners();
    }

    // ===== CONFETTI =====
    function startConfetti() {
        const canvas = document.getElementById('confetti-canvas');
        canvas.style.display = 'block';
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        
        const particles = [];
        for (let i = 0; i < 100; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height * 0.2,
                size: Math.random() * 5 + 2,
                speedY: Math.random() * 3 + 2,
                speedX: Math.random() * 2 - 1,
                color: `hsl(${Math.random() * 60 + 30}, 80%, 60%)`
            });
        }
        
        function draw() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            let stillFalling = false;
            particles.forEach(p => {
                p.y += p.speedY;
                p.x += p.speedX;
                if (p.y < canvas.height) stillFalling = true;
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                ctx.fillStyle = p.color;
                ctx.fill();
            });
            if (stillFalling) {
                requestAnimationFrame(draw);
            } else {
                canvas.style.display = 'none';
            }
        }
        draw();
    }

    // ===== DRAG HANDLERS =====
    function handleDragStart(e) {
        this.classList.add('dragging');
        e.dataTransfer.setData('text/plain', this.dataset.type);
        e.dataTransfer.effectAllowed = 'copy';
    }

    function handleDragEnd(e) {
        this.classList.remove('dragging');
    }

    // ===== BIN EVENTS =====
    binItems.forEach(bin => {
        bin.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'copy';
            this.style.transform = 'translateY(-6px) scale(1.03)';
        });

        bin.addEventListener('dragleave', function(e) {
            this.style.transform = 'translateY(0) scale(1)';
        });

        bin.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.transform = 'translateY(0) scale(1)';
            
            const wasteType = e.dataTransfer.getData('text/plain');
            const binType = this.dataset.accept;
            const draggedItem = document.querySelector(`.waste-item[draggable="true"][data-type="${wasteType}"]`);

            if (!draggedItem) {
                // Item already dropped or removed
                return;
            }

            if (wasteType === binType) {
                // ✅ CORRECT DROP
                this.classList.add('bin-success');
                setTimeout(() => this.classList.remove('bin-success'), 300);
                
                draggedItem.classList.add('waste-remove');
                setTimeout(() => {
                    draggedItem.remove();
                    correctDrops++;
                    updateScore();
                    
                    // Check for perfect game (5/5 correct)
                    if (correctDrops === TOTAL_ITEMS) {
                        startConfetti();
                        scoreMsg.innerHTML = '<span style="color: #2e7d32; font-size: 1.4rem;">🎉 PERFECT! You correctly sorted all items! 🎉</span>';
                    }
                    
                    // If all items are gone (correct or wrong), game ends silently – no extra message
                }, 150);
            } else {
                // ❌ WRONG DROP – item disappears, NO score
                this.classList.add('bin-error');
                setTimeout(() => this.classList.remove('bin-error'), 300);
                
                draggedItem.classList.add('waste-remove');
                setTimeout(() => {
                    draggedItem.remove();
                    // Do NOT increment correctDrops
                    // No confetti
                    // No perfect message
                    
                    // If all items are gone (all wrong), show a message? (optional)
                    if (document.querySelectorAll('.waste-item[draggable="true"]').length === 0) {
                        scoreMsg.innerHTML = '<span style="color: #c62828;">😓 No items left. Try again with "New Items"!</span>';
                    }
                }, 150);
            }
        });
    });

    // ===== UPDATE SCORE DISPLAY =====
    function updateScore() {
        if (scoreSpan) {
            scoreSpan.textContent = correctDrops;
        }
    }

    // ===== SUBMIT SCORE =====
    submitBtn.addEventListener('click', function() {
        if (correctDrops === 0) {
            scoreMsg.innerHTML = '<span style="color: #c62828;">❌ Drop at least one item first!</span>';
            return;
        }
        
        const points = correctDrops * 1; // 1 point per correct drop
        fetch(window.BASE_URL + 'games/submit_score.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `score=${points}&game=segregation`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                scoreMsg.innerHTML = `<span style="color: #2e7d32; font-weight: 700;">✔️ You earned ${points} points! Daily: ${data.daily}/10000</span>`;
                if (data.limit) {
                    submitBtn.disabled = true;
                    submitBtn.classList.remove('btn-pulse');
                    submitBtn.style.opacity = '0.6';
                }
                submitBtn.classList.add('btn-pulse');
            } else {
                scoreMsg.innerHTML = `<span style="color: #c62828;">❌ ${data.message}</span>`;
            }
        })
        .catch(() => {
            scoreMsg.innerHTML = '<span style="color: #c62828;">❌ Error submitting score.</span>';
        });
    });

    // ===== RESET GAME =====
    function resetGame() {
        // Reset score
        correctDrops = 0;
        updateScore();
        scoreMsg.innerHTML = '';
        
        // Get 5 NEW random items (different from before)
        const newItems = getRandomWasteItems(TOTAL_ITEMS);
        renderWasteItems(newItems);
        
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.classList.add('btn-pulse');
        submitBtn.style.opacity = '1';
    }

    // Attach drag listeners to all .waste-item elements
    function attachDragListeners() {
        const wasteItems = document.querySelectorAll('.waste-item[draggable="true"]');
        wasteItems.forEach(item => {
            item.addEventListener('dragstart', handleDragStart);
            item.addEventListener('dragend', handleDragEnd);
        });
    }

    // ===== INITIAL SETUP =====
    if (resetBtn) {
        resetBtn.addEventListener('click', resetGame);
    }

    // Start the game with 5 random items
    const initialItems = getRandomWasteItems(TOTAL_ITEMS);
    renderWasteItems(initialItems);
    updateScore();
});