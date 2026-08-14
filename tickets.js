document.addEventListener('DOMContentLoaded', () => {
  const dateLinks = document.querySelectorAll('.date-options a');
  const timeOptionsContainers = document.querySelectorAll('.time-options');

  dateLinks.forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();

      // Remove 'active' from all date links
      dateLinks.forEach(l => l.classList.remove('active'));

      // Add 'active' to clicked link
      link.classList.add('active');

      const selectedDate = link.dataset.date;

      // Show, hide time options based on selected date
      timeOptionsContainers.forEach(container => {
        if (container.dataset.date === selectedDate) {
          container.style.display = 'flex';
        } else {
          container.style.display = 'none';
        }
      });
    });
  });
});
