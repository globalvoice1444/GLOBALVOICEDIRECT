/* v3: friendly picture for "no rows" messages already on the page (text unchanged). */
(function(){if(!document.documentElement.classList.contains('lx-v3'))return;
 var art='<svg width="96" height="64" viewBox="0 0 96 64" fill="none" aria-hidden="true"><rect x="10" y="8" width="76" height="48" rx="12" fill="#eef2ff"/><rect x="22" y="20" width="36" height="5" rx="2.5" fill="#c7d2fe"/><rect x="22" y="31" width="52" height="5" rx="2.5" fill="#e0e7ff"/><rect x="22" y="42" width="26" height="5" rx="2.5" fill="#e0e7ff"/><circle cx="74" cy="20" r="7" fill="#4f46e5"/></svg>';
 function deco(){document.querySelectorAll('.gp-content .gvd-vo p.muted, .gp-content .gvd-vo td.muted, .gp-content .gvd-vo div.muted').forEach(function(p){if(p.dataset.lx||!/^(no |nothing|there are no)/i.test(p.textContent.trim()))return;p.dataset.lx=1;var w=document.createElement('div');w.className='lx-emp';w.innerHTML=art;p.parentNode.insertBefore(w,p);w.appendChild(p);});}
 deco();new MutationObserver(deco).observe(document.querySelector('.gp-content')||document.body,{childList:true,subtree:true});
})();
