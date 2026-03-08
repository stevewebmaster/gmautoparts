document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.getElementById('nav-toggle');
  var nav = document.querySelector('.nav-main');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      nav.style.display = nav.style.display === 'flex' ? 'none' : 'flex';
      nav.style.position = 'absolute';
      nav.style.top = '100%';
      nav.style.left = '0';
      nav.style.right = '0';
      nav.style.background = 'var(--bg-card)';
      nav.style.flexDirection = 'column';
      nav.style.padding = '1rem';
      nav.style.gap = '0.5rem';
      if (nav.style.display === 'flex') {
        nav.style.borderBottom = '1px solid var(--border)';
      }
    });
  }
});
