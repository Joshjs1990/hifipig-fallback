(function(){function ready(fn){if(document.readyState!=="loading")fn();else document.addEventListener("DOMContentLoaded",fn,{once:!0})}
ready(function(){const header=document.querySelector(".site-header");const nav=document.querySelector(".site-header .site-nav");if(!nav)return;const ul=nav.querySelector("ul#primary-menu")||nav.querySelector("ul.menu")||nav.querySelector("ul");if(!ul)return;if(!ul.id)ul.id="primary-menu";let moreLi=ul.querySelector(":scope > .menu-item-more");if(!moreLi){moreLi=document.createElement("li");moreLi.className="menu-item menu-item-has-children menu-item-more";const a=document.createElement("a");a.href="#";a.textContent="More";a.setAttribute("aria-haspopup","true");a.setAttribute("aria-expanded","false");a.addEventListener("click",(e)=>e.preventDefault());const sub=document.createElement("ul");sub.className="sub-menu";moreLi.appendChild(a);moreLi.appendChild(sub);ul.appendChild(moreLi)}
const moreLink=moreLi.querySelector("a");const moreSub=moreLi.querySelector(".sub-menu");function topItems(){return Array.from(ul.children).filter((li)=>li!==moreLi)}
function resetMore(){Array.from(moreSub.children).forEach((li)=>ul.insertBefore(li,moreLi))}
let didFirstLayout=!1;function markReadyOnce(){if(didFirstLayout)return;didFirstLayout=!0;document.body.classList.add("priority-nav-ready")}
function setMoreActive(active){moreLi.classList.toggle("is-active",!!active);if(moreLink)moreLink.setAttribute("aria-expanded",active?"true":"false");}
function fits(){return ul.scrollWidth<=nav.clientWidth}
function updatePriority(){if(window.matchMedia("(max-width: 900px)").matches){resetMore();setMoreActive(!1);markReadyOnce();return}
resetMore();setMoreActive(!1);if(fits()){markReadyOnce();return}
setMoreActive(!0);while(!fits()&&topItems().length>0){const items=topItems();const last=items[items.length-1];if(!last)break;moreSub.insertBefore(last,moreSub.firstChild)}
if(!moreSub.children.length)setMoreActive(!1);markReadyOnce()}
const schedulePriority=(()=>{let raf=0;return()=>{if(raf)cancelAnimationFrame(raf);raf=requestAnimationFrame(updatePriority)}})();if("ResizeObserver" in window){const ro=new ResizeObserver(schedulePriority);ro.observe(nav);ro.observe(ul);if(header)ro.observe(header);}
window.addEventListener("resize",schedulePriority,{passive:!0});if(document.fonts&&document.fonts.ready){document.fonts.ready.then(schedulePriority).catch(()=>{})}
window.addEventListener("load",schedulePriority,{passive:!0});updatePriority();requestAnimationFrame(updatePriority)})})()
;