// Global permission utility
// Expects window.APP_PERMISSIONS = { module: { action: true } }
// Use data-permission="module:action" or hierarchical wildcard support via server.
(function(){
  function parseAttr(str){
    if(!str) return [];
    return str.split(',').map(s=>s.trim()).filter(Boolean);
  }
  function hasPerm(s){
    if(!window.APP_PERMISSIONS) return false;
    const [module, action] = s.split(':');
    if(!module||!action) return false;
    const mod = window.APP_PERMISSIONS[module];
    if(!mod) return false;
    if(mod['*']) return true;
    if(mod[action]) return true;
    if(action.includes('.')){
      const parts = action.split('.');
      while(parts.length>1){
        parts.pop();
        const pref = parts.join('.') + '.*';
        if(mod[pref]) return true;
      }
    }
    return false;
  }
  function applyPermissions(){
    document.querySelectorAll('[data-permission]')
      .forEach(el=>{
        const reqs = parseAttr(el.getAttribute('data-permission'));
        const ok = reqs.some(r=>hasPerm(r));
        if(!ok){
          if(el.dataset.permissionMode === 'disable'){
            el.disabled = true;
            el.classList.add('disabled');
            if(el.tagName === 'A'){
              el.addEventListener('click', e=>e.preventDefault());
            }
          } else {
            el.remove();
          }
        }
      });
  }
  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', applyPermissions);
  } else {
    applyPermissions();
  }
})();
