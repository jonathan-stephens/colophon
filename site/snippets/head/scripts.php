  <!-- Scripts -->
    <script>
      // Theme initialization to prevent flickering
      // Theme init - check user preference first, fallback to system
      (function() {
      const getUserTheme = () => {
          try {
              return localStorage.getItem('theme-preference') || 'system';
          } catch {
              return 'system';
          }
      };

      const getSystemMode = () => window.matchMedia?.('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';

      const getAppliedTheme = (userPref) => {
          if (userPref === 'light') return 'light';
          if (userPref === 'dark') return 'dark';
          return getSystemMode();
      };

      const userPref = getUserTheme();
      const appliedTheme = getAppliedTheme(userPref);
      document.documentElement.dataset.theme = appliedTheme;
      document.documentElement.dataset.themeIcon = appliedTheme;
      })();
    </script>
