document.addEventListener('DOMContentLoaded', () => {
  const links = document.querySelectorAll('a[target="_blank"]');
  links.forEach(l => { l.rel = 'noopener'; });
});

