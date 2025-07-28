document.addEventListener('DOMContentLoaded', () => {
  const popup = document.getElementById('celebration-popup');

  // Handle Place Bid buttons
  document.querySelectorAll('.bid-btn').forEach(button => {
    button.addEventListener('click', () => {
      if (popup) {
        popup.style.display = 'block';
        setTimeout(() => {
          popup.style.display = 'none';
        }, 2000);
      }
    });
  });

  // Handle + and - buttons for bid adjustment
  document.querySelectorAll('.adjust-bid').forEach(button => {
    button.addEventListener('click', () => {
      const container = button.closest('.bid-info-box') || button.closest('.exclusive-item');
      const priceSpan = container.querySelector('.price span');
      const incrementSelect = container.querySelector('.bid-increment');
      if (!priceSpan || !incrementSelect) return;

      let currentPrice = parseInt(priceSpan.textContent);
      const increment = parseInt(incrementSelect.value);

      if (button.classList.contains('plus')) {
        currentPrice += increment;
      } else if (button.classList.contains('minus')) {
        currentPrice = Math.max(0, currentPrice - increment);
      }

      priceSpan.textContent = currentPrice;
    });
  });

  // Handle countdown timers
  document.querySelectorAll('.timer').forEach(timer => {
    let timeLeft = parseInt(timer.getAttribute('data-time'));
    const interval = setInterval(() => {
      if (timeLeft <= 0) {
        clearInterval(interval);
        timer.textContent = 'Time Left: Bidding Ended';
      } else {
        const minutes = Math.floor(timeLeft / 60).toString().padStart(2, '0');
        const seconds = (timeLeft % 60).toString().padStart(2, '0');
        timer.textContent = `Time Left: ${minutes}:${seconds}`;
        timeLeft--;
      }
    }, 1000);
  });
});
