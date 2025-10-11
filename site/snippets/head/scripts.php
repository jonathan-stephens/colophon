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

      /**
       * Service Worker Registration
       * Place this in your main site template or header
       */

      if ('serviceWorker' in navigator) {
        console.log('✅ Service Worker supported');

        window.addEventListener('load', async () => {
          try {
            console.log('🔧 Registering Service Worker...');

            // Register with explicit scope
            const registration = await navigator.serviceWorker.register('/sw.js', {
              scope: '/',
              updateViaCache: 'none' // Always check for updates
            });

            console.log('✅ Service Worker registered:', registration.scope);

            // Check if there's an update waiting
            if (registration.waiting) {
              console.log('⏳ Service Worker update waiting');

              // Optionally auto-update
              if (confirm('A new version is available. Update now?')) {
                registration.waiting.postMessage({ type: 'SKIP_WAITING' });
                window.location.reload();
              }
            }

            // Listen for updates
            registration.addEventListener('updatefound', () => {
              const newWorker = registration.installing;
              console.log('🆕 Service Worker update found');

              newWorker.addEventListener('statechange', () => {
                console.log('🔄 Service Worker state:', newWorker.state);

                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                  console.log('⏳ New Service Worker installed, waiting to activate');

                  // Optionally notify user
                  if (confirm('App updated! Reload to use new version?')) {
                    newWorker.postMessage({ type: 'SKIP_WAITING' });
                    window.location.reload();
                  }
                }
              });
            });

            // Check if page is currently controlled
            if (navigator.serviceWorker.controller) {
              console.log('✅ Page is controlled by Service Worker');
              console.log('   Scope:', registration.scope);
              console.log('   Script URL:', registration.active.scriptURL);
            } else {
              console.log('⚠️ Page is NOT controlled by Service Worker yet');
              console.log('   This is normal on first visit - refresh the page');
            }

            // Listen for controller change (when SW takes control)
            navigator.serviceWorker.addEventListener('controllerchange', () => {
              console.log('🔄 Service Worker controller changed - reloading');
              window.location.reload();
            });

            // Add a status check function to window for debugging
            window.checkSWStatus = async () => {
              const reg = await navigator.serviceWorker.getRegistration();
              console.log('📊 Service Worker Status:');
              console.log('  - Registered:', !!reg);
              console.log('  - Scope:', reg?.scope);
              console.log('  - Active:', !!reg?.active);
              console.log('  - Waiting:', !!reg?.waiting);
              console.log('  - Installing:', !!reg?.installing);
              console.log('  - Controlling:', !!navigator.serviceWorker.controller);

              if (reg?.active) {
                console.log('  - Active state:', reg.active.state);
                console.log('  - Script URL:', reg.active.scriptURL);
              }

              return reg;
            };

            // Add manual update checker
            window.updateSW = async () => {
              const reg = await navigator.serviceWorker.getRegistration();
              if (reg) {
                console.log('🔄 Checking for Service Worker updates...');
                await reg.update();
                console.log('✅ Update check complete');
              }
            };

          } catch (err) {
            console.error('❌ Service Worker registration failed:', err);
            console.error('   Error details:', err.message);
            console.error('   Stack:', err.stack);
          }
        });

        // Handle messages from Service Worker
        navigator.serviceWorker.addEventListener('message', (event) => {
          console.log('💬 Message from Service Worker:', event.data);

          if (event.data && event.data.type === 'CACHE_CLEARED') {
            console.log('✅ Cache cleared successfully');
          }
        });

      } else {
        console.error('❌ Service Workers not supported in this browser');
        console.log('   Share target functionality will not work');
      }

      // Global function to unregister (for debugging)
      window.unregisterSW = async () => {
        const registrations = await navigator.serviceWorker.getRegistrations();
        for (const registration of registrations) {
          console.log('🗑️ Unregistering:', registration.scope);
          await registration.unregister();
        }
        console.log('✅ All Service Workers unregistered');
        console.log('   Refresh the page to re-register');
      };

      console.log('✅ Service Worker registration script loaded');
      console.log('   Type checkSWStatus() in console to check status');
      console.log('   Type updateSW() to force update check');
      console.log('   Type unregisterSW() to unregister (for debugging)');

    </script>
