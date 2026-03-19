document.addEventListener('DOMContentLoaded', function() {
    if (window.dailyLimitReached) return;

    // ----- QUESTION BANK -----
    const questions = [
        {
            question: "What is the most environmentally friendly way to dispose of a plastic bottle?",
            options: ["Throw in general waste", "Recycle in plastic bin", "Burn it", "Bury it"],
            correct: 1,
            explanation: "Plastic bottles should be rinsed and placed in the plastic recycling bin."
        },
        {
            question: "Which item belongs to organic waste?",
            options: ["Glass", "Battery", "Banana peel", "Plastic bag"],
            correct: 2,
            explanation: "Banana peels are compostable and belong in organic waste."
        },
        {
            question: "What does 'e-waste' refer to?",
            options: ["Electronic waste", "Excessive waste", "Edible waste", "Empty waste"],
            correct: 0,
            explanation: "E-waste includes discarded electronics like phones, computers, and batteries."
        },
        {
            question: "How long does it take for a plastic bottle to decompose?",
            options: ["20 years", "50 years", "100 years", "450 years"],
            correct: 3,
            explanation: "Plastic bottles can take up to 450 years to decompose in landfills."
        },
        {
            question: "Which of these is compostable?",
            options: ["Aluminum can", "Styrofoam", "Apple core", "Plastic wrap"],
            correct: 2,
            explanation: "Apple cores are organic and compostable."
        }
    ];

    const TOTAL_QUESTIONS = questions.length;
    const QUIZ_TIME = 20; // seconds per quiz

    // ----- STATE -----
    let currentIndex = 0;
    let score = 0;
    let timeLeft = QUIZ_TIME;
    let timerInterval = null;
    let quizActive = true;
    let answerSelected = false;

    // ----- DOM ELEMENTS (created dynamically) -----
    const root = document.getElementById('quiz-root');
    if (!root) return;

    // ----- BUILD UI -----
    function buildUI() {
        root.innerHTML = `
            <div class="quiz-timer-section">
                <div class="timer-ring-container">
                    <div class="timer-ring-bg"></div>
                    <div class="timer-ring-progress" id="timer-ring"></div>
                    <div class="timer-text" id="timer-text">${QUIZ_TIME}s</div>
                    <div class="timer-label">time left</div>
                </div>
            </div>
            <div class="quiz-score-badge">
                🏆 Score: <span id="quiz-score">0</span> / ${TOTAL_QUESTIONS}
            </div>
            <div id="question-container">
                <!-- Question card and options will be injected here -->
            </div>
            <div id="progress-dots" class="progress-indicator"></div>
            <div id="quiz-message" style="text-align: center; min-height: 40px; margin-top: 1rem;"></div>
        `;
    }
    buildUI();

    const questionContainer = document.getElementById('question-container');
    const progressDots = document.getElementById('progress-dots');
    const timerRing = document.getElementById('timer-ring');
    const timerText = document.getElementById('timer-text');
    const quizScoreSpan = document.getElementById('quiz-score');
    const messageEl = document.getElementById('quiz-message');

    // ----- RENDER CURRENT QUESTION -----
    function renderQuestion(index) {
        const q = questions[index];
        questionContainer.innerHTML = `
            <div class="question-card">
                <div class="question-icon">❓</div>
                <div class="question-text">${q.question}</div>
            </div>
            <div class="options-grid" id="options-grid">
                ${q.options.map((opt, i) => `
                    <div class="quiz-option-card" data-option-index="${i}">
                        ${opt}
                    </div>
                `).join('')}
            </div>
        `;

        // Add click listeners to options
        const optionCards = document.querySelectorAll('.quiz-option-card');
        optionCards.forEach(card => {
            card.addEventListener('click', () => handleOptionClick(parseInt(card.dataset.optionIndex)));
        });

        // Update progress dots
        updateProgressDots(index);
    }

    // ----- PROGRESS DOTS -----
    function updateProgressDots(activeIndex) {
        let dotsHTML = '';
        for (let i = 0; i < TOTAL_QUESTIONS; i++) {
            let className = 'progress-dot';
            if (i === activeIndex) className += ' active';
            if (i < activeIndex) className += ' completed';
            dotsHTML += `<div class="${className}"></div>`;
        }
        progressDots.innerHTML = dotsHTML;
    }

    // ----- HANDLE ANSWER SELECTION -----
    function handleOptionClick(selectedIndex) {
        if (!quizActive || answerSelected) return;
        answerSelected = true;

        const q = questions[currentIndex];
        const isCorrect = (selectedIndex === q.correct);
        const optionCards = document.querySelectorAll('.quiz-option-card');

        // Highlight correct / wrong
        optionCards[q.correct].classList.add('correct');
        if (!isCorrect) {
            optionCards[selectedIndex].classList.add('wrong');
        } else {
            score++;
            updateScore();
            // Show confetti if perfect score? (optional)
        }

        // Disable all options
        optionCards.forEach(card => {
            card.classList.add('disabled');
            card.style.pointerEvents = 'none';
        });

        // Show brief explanation (optional)
        messageEl.innerHTML = `<span style="color: ${isCorrect ? '#2e7d32' : '#c62828'};">
            ${isCorrect ? '✅ Correct!' : '❌ Wrong!'} ${q.explanation || ''}
        </span>`;

        // Move to next question after delay
        setTimeout(() => {
            if (currentIndex < TOTAL_QUESTIONS - 1) {
                currentIndex++;
                answerSelected = false;
                renderQuestion(currentIndex);
                messageEl.innerHTML = ''; // clear message
            } else {
                // Quiz finished
                endQuiz();
            }
        }, 1200);
    }

    // ----- UPDATE SCORE DISPLAY -----
    function updateScore() {
        if (quizScoreSpan) {
            quizScoreSpan.textContent = score;
        }
    }

    // ----- TIMER -----
    function startTimer() {
        timeLeft = QUIZ_TIME;
        updateTimerDisplay();
        timerInterval = setInterval(() => {
            timeLeft--;
            updateTimerDisplay();

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                timerInterval = null;
                if (quizActive) {
                    // Time's up – force end quiz
                    endQuiz();
                }
            }
        }, 1000);
    }

    function updateTimerDisplay() {
        timerText.textContent = `${timeLeft}s`;
        
        // Calculate conic gradient degrees (360 * remaining percentage)
        const percentage = timeLeft / QUIZ_TIME;
        const degrees = percentage * 360;
        timerRing.style.background = `conic-gradient(#2e7d32 ${degrees}deg, #e0e0e0 0deg)`;

        // Low time warning
        if (timeLeft <= 5) {
            timerRing.classList.add('low-time');
            timerText.classList.add('low-time');
        } else {
            timerRing.classList.remove('low-time');
            timerText.classList.remove('low-time');
        }
    }

    // ----- END QUIZ -----
    function endQuiz() {
        if (!quizActive) return;
        quizActive = false;
        clearInterval(timerInterval);

        // Disable any remaining options
        const optionCards = document.querySelectorAll('.quiz-option-card');
        optionCards.forEach(card => {
            card.classList.add('disabled');
            card.style.pointerEvents = 'none';
        });

        // Show result card
        const pointsEarned = score; // 1 point per correct answer
        const maxPoints = TOTAL_QUESTIONS;
        const perfect = (score === maxPoints);

        questionContainer.innerHTML = `
            <div class="result-card">
                <div class="result-icon">${perfect ? '🏆' : '📊'}</div>
                <h2 style="margin-bottom: 0.5rem;">Quiz Completed!</h2>
                <div class="result-score">${score} / ${maxPoints}</div>
                <p class="result-message">
                    ${perfect ? '🌟 Perfect Score! Amazing!' : 'Great effort! Keep learning.'}
                </p>
                <div style="margin-top: 1.5rem;">
                    <button id="submit-quiz-score" class="btn btn-large">🏆 Submit Score (${pointsEarned} pts)</button>
                    <button id="play-again" class="btn btn-outline" style="margin-left: 1rem;">🔄 Play Again</button>
                </div>
            </div>
        `;

        // Attach submit and play again handlers
        const submitBtn = document.getElementById('submit-quiz-score');
        const playAgainBtn = document.getElementById('play-again');

        if (submitBtn) {
            submitBtn.addEventListener('click', function() {
                submitScore(pointsEarned);
            });
        }

        if (playAgainBtn) {
            playAgainBtn.addEventListener('click', function() {
                resetQuiz();
            });
        }
    }

    // ----- SUBMIT SCORE -----
    function submitScore(points) {
        if (points === 0) {
            messageEl.innerHTML = '<span style="color: #c62828;">❌ No points earned.</span>';
            return;
        }

        fetch(window.BASE_URL + 'games/submit_score.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `score=${points}&game=quiz`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                messageEl.innerHTML = `<span style="color: #2e7d32; font-weight: 700;">✔️ You earned ${points} points! Daily: ${data.daily}/10000</span>`;
                if (data.limit) {
                    // Daily limit reached – disable further play?
                    const playAgain = document.getElementById('play-again');
                    if (playAgain) playAgain.style.display = 'none';
                }
            } else {
                messageEl.innerHTML = `<span style="color: #c62828;">❌ ${data.message}</span>`;
            }
        })
        .catch(() => {
            messageEl.innerHTML = '<span style="color: #c62828;">❌ Error submitting score.</span>';
        });
    }

    // ----- RESET QUIZ -----
    function resetQuiz() {
        currentIndex = 0;
        score = 0;
        timeLeft = QUIZ_TIME;
        quizActive = true;
        answerSelected = false;
        updateScore();

        // Rebuild UI and start fresh
        buildUI();
        // Reassign element references
        // (simplify: we can just re-run initialisation)
        location.reload(); // quick and reliable
    }

    // ----- INITIALISE -----
    renderQuestion(0);
    startTimer();
    updateScore();
});