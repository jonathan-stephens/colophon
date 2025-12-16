  <!-- Scripts -->
    <script>
    // Theme init
      (()=>{const t=()=>{try{return localStorage.getItem('theme-preference')||'system'}catch{return'system'}},r=p=>p!=='system'?p:window.matchMedia?.('(prefers-color-scheme: dark)').matches?'dark':'light',theme=r(t());document.documentElement.dataset.theme=theme;document.documentElement.dataset.themeIcon=theme})();
    // Service Worker
      if('serviceWorker'in navigator)window.addEventListener('load',()=>{navigator.serviceWorker.register('/sw.js',{scope:'/',updateViaCache:'none'}).then(r=>r.addEventListener('updatefound',()=>r.installing?.addEventListener('statechange',()=>{if(r.installing.state==='installed'&&navigator.serviceWorker.controller)console.log('Update available - refresh to activate')}))).catch(e=>console.error('SW registration failed:',e));navigator.serviceWorker.addEventListener('controllerchange',()=>window.location.reload())});
    </script>
