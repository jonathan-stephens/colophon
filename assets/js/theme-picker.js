const THEME_STORAGE_KEY = 'theme';
const cachedTheme = localStorage.getItem(THEME_STORAGE_KEY);
const themePicker = document.getElementById('theme-picker');

if (themePicker) {
  const initialTheme = cachedTheme || 'auto';
  themePicker.querySelector('input[checked]').removeAttribute('checked');
  themePicker.querySelector(`input[value="${initialTheme}"]`).setAttribute('checked', '');
  themePicker.addEventListener('change', (e) => {
    const theme = e.target.value;
    if (theme === 'auto') {
      localStorage.removeItem(THEME_STORAGE_KEY);
    } else {
      localStorage.setItem(THEME_STORAGE_KEY, theme);
    }
  });
}
