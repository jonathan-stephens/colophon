(function(){"use strict";try{if(typeof document<"u"){var r=document.createElement("style");r.appendChild(document.createTextNode('kirby-loop{--color-neutral-h:900;--color-neutral-c:0;--color-accent-h:900;--color-accent-c:.18;--color-accent-l:.75;--color-accent-dark-factor:.4;--color-accent-light-factor:1.2;--color-neutral-l-0:1;--color-neutral-l-100:.95;--color-neutral-l-200:.9;--color-neutral-l-300:.7;--color-neutral-l-400:.6;--color-neutral-l-600:.4;--color-neutral-l-500:.5;--color-neutral-l-700:.3;--color-neutral-l-800:.2;--color-neutral-l-900:.1;--color-neutral-l-1000:0}kirby-loop[theme=dark]{--color-accent-l:.85;--color-neutral-l-0:0;--color-neutral-l-100:.1;--color-neutral-l-200:.2;--color-neutral-l-300:.45;--color-neutral-l-400:.5;--color-neutral-l-600:.55;--color-neutral-l-500:.6;--color-neutral-l-700:.7;--color-neutral-l-800:.8;--color-neutral-l-900:.95;--color-neutral-l-1000:1;--shadow-s:0 .1em .25em oklch(var(--color-neutral-l-100)var(--color-neutral-c)var(--color-neutral-h)/.1);--shadow-m:0 2px 8px oklch(var(--color-neutral-l-100)var(--color-neutral-c)var(--color-neutral-h)/.08),0 8px 16px oklch(var(--color-neutral-l-100)var(--color-neutral-c)var(--color-neutral-h)/.12),0 16px 24px oklch(var(--color-neutral-l-100)var(--color-neutral-c)var(--color-neutral-h)/.08);--shadow-l:0 4px 16px oklch(var(--color-neutral-l-100)var(--color-neutral-c)var(--color-neutral-h)/.08),0 12px 32px oklch(var(--color-neutral-l-100)var(--color-neutral-c)var(--color-neutral-h)/.12),0 24px 48px oklch(var(--color-neutral-l-100)var(--color-neutral-c)var(--color-neutral-h)/.16),0 48px 80px oklch(var(--color-neutral-l-100)var(--color-neutral-c)var(--color-neutral-h)/.08);--shadow-light-edge:inset 1px 1px 1px oklch(var(--color-neutral-l-1000)var(--color-neutral-c)var(--color-neutral-h)/.3);--shadow-dark-edge:inset -1px -1px 1px oklch(var(--color-neutral-l-100)var(--color-neutral-c)var(--color-neutral-h)/.3);--background-glass:linear-gradient(135deg,transparent,var(--color-base-background-o-50));--panel-threads-background:oklch(var(--color-neutral-l-200)var(--color-neutral-c)var(--color-neutral-h)/.99)}kirby-loop{--color-base:var(--color-neutral-900);--color-base-background:var(--color-neutral-0);--color-base-background-o-5:oklch(var(--color-neutral-l-0)var(--color-neutral-c)var(--color-neutral-h)/.05);--color-base-background-o-10:oklch(var(--color-neutral-l-0)var(--color-neutral-c)var(--color-neutral-h)/.1);--color-base-background-o-20:oklch(var(--color-neutral-l-0)var(--color-neutral-c)var(--color-neutral-h)/.2);--color-base-background-o-50:oklch(var(--color-neutral-l-0)var(--color-neutral-c)var(--color-neutral-h)/.5);--color-base-background-o-60:oklch(var(--color-neutral-l-0)var(--color-neutral-c)var(--color-neutral-h)/.6);--color-base-background-o-75:oklch(var(--color-neutral-l-0)var(--color-neutral-c)var(--color-neutral-h)/.75);--color-base-background-o-95:oklch(var(--color-neutral-l-0)var(--color-neutral-c)var(--color-neutral-h)/.95);--color-accent-light:oklch(calc(var(--color-accent-l)*var(--color-accent-light-factor))var(--color-accent-c)var(--color-accent-h));--color-accent:oklch(var(--color-accent-l)var(--color-accent-c)var(--color-accent-h));--color-accent-dark:oklch(calc(var(--color-accent-l)*var(--color-accent-dark-factor))var(--color-accent-c)var(--color-accent-h));--color-neutral-0:oklch(var(--color-neutral-l-0)var(--color-neutral-c)var(--color-neutral-h));--color-neutral-100:oklch(var(--color-neutral-l-100)var(--color-neutral-c)var(--color-neutral-h));--color-neutral-200:oklch(var(--color-neutral-l-200)var(--color-neutral-c)var(--color-neutral-h));--color-neutral-300:oklch(var(--color-neutral-l-300)var(--color-neutral-c)var(--color-neutral-h));--color-neutral-400:oklch(var(--color-neutral-l-400)var(--color-neutral-c)var(--color-neutral-h));--color-neutral-500:oklch(var(--color-neutral-l-500)var(--color-neutral-c)var(--color-neutral-h));--color-neutral-600:oklch(var(--color-neutral-l-600)var(--color-neutral-c)var(--color-neutral-h));--color-neutral-700:oklch(var(--color-neutral-l-700)var(--color-neutral-c)var(--color-neutral-h));--color-neutral-800:oklch(var(--color-neutral-l-800)var(--color-neutral-c)var(--color-neutral-h));--color-neutral-900:oklch(var(--color-neutral-l-900)var(--color-neutral-c)var(--color-neutral-h));--color-neutral-1000:oklch(var(--color-neutral-l-1000)var(--color-neutral-c)var(--color-neutral-h));--color-success:oklch(.65 .15 150);--color-warning:oklch(.75 .15 80);--color-error:oklch(.65 .18 25);--color-info:oklch(.65 .15 220);--font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol",sans-serif;--line-height:1.4;--font-weight-light:300;--font-weight-normal:400;--font-weight-medium:550;--font-weight-bold:700;--font-size-3:clamp(1.9531rem,1.4262rem + 1.7565vw,3.5339rem);--font-size-4:clamp(1.5625rem,1.2503rem + 1.0408vw,2.4992rem);--font-size-5:clamp(1.25rem,1.0775rem + .575vw,1.7675rem);--font-size-6:clamp(1rem,.9167rem + .2778vw,1.25rem);--font-size-7:clamp(.8rem,.772rem + .0934vw,.884rem);--font-size-8:clamp(.6252rem,.6449rem + -.0165vw,.64rem);--border-radius-s:.125rem;--border-radius:.25rem;--border-radius-rounded:4096px;--space-2xs:clamp(.25rem,.2292rem + .0694vw,.3125rem);--space-xs:clamp(.5rem,.4583rem + .1389vw,.625rem);--space-s:clamp(1rem,.9167rem + .2778vw,1.25rem);--space-m:clamp(1.5rem,1.375rem + .4167vw,1.875rem);--space-l:clamp(2rem,1.8333rem + .5556vw,2.5rem);--space-2xs-xs:clamp(.25rem,.125rem + .4167vw,.625rem);--space-xs-s:clamp(.5rem,.25rem + .8333vw,1.25rem);--space-s-m:clamp(1rem,.7083rem + .9722vw,1.875rem);--space-m-l:clamp(1.5rem,1.1667rem + 1.1111vw,2.5rem);--space-s-l:clamp(1rem,.5rem + 1.6667vw,2.5rem);--shadow-s:0 .1em .25em oklch(var(--color-neutral-l-900)var(--color-neutral-c)var(--color-neutral-h)/.1);--shadow-m:0 2px 8px oklch(var(--color-neutral-l-900)var(--color-neutral-c)var(--color-neutral-h)/.08),0 8px 16px oklch(var(--color-neutral-l-900)var(--color-neutral-c)var(--color-neutral-h)/.12),0 16px 24px oklch(var(--color-neutral-l-900)var(--color-neutral-c)var(--color-neutral-h)/.08);--shadow-l:0 4px 16px oklch(var(--color-neutral-l-900)var(--color-neutral-c)var(--color-neutral-h)/.08),0 12px 32px oklch(var(--color-neutral-l-900)var(--color-neutral-c)var(--color-neutral-h)/.12),0 24px 48px oklch(var(--color-neutral-l-900)var(--color-neutral-c)var(--color-neutral-h)/.16),0 48px 80px oklch(var(--color-neutral-l-900)var(--color-neutral-c)var(--color-neutral-h)/.08);--shadow-light-edge:inset 1px 1px 1px oklch(var(--color-neutral-l-0)var(--color-neutral-c)var(--color-neutral-h)/.3);--shadow-dark-edge:inset 0 -1px 1px oklch(var(--color-neutral-l-900)var(--color-neutral-c)var(--color-neutral-h)/.3);--backdrop-blur:blur(6px);--backdrop-glass:var(--backdrop-blur)saturate(1.4)brightness(1.2);--background-glass:linear-gradient(135deg,transparent,var(--color-base-background-o-95));--background-glass-frosted:linear-gradient(0deg,var(--color-base-background-o-75)0%,var(--color-base-background-o-95)50%);--opacity-subtle:.5;--opacity-medium:.7;--opacity-strong:.9;--outline-color:var(--color-accent);--outline-offset:.25rem;--transition-duration:.2s;--transition-duration-jump:.4s;--transition-easing-jump:cubic-bezier(.44,1.2,.64,1);--transition-easing:cubic-bezier(0,0,.2,1);--z-loop-marker:9998;--z-loop-panel:9999;--z-loop-dialog:10000;--author-avatar-color:var(--color-neutral-600);--author-avatar-background-color:var(--color-neutral-100);--author-avatar-size:2.5rem;--author-avatar-border-radius:var(--border-radius-rounded);--author-avatar-font-size:var(--font-size-6);--button-background:transparent;--button-color:var(--color-neutral-600);--button-border-radius:var(--border-radius);--button-padding:0 var(--space-xs);--button-gap:var(--space-2xs);--button-font-size:var(--font-size-7);--button-font-weight:var(--font-weight-medium);--button-height:2.25rem;--button-transition:var(--transition-duration)var(--transition-easing);--button-outline-color:var(--outline-color);--button-outline-offset:var(--outline-offset);--button-hover-color:var(--color-neutral-900);--button-hover-background:var(--color-neutral-200);--button-header-background:transparent;--button-header-height:3rem;--button-header-padding:0 var(--space-s);--button-header-hover-background:var(--color-base-background-o-95);--button-header-blend-mode:multiply;--button-panel-background:transparent;--button-panel-padding:0 calc(var(--space-s)*.4);--button-solid-background:var(--color-neutral-100);--button-solid-hover-color:var(--color-neutral-900);--button-solid-hover-background:var(--color-neutral-200);--button-small-height:1.5rem;--button-small-font-size:var(--font-size-7);--button-icon-background:var(--color-neutral-0);--button-icon-color:var(--color-neutral-500);--button-icon-height:3rem;--button-icon-shadow:var(--shadow-s);--button-icon-border-radius:var(--border-radius-rounded);--button-icon-font-size:var(--font-size-6);--button-icon-hover-background:var(--color-neutral-200);--button-icon-hover-color:var(--color-neutral-900);--button-marker-background:var(--color-accent);--button-marker-color:var(--color-accent-dark);--button-marker-font-weight:var(--font-weight-bold);--button-marker-border-radius:var(--border-radius-rounded);--button-marker-highlighted-background:var(--color-accent);--button-marker-highlighted-color:var(--color-accent-dark);--button-filter-background:transparent;--button-filter-color:var(--color-neutral-500);--button-filter-height:1.75rem;--button-filter-font-size:var(--font-size-8);--button-filter-padding:0 var(--space-xs);--button-filter-border-radius:calc(var(--border-radius) - 2px);--button-filter-hover-color:var(--color-neutral-700);--button-filter-hover-background:var(--color-neutral-200);--button-filter-active-background:var(--color-base-background);--button-filter-active-color:var(--color-base);--button-filter-active-font-weight:var(--font-weight-medium);--button-menu-item-background:transparent;--button-menu-item-color:var(--color-neutral-700);--button-menu-item-padding:var(--space-2xs)var(--space-xs);--button-menu-item-border-radius:calc(var(--border-radius) - 2px);--button-menu-item-font-size:var(--font-size-7);--button-menu-item-gap:var(--space-2xs);--button-menu-item-hover-background:var(--color-neutral-100);--button-menu-item-hover-color:var(--color-neutral-900);--button-menu-item-active-background:var(--color-accent-light);--button-menu-item-active-color:var(--color-accent-dark);--button-menu-item-active-font-weight:var(--font-weight-medium);--button-active-background:var(--color-accent);--button-active-color:var(--color-accent-dark);--button-disabled-opacity:var(--opacity-subtle);--button-disabled-hover-color:var(--color-neutral-700);--button-disabled-hover-background:var(--color-neutral-100);--comment-avatar-size:2.5rem;--comment-marker-background:var(--color-neutral-200);--comment-marker-color:var(--color-neutral-800);--comment-line-background:var(--color-neutral-100);--comment-line-width:.1rem;--comment-line-offset:calc(var(--space-s) + var(--comment-avatar-size)/2);--comment-header-font-size:var(--font-size-7);--comment-header-padding:var(--space-s);--comment-header-gap:var(--space-s);--comment-header-outline-color:var(--outline-color);--comment-header-outline-offset:-2px;--comment-header-border-radius:var(--border-radius);--comment-content-padding:var(--space-xs);--comment-content-background:var(--color-neutral-100);--comment-content-background-dark:var(--color-neutral-200);--comment-content-border-radius:var(--border-radius);--comment-author-gap:var(--space-xs);--comment-author-margin-bottom:var(--space-2xs);--comment-timestamp-font-size:var(--font-size-8);--comment-timestamp-color:var(--color-neutral-300);--comment-replies-padding:0 var(--space-s);--comment-replies-gap:var(--space-s);--comment-footer-padding:var(--space-s);--comment-footer-gap:var(--space-s);--comment-buttons-gap:var(--space-xs);--comment-dialog-position:absolute;--comment-dialog-max-width:300px;--comment-dialog-border-radius:var(--border-radius);--comment-dialog-shadow:var(--shadow-s);--comment-dialog-backdrop-background:transparent;--comment-dialog-textarea-font-size:var(--font-size-6);--comment-form-background:var(--color-base-background);--comment-form-color:var(--color-base);--comment-form-border:1px solid var(--color-neutral-200);--comment-form-border-radius:var(--border-radius);--comment-form-textarea-height:15ch;--comment-form-textarea-padding:var(--space-s);--comment-form-textarea-background:var(--color-base-background);--comment-form-textarea-font-family:var(--font-family);--comment-form-textarea-font-size:var(--font-size-7);--comment-form-footer-padding:var(--space-xs);--comment-form-footer-gap:var(--space-xs);--comment-form-hint-font-size:var(--font-size-8);--comment-form-hint-color:var(--color-neutral-300);--comment-form-hint-padding:0 var(--space-xs)var(--space-xs)var(--space-xs);--context-menu-container-bottom:var(--space-s);--context-menu-container-right:var(--space-s);--context-menu-container-z-index:10;--context-menu-trigger-size:2.5rem;--context-menu-trigger-border-radius:var(--border-radius-rounded);--context-menu-background:var(--color-base-background);--context-menu-border-radius:var(--border-radius);--context-menu-shadow:var(--shadow-s);--context-menu-padding:var(--space-xs);--context-menu-min-width:12rem;--context-menu-backdrop-background:transparent;--context-menu-section-gap:var(--space-2xs);--context-menu-title-font-size:var(--font-size-8);--context-menu-title-font-weight:var(--font-weight-medium);--context-menu-title-color:var(--color-neutral-500);--context-menu-title-margin-bottom:var(--space-2xs);--context-menu-title-letter-spacing:.05em;--context-menu-filter-gap:1px;--context-menu-filter-dot-size:.5em;--context-menu-filter-dot-border-radius:50%;--context-menu-filter-dot-margin-right:var(--space-2xs);--context-menu-filter-dot-open-background:var(--color-accent);--context-menu-filter-dot-resolved-background:var(--color-neutral-400);--header-position:fixed;--header-top:var(--space-xs);--header-transform:translateX(-50%);--header-color:var(--color-base);--header-border-radius:var(--border-radius-rounded);--header-z-index:9999;--header-bottom-position:var(--space-xs);--header-backdrop-filter:var(--backdrop-glass);--header-background:var(--background-glass);--header-count-size:2rem;--header-count-border-radius:var(--border-radius-rounded);--header-count-backdrop-filter:var(--backdrop-glass);--header-count-background:var(--background-glass);--marker-size:2rem;--marker-position:absolute;--marker-z-index:var(--z-loop-marker);--marker-transform:translate(-50%,-50%);--marker-border-radius:var(--border-radius-rounded);--panel-width:380px;--panel-mobile-width:85svw;--panel-position:fixed;--panel-right:var(--space-xs);--panel-top:var(--space-xs);--panel-height:calc(100svh - var(--space-xs)*2);--panel-transform-closed:translateX(calc(100% + var(--space-xs)));--panel-transform-open:translateX(0);--panel-color:var(--color-base);--panel-border-radius:var(--border-radius);--panel-border-top-left-radius:0;--panel-transition:var(--transition-duration-jump)var(--transition-easing-jump);--panel-z-index:var(--z-loop-panel);--panel-shadow:var(--shadow-m);--panel-header-transform-closed:translate(-95%);--panel-header-transform-open:translate(calc(-100% + 1px));--panel-header-transform-hover:translate(calc(-100% + 1px));--panel-header-border-radius:var(--border-radius-rounded);--panel-header-gap:var(--space-xs);--panel-header-backdrop-filter:var(--backdrop-glass);--panel-header-background:var(--background-glass);--panel-threads-background:var(--color-base-background-o-95);--panel-threads-backdrop:var(--backdrop-blur);--panel-threads-border-radius:var(--border-radius);--panel-threads-border-top-left-radius:0;--panel-threads-padding:0 0 var(--space-s)0;--panel-threads-item-margin:var(--space-s);--panel-threads-scrollbar-width:thin;--panel-no-threads-padding:var(--space-s)var(--space-l);--panel-no-threads-font-size:var(--font-size-6);--panel-no-threads-color:var(--color-neutral-300);--reply-gap:var(--space-s);--reply-content-padding:var(--space-xs);--reply-content-background:var(--color-neutral-100);--reply-content-background-dark:var(--color-neutral-200);--reply-content-border-radius:var(--border-radius);--reply-header-gap:var(--space-xs);--reply-header-margin-bottom:var(--space-2xs);--reply-timestamp-font-size:var(--font-size-8);--reply-timestamp-color:var(--color-neutral-300);--welcome-dialog-background:var(--background-glass-frosted);--welcome-dialog-backdrop-filter:var(--backdrop-glass);--welcome-dialog-border:0px;--welcome-dialog-border-radius:var(--border-radius);--welcome-dialog-shadow:var(--shadow-l),var(--shadow-light-edge),var(--shadow-dark-edge);--welcome-dialog-max-width:500px;--welcome-dialog-backdrop-background:var(--color-base-background-o-10);--welcome-dialog-backdrop-backdrop-filter:none;--welcome-dialog-form-padding:var(--space-l);--welcome-dialog-title-margin:0 0 var(--space-s)0;--welcome-dialog-title-font-size:var(--font-size-4);--welcome-dialog-title-color:var(--color-base);--welcome-dialog-title-font-weight:var(--font-weight-bold);--welcome-dialog-text-margin:0 0 var(--space-m)0;--welcome-dialog-text-font-size:var(--font-size-6);--welcome-dialog-text-color:var(--color-neutral-600);--welcome-dialog-text-line-height:var(--line-height);--welcome-dialog-name-section-margin:var(--space-l);--welcome-dialog-input-border:1px solid var(--color-neutral-300);--welcome-dialog-input-border-radius:var(--border-radius-s);--welcome-dialog-input-padding:var(--space-xs);--welcome-dialog-input-font-family:var(--font-family);--welcome-dialog-input-font-size:var(--font-size-6);--welcome-dialog-input-color:var(--color-base);--welcome-dialog-input-background:var(--color-base-background);--welcome-dialog-input-outline-color:var(--outline-color);--welcome-dialog-input-outline-offset:var(--outline-offset);--welcome-dialog-footer-gap:var(--space-xs);--icon-size:1em}kirby-loop[data-theme=dark]{--color-neutral-l-0:0;--color-neutral-l-100:.1;--color-neutral-l-200:.2;--color-neutral-l-300:.3;--color-neutral-l-400:.4;--color-neutral-l-500:.5;--color-neutral-l-600:.6;--color-neutral-l-700:.7;--color-neutral-l-800:.9;--color-neutral-l-900:.95;--color-neutral-l-1000:1}kirby-loop{font-family:var(--font-family);line-height:var(--line-height);font-weight:var(--font-weight-normal);font-size:var(--font-size-7);box-sizing:border-box}html.loop-overlay-open a{pointer-events:none}')),document.head.appendChild(r)}}catch(o){console.error("vite-plugin-css-injected-by-js",o)}})();
var io = Object.defineProperty;
var Dr = (e) => {
  throw TypeError(e);
};
var lo = (e, t, r) => t in e ? io(e, t, { enumerable: !0, configurable: !0, writable: !0, value: r }) : e[t] = r;
var oe = (e, t, r) => lo(e, typeof t != "symbol" ? t + "" : t, r), jr = (e, t, r) => t.has(e) || Dr("Cannot " + r);
var de = (e, t, r) => (jr(e, t, "read from private field"), r ? r.call(e) : t.get(e)), ar = (e, t, r) => t.has(e) ? Dr("Cannot add the same private member more than once") : t instanceof WeakSet ? t.add(e) : t.set(e, r), sr = (e, t, r, n) => (jr(e, t, "write to private field"), n ? n.call(e, r) : t.set(e, r), r);
const uo = "5";
var nn;
typeof window < "u" && ((nn = window.__svelte ?? (window.__svelte = {})).v ?? (nn.v = /* @__PURE__ */ new Set())).add(uo);
const pr = 1, br = 2, on = 4, co = 8, fo = 16, vo = 1, ho = 2, mo = 4, go = 8, po = 16, bo = 1, wo = 2, wr = "[", _r = "[!", yr = "]", wt = {}, fe = Symbol(), _o = "http://www.w3.org/1999/xhtml", Hr = !1, Se = 2, an = 4, Zt = 8, kr = 16, Le = 32, rt = 64, Tt = 128, Ce = 256, Ot = 512, be = 1024, De = 2048, dt = 4096, Te = 8192, Jt = 16384, yo = 32768, xr = 65536, ko = 1 << 17, xo = 1 << 19, sn = 1 << 20, Ke = Symbol("$state"), ln = Symbol("legacy props"), $o = Symbol("");
var $r = Array.isArray, Co = Array.prototype.indexOf, Cr = Array.from, At = Object.keys, Lt = Object.defineProperty, Ge = Object.getOwnPropertyDescriptor, un = Object.getOwnPropertyDescriptors, zo = Object.prototype, So = Array.prototype, zr = Object.getPrototypeOf;
function Eo(e) {
  return e();
}
function Dt(e) {
  for (var t = 0; t < e.length; t++)
    e[t]();
}
const qo = typeof requestIdleCallback > "u" ? (e) => setTimeout(e, 1) : requestIdleCallback;
let yt = [], kt = [];
function cn() {
  var e = yt;
  yt = [], Dt(e);
}
function dn() {
  var e = kt;
  kt = [], Dt(e);
}
function Kt(e) {
  yt.length === 0 && queueMicrotask(cn), yt.push(e);
}
function Io(e) {
  kt.length === 0 && qo(dn), kt.push(e);
}
function Fr() {
  yt.length > 0 && cn(), kt.length > 0 && dn();
}
function fn(e) {
  return e === this.v;
}
function Mo(e, t) {
  return e != e ? t == t : e !== t || e !== null && typeof e == "object" || typeof e == "function";
}
function Sr(e) {
  return !Mo(e, this.v);
}
function Po(e) {
  throw new Error("https://svelte.dev/e/effect_in_teardown");
}
function No() {
  throw new Error("https://svelte.dev/e/effect_in_unowned_derived");
}
function Ro(e) {
  throw new Error("https://svelte.dev/e/effect_orphan");
}
function To() {
  throw new Error("https://svelte.dev/e/effect_update_depth_exceeded");
}
function Oo() {
  throw new Error("https://svelte.dev/e/hydration_failed");
}
function Ao(e) {
  throw new Error("https://svelte.dev/e/props_invalid_value");
}
function Lo() {
  throw new Error("https://svelte.dev/e/state_descriptors_fixed");
}
function Do() {
  throw new Error("https://svelte.dev/e/state_prototype_fixed");
}
function jo() {
  throw new Error("https://svelte.dev/e/state_unsafe_local_read");
}
function Ho() {
  throw new Error("https://svelte.dev/e/state_unsafe_mutation");
}
let ft = !1, Fo = !1;
function Vo() {
  ft = !0;
}
const xt = /* @__PURE__ */ new Map();
function ve(e, t) {
  var r = {
    f: 0,
    // TODO ideally we could skip this altogether, but it causes type errors
    v: e,
    reactions: null,
    equals: fn,
    rv: 0,
    wv: 0
  };
  return r;
}
function ue(e) {
  return /* @__PURE__ */ vn(ve(e));
}
// @__NO_SIDE_EFFECTS__
function Gt(e, t = !1) {
  var n;
  const r = ve(e);
  return t || (r.equals = Sr), ft && U !== null && U.l !== null && ((n = U.l).s ?? (n.s = [])).push(r), r;
}
function Vr(e, t = !1) {
  return /* @__PURE__ */ vn(/* @__PURE__ */ Gt(e, t));
}
// @__NO_SIDE_EFFECTS__
function vn(e) {
  return Y !== null && !ze && (Y.f & Se) !== 0 && (Ie === null ? Wo([e]) : Ie.push(e)), e;
}
function Mt(e, t) {
  return T(
    e,
    Ue(() => h(e))
  ), t;
}
function T(e, t) {
  return Y !== null && !ze && Et() && (Y.f & (Se | kr)) !== 0 && // If the source was created locally within the current derived, then
  // we allow the mutation.
  (Ie === null || !Ie.includes(e)) && Ho(), dr(e, t);
}
function dr(e, t) {
  if (!e.equals(t)) {
    var r = e.v;
    zt ? xt.set(e, t) : xt.set(e, r), e.v = t, e.wv = kn(), hn(e, De), Et() && B !== null && (B.f & be) !== 0 && (B.f & (Le | rt)) === 0 && (ke === null ? Xo([e]) : ke.push(e));
  }
  return t;
}
function hn(e, t) {
  var r = e.reactions;
  if (r !== null)
    for (var n = Et(), o = r.length, a = 0; a < o; a++) {
      var s = r[a], i = s.f;
      (i & De) === 0 && (!n && s === B || (Pe(s, t), (i & (be | Ce)) !== 0 && ((i & Se) !== 0 ? hn(
        /** @type {Derived} */
        s,
        dt
      ) : er(
        /** @type {Effect} */
        s
      ))));
    }
}
// @__NO_SIDE_EFFECTS__
function ee(e) {
  var t = Se | De, r = Y !== null && (Y.f & Se) !== 0 ? (
    /** @type {Derived} */
    Y
  ) : null;
  return B === null || r !== null && (r.f & Ce) !== 0 ? t |= Ce : B.f |= sn, {
    ctx: U,
    deps: null,
    effects: null,
    equals: fn,
    f: t,
    fn: e,
    reactions: null,
    rv: 0,
    v: (
      /** @type {V} */
      null
    ),
    wv: 0,
    parent: r ?? B
  };
}
// @__NO_SIDE_EFFECTS__
function me(e) {
  const t = /* @__PURE__ */ ee(e);
  return t.equals = Sr, t;
}
function mn(e) {
  var t = e.effects;
  if (t !== null) {
    e.effects = null;
    for (var r = 0; r < t.length; r += 1)
      Ae(
        /** @type {Effect} */
        t[r]
      );
  }
}
function Bo(e) {
  for (var t = e.parent; t !== null; ) {
    if ((t.f & Se) === 0)
      return (
        /** @type {Effect} */
        t
      );
    t = t.parent;
  }
  return null;
}
function Yo(e) {
  var t, r = B;
  Xe(Bo(e));
  try {
    mn(e), t = $n(e);
  } finally {
    Xe(r);
  }
  return t;
}
function gn(e) {
  var t = Yo(e), r = (Be || (e.f & Ce) !== 0) && e.deps !== null ? dt : be;
  Pe(e, r), e.equals(t) || (e.v = t, e.wv = kn());
}
function Er(e) {
  console.warn("https://svelte.dev/e/hydration_mismatch");
}
let A = !1;
function Re(e) {
  A = e;
}
let H;
function pe(e) {
  if (e === null)
    throw Er(), wt;
  return H = e;
}
function Ct() {
  return pe(
    /** @type {TemplateNode} */
    /* @__PURE__ */ je(H)
  );
}
function x(e) {
  if (A) {
    if (/* @__PURE__ */ je(H) !== null)
      throw Er(), wt;
    H = e;
  }
}
function ge(e = 1) {
  if (A) {
    for (var t = e, r = H; t--; )
      r = /** @type {TemplateNode} */
      /* @__PURE__ */ je(r);
    H = r;
  }
}
function fr() {
  for (var e = 0, t = H; ; ) {
    if (t.nodeType === 8) {
      var r = (
        /** @type {Comment} */
        t.data
      );
      if (r === yr) {
        if (e === 0) return t;
        e -= 1;
      } else (r === wr || r === _r) && (e += 1);
    }
    var n = (
      /** @type {TemplateNode} */
      /* @__PURE__ */ je(t)
    );
    t.remove(), t = n;
  }
}
function J(e, t = null, r) {
  if (typeof e != "object" || e === null || Ke in e)
    return e;
  const n = zr(e);
  if (n !== zo && n !== So)
    return e;
  var o = /* @__PURE__ */ new Map(), a = $r(e), s = ve(0);
  a && o.set("length", ve(
    /** @type {any[]} */
    e.length
  ));
  var i;
  return new Proxy(
    /** @type {any} */
    e,
    {
      defineProperty(u, c, v) {
        (!("value" in v) || v.configurable === !1 || v.enumerable === !1 || v.writable === !1) && Lo();
        var m = o.get(c);
        return m === void 0 ? (m = ve(v.value), o.set(c, m)) : T(m, J(v.value, i)), !0;
      },
      deleteProperty(u, c) {
        var v = o.get(c);
        if (v === void 0)
          c in u && o.set(c, ve(fe));
        else {
          if (a && typeof c == "string") {
            var m = (
              /** @type {Source<number>} */
              o.get("length")
            ), l = Number(c);
            Number.isInteger(l) && l < m.v && T(m, l);
          }
          T(v, fe), Br(s);
        }
        return !0;
      },
      get(u, c, v) {
        var d;
        if (c === Ke)
          return e;
        var m = o.get(c), l = c in u;
        if (m === void 0 && (!l || (d = Ge(u, c)) != null && d.writable) && (m = ve(J(l ? u[c] : fe, i)), o.set(c, m)), m !== void 0) {
          var f = h(m);
          return f === fe ? void 0 : f;
        }
        return Reflect.get(u, c, v);
      },
      getOwnPropertyDescriptor(u, c) {
        var v = Reflect.getOwnPropertyDescriptor(u, c);
        if (v && "value" in v) {
          var m = o.get(c);
          m && (v.value = h(m));
        } else if (v === void 0) {
          var l = o.get(c), f = l == null ? void 0 : l.v;
          if (l !== void 0 && f !== fe)
            return {
              enumerable: !0,
              configurable: !0,
              value: f,
              writable: !0
            };
        }
        return v;
      },
      has(u, c) {
        var f;
        if (c === Ke)
          return !0;
        var v = o.get(c), m = v !== void 0 && v.v !== fe || Reflect.has(u, c);
        if (v !== void 0 || B !== null && (!m || (f = Ge(u, c)) != null && f.writable)) {
          v === void 0 && (v = ve(m ? J(u[c], i) : fe), o.set(c, v));
          var l = h(v);
          if (l === fe)
            return !1;
        }
        return m;
      },
      set(u, c, v, m) {
        var k;
        var l = o.get(c), f = c in u;
        if (a && c === "length")
          for (var d = v; d < /** @type {Source<number>} */
          l.v; d += 1) {
            var p = o.get(d + "");
            p !== void 0 ? T(p, fe) : d in u && (p = ve(fe), o.set(d + "", p));
          }
        l === void 0 ? (!f || (k = Ge(u, c)) != null && k.writable) && (l = ve(void 0), T(l, J(v, i)), o.set(c, l)) : (f = l.v !== fe, T(l, J(v, i)));
        var w = Reflect.getOwnPropertyDescriptor(u, c);
        if (w != null && w.set && w.set.call(m, v), !f) {
          if (a && typeof c == "string") {
            var I = (
              /** @type {Source<number>} */
              o.get("length")
            ), _ = Number(c);
            Number.isInteger(_) && _ >= I.v && T(I, _ + 1);
          }
          Br(s);
        }
        return !0;
      },
      ownKeys(u) {
        h(s);
        var c = Reflect.ownKeys(u).filter((l) => {
          var f = o.get(l);
          return f === void 0 || f.v !== fe;
        });
        for (var [v, m] of o)
          m.v !== fe && !(v in u) && c.push(v);
        return c;
      },
      setPrototypeOf() {
        Do();
      }
    }
  );
}
function Br(e, t = 1) {
  T(e, e.v + t);
}
var Yr, pn, bn, wn, _n;
function vr() {
  if (Yr === void 0) {
    Yr = window, pn = document, bn = /Firefox/.test(navigator.userAgent);
    var e = Element.prototype, t = Node.prototype;
    wn = Ge(t, "firstChild").get, _n = Ge(t, "nextSibling").get, e.__click = void 0, e.__className = void 0, e.__attributes = null, e.__style = void 0, e.__e = void 0, Text.prototype.__t = void 0;
  }
}
function Ye(e = "") {
  return document.createTextNode(e);
}
// @__NO_SIDE_EFFECTS__
function Ee(e) {
  return wn.call(e);
}
// @__NO_SIDE_EFFECTS__
function je(e) {
  return _n.call(e);
}
function C(e, t) {
  if (!A)
    return /* @__PURE__ */ Ee(e);
  var r = (
    /** @type {TemplateNode} */
    /* @__PURE__ */ Ee(H)
  );
  if (r === null)
    r = H.appendChild(Ye());
  else if (t && r.nodeType !== 3) {
    var n = Ye();
    return r == null || r.before(n), pe(n), n;
  }
  return pe(r), r;
}
function Oe(e, t) {
  if (!A) {
    var r = (
      /** @type {DocumentFragment} */
      /* @__PURE__ */ Ee(
        /** @type {Node} */
        e
      )
    );
    return r instanceof Comment && r.data === "" ? /* @__PURE__ */ je(r) : r;
  }
  return H;
}
function V(e, t = 1, r = !1) {
  let n = A ? H : e;
  for (var o; t--; )
    o = n, n = /** @type {TemplateNode} */
    /* @__PURE__ */ je(n);
  if (!A)
    return n;
  var a = n == null ? void 0 : n.nodeType;
  if (r && a !== 3) {
    var s = Ye();
    return n === null ? o == null || o.after(s) : n.before(s), pe(s), s;
  }
  return pe(n), /** @type {TemplateNode} */
  n;
}
function qr(e) {
  e.textContent = "";
}
let Nt = !1, jt = !1, Ht = null, Qe = !1, zt = !1;
function Wr(e) {
  zt = e;
}
let _t = [];
let Y = null, ze = !1;
function We(e) {
  Y = e;
}
let B = null;
function Xe(e) {
  B = e;
}
let Ie = null;
function Wo(e) {
  Ie = e;
}
let ie = null, he = 0, ke = null;
function Xo(e) {
  ke = e;
}
let yn = 1, Ft = 0, Be = !1;
function kn() {
  return ++yn;
}
function St(e) {
  var m;
  var t = e.f;
  if ((t & De) !== 0)
    return !0;
  if ((t & dt) !== 0) {
    var r = e.deps, n = (t & Ce) !== 0;
    if (r !== null) {
      var o, a, s = (t & Ot) !== 0, i = n && B !== null && !Be, u = r.length;
      if (s || i) {
        var c = (
          /** @type {Derived} */
          e
        ), v = c.parent;
        for (o = 0; o < u; o++)
          a = r[o], (s || !((m = a == null ? void 0 : a.reactions) != null && m.includes(c))) && (a.reactions ?? (a.reactions = [])).push(c);
        s && (c.f ^= Ot), i && v !== null && (v.f & Ce) === 0 && (c.f ^= Ce);
      }
      for (o = 0; o < u; o++)
        if (a = r[o], St(
          /** @type {Derived} */
          a
        ) && gn(
          /** @type {Derived} */
          a
        ), a.wv > e.wv)
          return !0;
    }
    (!n || B !== null && !Be) && Pe(e, be);
  }
  return !1;
}
function Uo(e, t) {
  for (var r = t; r !== null; ) {
    if ((r.f & Tt) !== 0)
      try {
        r.fn(e);
        return;
      } catch {
        r.f ^= Tt;
      }
    r = r.parent;
  }
  throw Nt = !1, e;
}
function Zo(e) {
  return (e.f & Jt) === 0 && (e.parent === null || (e.parent.f & Tt) === 0);
}
function Qt(e, t, r, n) {
  if (Nt) {
    if (r === null && (Nt = !1), Zo(t))
      throw e;
    return;
  }
  r !== null && (Nt = !0);
  {
    Uo(e, t);
    return;
  }
}
function xn(e, t, r = !0) {
  var n = e.reactions;
  if (n !== null)
    for (var o = 0; o < n.length; o++) {
      var a = n[o];
      (a.f & Se) !== 0 ? xn(
        /** @type {Derived} */
        a,
        t,
        !1
      ) : t === a && (r ? Pe(a, De) : (a.f & be) !== 0 && Pe(a, dt), er(
        /** @type {Effect} */
        a
      ));
    }
}
function $n(e) {
  var f;
  var t = ie, r = he, n = ke, o = Y, a = Be, s = Ie, i = U, u = ze, c = e.f;
  ie = /** @type {null | Value[]} */
  null, he = 0, ke = null, Be = (c & Ce) !== 0 && (ze || !Qe || Y === null), Y = (c & (Le | rt)) === 0 ? e : null, Ie = null, Xr(e.ctx), ze = !1, Ft++;
  try {
    var v = (
      /** @type {Function} */
      (0, e.fn)()
    ), m = e.deps;
    if (ie !== null) {
      var l;
      if (Vt(e, he), m !== null && he > 0)
        for (m.length = he + ie.length, l = 0; l < ie.length; l++)
          m[he + l] = ie[l];
      else
        e.deps = m = ie;
      if (!Be)
        for (l = he; l < m.length; l++)
          ((f = m[l]).reactions ?? (f.reactions = [])).push(e);
    } else m !== null && he < m.length && (Vt(e, he), m.length = he);
    if (Et() && ke !== null && !ze && m !== null && (e.f & (Se | dt | De)) === 0)
      for (l = 0; l < /** @type {Source[]} */
      ke.length; l++)
        xn(
          ke[l],
          /** @type {Effect} */
          e
        );
    return o !== null && (Ft++, ke !== null && (n === null ? n = ke : n.push(.../** @type {Source[]} */
    ke))), v;
  } finally {
    ie = t, he = r, ke = n, Y = o, Be = a, Ie = s, Xr(i), ze = u;
  }
}
function Jo(e, t) {
  let r = t.reactions;
  if (r !== null) {
    var n = Co.call(r, e);
    if (n !== -1) {
      var o = r.length - 1;
      o === 0 ? r = t.reactions = null : (r[n] = r[o], r.pop());
    }
  }
  r === null && (t.f & Se) !== 0 && // Destroying a child effect while updating a parent effect can cause a dependency to appear
  // to be unused, when in fact it is used by the currently-updating parent. Checking `new_deps`
  // allows us to skip the expensive work of disconnecting and immediately reconnecting it
  (ie === null || !ie.includes(t)) && (Pe(t, dt), (t.f & (Ce | Ot)) === 0 && (t.f ^= Ot), mn(
    /** @type {Derived} **/
    t
  ), Vt(
    /** @type {Derived} **/
    t,
    0
  ));
}
function Vt(e, t) {
  var r = e.deps;
  if (r !== null)
    for (var n = t; n < r.length; n++)
      Jo(e, r[n]);
}
function Ir(e) {
  var t = e.f;
  if ((t & Jt) === 0) {
    Pe(e, be);
    var r = B, n = U, o = Qe;
    B = e, Qe = !0;
    try {
      (t & kr) !== 0 ? sa(e) : En(e), Sn(e);
      var a = $n(e);
      e.teardown = typeof a == "function" ? a : null, e.wv = yn;
      var s = e.deps, i;
      Hr && Fo && e.f & De;
    } catch (u) {
      Qt(u, e, r, n || e.ctx);
    } finally {
      Qe = o, B = r;
    }
  }
}
function Ko() {
  try {
    To();
  } catch (e) {
    if (Ht !== null)
      Qt(e, Ht, null);
    else
      throw e;
  }
}
function Cn() {
  var e = Qe;
  try {
    var t = 0;
    for (Qe = !0; _t.length > 0; ) {
      t++ > 1e3 && Ko();
      var r = _t, n = r.length;
      _t = [];
      for (var o = 0; o < n; o++) {
        var a = Qo(r[o]);
        Go(a);
      }
    }
  } finally {
    jt = !1, Qe = e, Ht = null, xt.clear();
  }
}
function Go(e) {
  var t = e.length;
  if (t !== 0)
    for (var r = 0; r < t; r++) {
      var n = e[r];
      if ((n.f & (Jt | Te)) === 0)
        try {
          St(n) && (Ir(n), n.deps === null && n.first === null && n.nodes_start === null && (n.teardown === null ? qn(n) : n.fn = null));
        } catch (o) {
          Qt(o, n, null, n.ctx);
        }
    }
}
function er(e) {
  jt || (jt = !0, queueMicrotask(Cn));
  for (var t = Ht = e; t.parent !== null; ) {
    t = t.parent;
    var r = t.f;
    if ((r & (rt | Le)) !== 0) {
      if ((r & be) === 0) return;
      t.f ^= be;
    }
  }
  _t.push(t);
}
function Qo(e) {
  for (var t = [], r = e; r !== null; ) {
    var n = r.f, o = (n & (Le | rt)) !== 0, a = o && (n & be) !== 0;
    if (!a && (n & Te) === 0) {
      if ((n & an) !== 0)
        t.push(r);
      else if (o)
        r.f ^= be;
      else {
        var s = Y;
        try {
          Y = r, St(r) && Ir(r);
        } catch (c) {
          Qt(c, r, null, r.ctx);
        } finally {
          Y = s;
        }
      }
      var i = r.first;
      if (i !== null) {
        r = i;
        continue;
      }
    }
    var u = r.parent;
    for (r = r.next; r === null && u !== null; )
      r = u.next, u = u.parent;
  }
  return t;
}
function E(e) {
  var t;
  for (Fr(); _t.length > 0; )
    jt = !0, Cn(), Fr();
  return (
    /** @type {T} */
    t
  );
}
function h(e) {
  var t = e.f, r = (t & Se) !== 0;
  if (Y !== null && !ze) {
    Ie !== null && Ie.includes(e) && jo();
    var n = Y.deps;
    e.rv < Ft && (e.rv = Ft, ie === null && n !== null && n[he] === e ? he++ : ie === null ? ie = [e] : (!Be || !ie.includes(e)) && ie.push(e));
  } else if (r && /** @type {Derived} */
  e.deps === null && /** @type {Derived} */
  e.effects === null) {
    var o = (
      /** @type {Derived} */
      e
    ), a = o.parent;
    a !== null && (a.f & Ce) === 0 && (o.f ^= Ce);
  }
  return r && (o = /** @type {Derived} */
  e, St(o) && gn(o)), zt && xt.has(e) ? xt.get(e) : e.v;
}
function Ue(e) {
  var t = ze;
  try {
    return ze = !0, e();
  } finally {
    ze = t;
  }
}
const ea = -7169;
function Pe(e, t) {
  e.f = e.f & ea | t;
}
function ta(e) {
  if (!(typeof e != "object" || !e || e instanceof EventTarget)) {
    if (Ke in e)
      hr(e);
    else if (!Array.isArray(e))
      for (let t in e) {
        const r = e[t];
        typeof r == "object" && r && Ke in r && hr(r);
      }
  }
}
function hr(e, t = /* @__PURE__ */ new Set()) {
  if (typeof e == "object" && e !== null && // We don't want to traverse DOM elements
  !(e instanceof EventTarget) && !t.has(e)) {
    t.add(e), e instanceof Date && e.getTime();
    for (let n in e)
      try {
        hr(e[n], t);
      } catch {
      }
    const r = zr(e);
    if (r !== Object.prototype && r !== Array.prototype && r !== Map.prototype && r !== Set.prototype && r !== Date.prototype) {
      const n = un(r);
      for (let o in n) {
        const a = n[o].get;
        if (a)
          try {
            a.call(e);
          } catch {
          }
      }
    }
  }
}
function zn(e) {
  B === null && Y === null && Ro(), Y !== null && (Y.f & Ce) !== 0 && B === null && No(), zt && Po();
}
function ra(e, t) {
  var r = t.last;
  r === null ? t.last = t.first = e : (r.next = e, e.prev = r, t.last = e);
}
function nt(e, t, r, n = !0) {
  var o = B, a = {
    ctx: U,
    deps: null,
    nodes_start: null,
    nodes_end: null,
    f: e | De,
    first: null,
    fn: t,
    last: null,
    next: null,
    parent: o,
    prev: null,
    teardown: null,
    transitions: null,
    wv: 0
  };
  if (r)
    try {
      Ir(a), a.f |= yo;
    } catch (u) {
      throw Ae(a), u;
    }
  else t !== null && er(a);
  var s = r && a.deps === null && a.first === null && a.nodes_start === null && a.teardown === null && (a.f & (sn | Tt)) === 0;
  if (!s && n && (o !== null && ra(a, o), Y !== null && (Y.f & Se) !== 0)) {
    var i = (
      /** @type {Derived} */
      Y
    );
    (i.effects ?? (i.effects = [])).push(a);
  }
  return a;
}
function tr(e) {
  const t = nt(Zt, null, !1);
  return Pe(t, be), t.teardown = e, t;
}
function et(e) {
  zn();
  var t = B !== null && (B.f & Le) !== 0 && U !== null && !U.m;
  if (t) {
    var r = (
      /** @type {ComponentContext} */
      U
    );
    (r.e ?? (r.e = [])).push({
      fn: e,
      effect: B,
      reaction: Y
    });
  } else {
    var n = Mr(e);
    return n;
  }
}
function na(e) {
  return zn(), vt(e);
}
function oa(e) {
  const t = nt(rt, e, !0);
  return () => {
    Ae(t);
  };
}
function aa(e) {
  const t = nt(rt, e, !0);
  return (r = {}) => new Promise((n) => {
    r.outro ? Bt(t, () => {
      Ae(t), n(void 0);
    }) : (Ae(t), n(void 0));
  });
}
function Mr(e) {
  return nt(an, e, !1);
}
function vt(e) {
  return nt(Zt, e, !0);
}
function j(e, t = [], r = ee) {
  const n = t.map(r);
  return Pr(() => e(...n.map(h)));
}
function Pr(e, t = 0) {
  return nt(Zt | kr | t, e, !0);
}
function $t(e, t = !0) {
  return nt(Zt | Le, e, !0, t);
}
function Sn(e) {
  var t = e.teardown;
  if (t !== null) {
    const r = zt, n = Y;
    Wr(!0), We(null);
    try {
      t.call(null);
    } finally {
      Wr(r), We(n);
    }
  }
}
function En(e, t = !1) {
  var r = e.first;
  for (e.first = e.last = null; r !== null; ) {
    var n = r.next;
    (r.f & rt) !== 0 ? r.parent = null : Ae(r, t), r = n;
  }
}
function sa(e) {
  for (var t = e.first; t !== null; ) {
    var r = t.next;
    (t.f & Le) === 0 && Ae(t), t = r;
  }
}
function Ae(e, t = !0) {
  var r = !1;
  if ((t || (e.f & xo) !== 0) && e.nodes_start !== null) {
    for (var n = e.nodes_start, o = e.nodes_end; n !== null; ) {
      var a = n === o ? null : (
        /** @type {TemplateNode} */
        /* @__PURE__ */ je(n)
      );
      n.remove(), n = a;
    }
    r = !0;
  }
  En(e, t && !r), Vt(e, 0), Pe(e, Jt);
  var s = e.transitions;
  if (s !== null)
    for (const u of s)
      u.stop();
  Sn(e);
  var i = e.parent;
  i !== null && i.first !== null && qn(e), e.next = e.prev = e.teardown = e.ctx = e.deps = e.fn = e.nodes_start = e.nodes_end = null;
}
function qn(e) {
  var t = e.parent, r = e.prev, n = e.next;
  r !== null && (r.next = n), n !== null && (n.prev = r), t !== null && (t.first === e && (t.first = n), t.last === e && (t.last = r));
}
function Bt(e, t) {
  var r = [];
  Nr(e, r, !0), In(r, () => {
    Ae(e), t && t();
  });
}
function In(e, t) {
  var r = e.length;
  if (r > 0) {
    var n = () => --r || t();
    for (var o of e)
      o.out(n);
  } else
    t();
}
function Nr(e, t, r) {
  if ((e.f & Te) === 0) {
    if (e.f ^= Te, e.transitions !== null)
      for (const s of e.transitions)
        (s.is_global || r) && t.push(s);
    for (var n = e.first; n !== null; ) {
      var o = n.next, a = (n.f & xr) !== 0 || (n.f & Le) !== 0;
      Nr(n, t, a ? r : !1), n = o;
    }
  }
}
function Yt(e) {
  Mn(e, !0);
}
function Mn(e, t) {
  if ((e.f & Te) !== 0) {
    e.f ^= Te, (e.f & be) === 0 && (e.f ^= be), St(e) && (Pe(e, De), er(e));
    for (var r = e.first; r !== null; ) {
      var n = r.next, o = (r.f & xr) !== 0 || (r.f & Le) !== 0;
      Mn(r, o ? t : !1), r = n;
    }
    if (e.transitions !== null)
      for (const a of e.transitions)
        (a.is_global || t) && a.in();
  }
}
function Pn(e) {
  throw new Error("https://svelte.dev/e/lifecycle_outside_component");
}
let U = null;
function Xr(e) {
  U = e;
}
function we(e, t = !1, r) {
  var n = U = {
    p: U,
    c: null,
    d: !1,
    e: null,
    m: !1,
    s: e,
    x: null,
    l: null
  };
  ft && !t && (U.l = {
    s: null,
    u: null,
    r1: [],
    r2: ve(!1)
  }), tr(() => {
    n.d = !0;
  });
}
function _e(e) {
  const t = U;
  if (t !== null) {
    e !== void 0 && (t.x = e);
    const s = t.e;
    if (s !== null) {
      var r = B, n = Y;
      t.e = null;
      try {
        for (var o = 0; o < s.length; o++) {
          var a = s[o];
          Xe(a.effect), We(a.reaction), Mr(a.fn);
        }
      } finally {
        Xe(r), We(n);
      }
    }
    U = t.p, t.m = !0;
  }
  return e || /** @type {T} */
  {};
}
function Et() {
  return !ft || U !== null && U.l === null;
}
const ia = ["touchstart", "touchmove"];
function la(e) {
  return ia.includes(e);
}
function ua(e) {
  A && /* @__PURE__ */ Ee(e) !== null && qr(e);
}
let Ur = !1;
function Nn() {
  Ur || (Ur = !0, document.addEventListener(
    "reset",
    (e) => {
      Promise.resolve().then(() => {
        var t;
        if (!e.defaultPrevented)
          for (
            const r of
            /**@type {HTMLFormElement} */
            e.target.elements
          )
            (t = r.__on_r) == null || t.call(r);
      });
    },
    // In the capture phase to guarantee we get noticed of it (no possiblity of stopPropagation)
    { capture: !0 }
  ));
}
function Rn(e) {
  var t = Y, r = B;
  We(null), Xe(null);
  try {
    return e();
  } finally {
    We(t), Xe(r);
  }
}
function ca(e, t, r, n = r) {
  e.addEventListener(t, () => Rn(r));
  const o = e.__on_r;
  o ? e.__on_r = () => {
    o(), n(!0);
  } : e.__on_r = () => n(!0), Nn();
}
const Tn = /* @__PURE__ */ new Set(), mr = /* @__PURE__ */ new Set();
function da(e, t, r, n = {}) {
  function o(a) {
    if (n.capture || bt.call(t, a), !a.cancelBubble)
      return Rn(() => r == null ? void 0 : r.call(this, a));
  }
  return e.startsWith("pointer") || e.startsWith("touch") || e === "wheel" ? Kt(() => {
    t.addEventListener(e, o, n);
  }) : t.addEventListener(e, o, n), o;
}
function Ze(e, t, r, n, o) {
  var a = { capture: n, passive: o }, s = da(e, t, r, a);
  (t === document.body || t === window || t === document) && tr(() => {
    t.removeEventListener(e, s, a);
  });
}
function On(e) {
  for (var t = 0; t < e.length; t++)
    Tn.add(e[t]);
  for (var r of mr)
    r(e);
}
function bt(e) {
  var _;
  var t = this, r = (
    /** @type {Node} */
    t.ownerDocument
  ), n = e.type, o = ((_ = e.composedPath) == null ? void 0 : _.call(e)) || [], a = (
    /** @type {null | Element} */
    o[0] || e.target
  ), s = 0, i = e.__root;
  if (i) {
    var u = o.indexOf(i);
    if (u !== -1 && (t === document || t === /** @type {any} */
    window)) {
      e.__root = t;
      return;
    }
    var c = o.indexOf(t);
    if (c === -1)
      return;
    u <= c && (s = u);
  }
  if (a = /** @type {Element} */
  o[s] || e.target, a !== t) {
    Lt(e, "currentTarget", {
      configurable: !0,
      get() {
        return a || r;
      }
    });
    var v = Y, m = B;
    We(null), Xe(null);
    try {
      for (var l, f = []; a !== null; ) {
        var d = a.assignedSlot || a.parentNode || /** @type {any} */
        a.host || null;
        try {
          var p = a["__" + n];
          if (p != null && (!/** @type {any} */
          a.disabled || // DOM could've been updated already by the time this is reached, so we check this as well
          // -> the target could not have been disabled because it emits the event in the first place
          e.target === a))
            if ($r(p)) {
              var [w, ...I] = p;
              w.apply(a, [e, ...I]);
            } else
              p.call(a, e);
        } catch (k) {
          l ? f.push(k) : l = k;
        }
        if (e.cancelBubble || d === t || d === null)
          break;
        a = d;
      }
      if (l) {
        for (let k of f)
          queueMicrotask(() => {
            throw k;
          });
        throw l;
      }
    } finally {
      e.__root = t, delete e.currentTarget, We(v), Xe(m);
    }
  }
}
function An(e) {
  var t = document.createElement("template");
  return t.innerHTML = e, t.content;
}
function Me(e, t) {
  var r = (
    /** @type {Effect} */
    B
  );
  r.nodes_start === null && (r.nodes_start = e, r.nodes_end = t);
}
// @__NO_SIDE_EFFECTS__
function W(e, t) {
  var r = (t & bo) !== 0, n = (t & wo) !== 0, o, a = !e.startsWith("<!>");
  return () => {
    if (A)
      return Me(H, null), H;
    o === void 0 && (o = An(a ? e : "<!>" + e), r || (o = /** @type {Node} */
    /* @__PURE__ */ Ee(o)));
    var s = (
      /** @type {TemplateNode} */
      n || bn ? document.importNode(o, !0) : o.cloneNode(!0)
    );
    if (r) {
      var i = (
        /** @type {TemplateNode} */
        /* @__PURE__ */ Ee(s)
      ), u = (
        /** @type {TemplateNode} */
        s.lastChild
      );
      Me(i, u);
    } else
      Me(s, s);
    return s;
  };
}
// @__NO_SIDE_EFFECTS__
function qt(e, t, r = "svg") {
  var n = !e.startsWith("<!>"), o = `<${r}>${n ? e : "<!>" + e}</${r}>`, a;
  return () => {
    if (A)
      return Me(H, null), H;
    if (!a) {
      var s = (
        /** @type {DocumentFragment} */
        An(o)
      ), i = (
        /** @type {Element} */
        /* @__PURE__ */ Ee(s)
      );
      a = /** @type {Element} */
      /* @__PURE__ */ Ee(i);
    }
    var u = (
      /** @type {TemplateNode} */
      a.cloneNode(!0)
    );
    return Me(u, u), u;
  };
}
function le(e = "") {
  if (!A) {
    var t = Ye(e + "");
    return Me(t, t), t;
  }
  var r = H;
  return r.nodeType !== 3 && (r.before(r = Ye()), pe(r)), Me(r, r), r;
}
function Rr() {
  if (A)
    return Me(H, null), H;
  var e = document.createDocumentFragment(), t = document.createComment(""), r = Ye();
  return e.append(t, r), Me(t, r), e;
}
function $(e, t) {
  if (A) {
    B.nodes_end = H, Ct();
    return;
  }
  e !== null && e.before(
    /** @type {Node} */
    t
  );
}
function F(e, t) {
  var r = t == null ? "" : typeof t == "object" ? t + "" : t;
  r !== (e.__t ?? (e.__t = e.nodeValue)) && (e.__t = r, e.nodeValue = r + "");
}
function Ln(e, t) {
  return Dn(e, t);
}
function fa(e, t) {
  vr(), t.intro = t.intro ?? !1;
  const r = t.target, n = A, o = H;
  try {
    for (var a = (
      /** @type {TemplateNode} */
      /* @__PURE__ */ Ee(r)
    ); a && (a.nodeType !== 8 || /** @type {Comment} */
    a.data !== wr); )
      a = /** @type {TemplateNode} */
      /* @__PURE__ */ je(a);
    if (!a)
      throw wt;
    Re(!0), pe(
      /** @type {Comment} */
      a
    ), Ct();
    const s = Dn(e, { ...t, anchor: a });
    if (H === null || H.nodeType !== 8 || /** @type {Comment} */
    H.data !== yr)
      throw Er(), wt;
    return Re(!1), /**  @type {Exports} */
    s;
  } catch (s) {
    if (s === wt)
      return t.recover === !1 && Oo(), vr(), qr(r), Re(!1), Ln(e, t);
    throw s;
  } finally {
    Re(n), pe(o);
  }
}
const at = /* @__PURE__ */ new Map();
function Dn(e, { target: t, anchor: r, props: n = {}, events: o, context: a, intro: s = !0 }) {
  vr();
  var i = /* @__PURE__ */ new Set(), u = (m) => {
    for (var l = 0; l < m.length; l++) {
      var f = m[l];
      if (!i.has(f)) {
        i.add(f);
        var d = la(f);
        t.addEventListener(f, bt, { passive: d });
        var p = at.get(f);
        p === void 0 ? (document.addEventListener(f, bt, { passive: d }), at.set(f, 1)) : at.set(f, p + 1);
      }
    }
  };
  u(Cr(Tn)), mr.add(u);
  var c = void 0, v = aa(() => {
    var m = r ?? t.appendChild(Ye());
    return $t(() => {
      if (a) {
        we({});
        var l = (
          /** @type {ComponentContext} */
          U
        );
        l.c = a;
      }
      o && (n.$$events = o), A && Me(
        /** @type {TemplateNode} */
        m,
        null
      ), c = e(m, n) || {}, A && (B.nodes_end = H), a && _e();
    }), () => {
      var d;
      for (var l of i) {
        t.removeEventListener(l, bt);
        var f = (
          /** @type {number} */
          at.get(l)
        );
        --f === 0 ? (document.removeEventListener(l, bt), at.delete(l)) : at.set(l, f);
      }
      mr.delete(u), m !== r && ((d = m.parentNode) == null || d.removeChild(m));
    };
  });
  return gr.set(c, v), c;
}
let gr = /* @__PURE__ */ new WeakMap();
function va(e, t) {
  const r = gr.get(e);
  return r ? (gr.delete(e), r(t)) : Promise.resolve();
}
function $e(e, t, [r, n] = [0, 0]) {
  A && r === 0 && Ct();
  var o = e, a = null, s = null, i = fe, u = r > 0 ? xr : 0, c = !1;
  const v = (l, f = !0) => {
    c = !0, m(f, l);
  }, m = (l, f) => {
    if (i === (i = l)) return;
    let d = !1;
    if (A && n !== -1) {
      if (r === 0) {
        const w = (
          /** @type {Comment} */
          o.data
        );
        w === wr ? n = 0 : w === _r ? n = 1 / 0 : (n = parseInt(w.substring(1)), n !== n && (n = i ? 1 / 0 : -1));
      }
      const p = n > r;
      !!i === p && (o = fr(), pe(o), Re(!1), d = !0, n = -1);
    }
    i ? (a ? Yt(a) : f && (a = $t(() => f(o))), s && Bt(s, () => {
      s = null;
    })) : (s ? Yt(s) : f && (s = $t(() => f(o, [r + 1, n]))), a && Bt(a, () => {
      a = null;
    })), d && Re(!0);
  };
  Pr(() => {
    c = !1, t(v), c || m(null, null);
  }, u), A && (o = H);
}
function Zr(e, t) {
  A && pe(
    /** @type {TemplateNode} */
    /* @__PURE__ */ Ee(e)
  ), vt(() => {
    var r = t();
    for (var n in r) {
      var o = r[n];
      o ? e.style.setProperty(n, o) : e.style.removeProperty(n);
    }
  }), tr(() => {
    e.remove();
  });
}
function ha(e, t, r, n) {
  for (var o = [], a = t.length, s = 0; s < a; s++)
    Nr(t[s].e, o, !0);
  var i = a > 0 && o.length === 0 && r !== null;
  if (i) {
    var u = (
      /** @type {Element} */
      /** @type {Element} */
      r.parentNode
    );
    qr(u), u.append(
      /** @type {Element} */
      r
    ), n.clear(), Ve(e, t[0].prev, t[a - 1].next);
  }
  In(o, () => {
    for (var c = 0; c < a; c++) {
      var v = t[c];
      i || (n.delete(v.k), Ve(e, v.prev, v.next)), Ae(v.e, !i);
    }
  });
}
function Tr(e, t, r, n, o, a = null) {
  var s = e, i = { flags: t, items: /* @__PURE__ */ new Map(), first: null }, u = (t & on) !== 0;
  if (u) {
    var c = (
      /** @type {Element} */
      e
    );
    s = A ? pe(
      /** @type {Comment | Text} */
      /* @__PURE__ */ Ee(c)
    ) : c.appendChild(Ye());
  }
  A && Ct();
  var v = null, m = !1, l = /* @__PURE__ */ me(() => {
    var f = r();
    return $r(f) ? f : f == null ? [] : Cr(f);
  });
  Pr(() => {
    var f = h(l), d = f.length;
    if (m && d === 0)
      return;
    m = d === 0;
    let p = !1;
    if (A) {
      var w = (
        /** @type {Comment} */
        s.data === _r
      );
      w !== (d === 0) && (s = fr(), pe(s), Re(!1), p = !0);
    }
    if (A) {
      for (var I = null, _, k = 0; k < d; k++) {
        if (H.nodeType === 8 && /** @type {Comment} */
        H.data === yr) {
          s = /** @type {Comment} */
          H, p = !0, Re(!1);
          break;
        }
        var y = f[k], g = n(y, k);
        _ = jn(
          H,
          i,
          I,
          null,
          y,
          g,
          k,
          o,
          t,
          r
        ), i.items.set(g, _), I = _;
      }
      d > 0 && pe(fr());
    }
    A || ma(f, i, s, o, t, n, r), a !== null && (d === 0 ? v ? Yt(v) : v = $t(() => a(s)) : v !== null && Bt(v, () => {
      v = null;
    })), p && Re(!0), h(l);
  }), A && (s = H);
}
function ma(e, t, r, n, o, a, s) {
  var re, z, S, D;
  var i = (o & co) !== 0, u = (o & (pr | br)) !== 0, c = e.length, v = t.items, m = t.first, l = m, f, d = null, p, w = [], I = [], _, k, y, g;
  if (i)
    for (g = 0; g < c; g += 1)
      _ = e[g], k = a(_, g), y = v.get(k), y !== void 0 && ((re = y.a) == null || re.measure(), (p ?? (p = /* @__PURE__ */ new Set())).add(y));
  for (g = 0; g < c; g += 1) {
    if (_ = e[g], k = a(_, g), y = v.get(k), y === void 0) {
      var R = l ? (
        /** @type {TemplateNode} */
        l.e.nodes_start
      ) : r;
      d = jn(
        R,
        t,
        d,
        d === null ? t.first : d.next,
        _,
        k,
        g,
        n,
        o,
        s
      ), v.set(k, d), w = [], I = [], l = d.next;
      continue;
    }
    if (u && ga(y, _, g, o), (y.e.f & Te) !== 0 && (Yt(y.e), i && ((z = y.a) == null || z.unfix(), (p ?? (p = /* @__PURE__ */ new Set())).delete(y))), y !== l) {
      if (f !== void 0 && f.has(y)) {
        if (w.length < I.length) {
          var M = I[0], b;
          d = M.prev;
          var P = w[0], N = w[w.length - 1];
          for (b = 0; b < w.length; b += 1)
            Jr(w[b], M, r);
          for (b = 0; b < I.length; b += 1)
            f.delete(I[b]);
          Ve(t, P.prev, N.next), Ve(t, d, P), Ve(t, N, M), l = M, d = N, g -= 1, w = [], I = [];
        } else
          f.delete(y), Jr(y, l, r), Ve(t, y.prev, y.next), Ve(t, y, d === null ? t.first : d.next), Ve(t, d, y), d = y;
        continue;
      }
      for (w = [], I = []; l !== null && l.k !== k; )
        (l.e.f & Te) === 0 && (f ?? (f = /* @__PURE__ */ new Set())).add(l), I.push(l), l = l.next;
      if (l === null)
        continue;
      y = l;
    }
    w.push(y), d = y, l = y.next;
  }
  if (l !== null || f !== void 0) {
    for (var L = f === void 0 ? [] : Cr(f); l !== null; )
      (l.e.f & Te) === 0 && L.push(l), l = l.next;
    var K = L.length;
    if (K > 0) {
      var Q = (o & on) !== 0 && c === 0 ? r : null;
      if (i) {
        for (g = 0; g < K; g += 1)
          (S = L[g].a) == null || S.measure();
        for (g = 0; g < K; g += 1)
          (D = L[g].a) == null || D.fix();
      }
      ha(t, L, Q, v);
    }
  }
  i && Kt(() => {
    var X;
    if (p !== void 0)
      for (y of p)
        (X = y.a) == null || X.apply();
  }), B.first = t.first && t.first.e, B.last = d && d.e;
}
function ga(e, t, r, n) {
  (n & pr) !== 0 && dr(e.v, t), (n & br) !== 0 ? dr(
    /** @type {Value<number>} */
    e.i,
    r
  ) : e.i = r;
}
function jn(e, t, r, n, o, a, s, i, u, c) {
  var v = (u & pr) !== 0, m = (u & fo) === 0, l = v ? m ? /* @__PURE__ */ Gt(o) : ve(o) : o, f = (u & br) === 0 ? s : ve(s), d = {
    i: f,
    v: l,
    k: a,
    a: null,
    // @ts-expect-error
    e: null,
    prev: r,
    next: n
  };
  try {
    return d.e = $t(() => i(e, l, f, c), A), d.e.prev = r && r.e, d.e.next = n && n.e, r === null ? t.first = d : (r.next = d, r.e.next = d.e), n !== null && (n.prev = d, n.e.prev = d.e), d;
  } finally {
  }
}
function Jr(e, t, r) {
  for (var n = e.next ? (
    /** @type {TemplateNode} */
    e.next.e.nodes_start
  ) : r, o = t ? (
    /** @type {TemplateNode} */
    t.e.nodes_start
  ) : r, a = (
    /** @type {TemplateNode} */
    e.e.nodes_start
  ); a !== n; ) {
    var s = (
      /** @type {TemplateNode} */
      /* @__PURE__ */ je(a)
    );
    o.before(a), a = s;
  }
}
function Ve(e, t, r) {
  t === null ? e.first = r : (t.next = r, t.e.next = r && r.e), r !== null && (r.prev = t, r.e.prev = t && t.e);
}
function Kr(e, t, r, n, o) {
  var i;
  A && Ct();
  var a = (i = t.$$slots) == null ? void 0 : i[r], s = !1;
  a === !0 && (a = t[r === "default" ? "children" : r], s = !0), a === void 0 || a(e, s ? () => n : n);
}
function pa(e) {
  const t = {};
  e.children && (t.default = !0);
  for (const r in e.$$slots)
    t[r] = !0;
  return t;
}
function se(e, t) {
  Kt(() => {
    var r = e.getRootNode(), n = (
      /** @type {ShadowRoot} */
      r.host ? (
        /** @type {ShadowRoot} */
        r
      ) : (
        /** @type {Document} */
        r.head ?? /** @type {Document} */
        r.ownerDocument.head
      )
    );
    if (!n.querySelector("#" + t.hash)) {
      const o = document.createElement("style");
      o.id = t.hash, o.textContent = t.code, n.appendChild(o);
    }
  });
}
const Gr = [...` 	
\r\f \v\uFEFF`];
function ba(e, t, r) {
  var n = e == null ? "" : "" + e;
  if (t && (n = n ? n + " " + t : t), r) {
    for (var o in r)
      if (r[o])
        n = n ? n + " " + o : o;
      else if (n.length)
        for (var a = o.length, s = 0; (s = n.indexOf(o, s)) >= 0; ) {
          var i = s + a;
          (s === 0 || Gr.includes(n[s - 1])) && (i === n.length || Gr.includes(n[i])) ? n = (s === 0 ? "" : n.substring(0, s)) + n.substring(i + 1) : s = i;
        }
  }
  return n === "" ? null : n;
}
function wa(e, t) {
  return e == null ? null : String(e);
}
function ot(e, t, r, n, o, a) {
  var s = e.__className;
  if (A || s !== r) {
    var i = ba(r, n, a);
    (!A || i !== e.getAttribute("class")) && (i == null ? e.removeAttribute("class") : e.className = i), e.__className = r;
  } else if (a && o !== a)
    for (var u in a) {
      var c = !!a[u];
      (o == null || c !== !!o[u]) && e.classList.toggle(u, c);
    }
  return a;
}
function _a(e, t, r, n) {
  var o = e.__style;
  if (A || o !== t) {
    var a = wa(t);
    (!A || a !== e.getAttribute("style")) && (a == null ? e.removeAttribute("style") : e.style.cssText = a), e.__style = t;
  }
  return n;
}
const ya = Symbol("is custom element"), ka = Symbol("is html");
function xa(e) {
  if (A) {
    var t = !1, r = () => {
      if (!t) {
        if (t = !0, e.hasAttribute("value")) {
          var n = e.value;
          G(e, "value", null), e.value = n;
        }
        if (e.hasAttribute("checked")) {
          var o = e.checked;
          G(e, "checked", null), e.checked = o;
        }
      }
    };
    e.__on_r = r, Io(r), Nn();
  }
}
function G(e, t, r, n) {
  var o = $a(e);
  A && (o[t] = e.getAttribute(t), t === "src" || t === "srcset" || t === "href" && e.nodeName === "LINK") || o[t] !== (o[t] = r) && (t === "loading" && (e[$o] = r), r == null ? e.removeAttribute(t) : typeof r != "string" && Ca(e).includes(t) ? e[t] = r : e.setAttribute(t, r));
}
function $a(e) {
  return (
    /** @type {Record<string | symbol, unknown>} **/
    // @ts-expect-error
    e.__attributes ?? (e.__attributes = {
      [ya]: e.nodeName.includes("-"),
      [ka]: e.namespaceURI === _o
    })
  );
}
var Qr = /* @__PURE__ */ new Map();
function Ca(e) {
  var t = Qr.get(e.nodeName);
  if (t) return t;
  Qr.set(e.nodeName, t = []);
  for (var r, n = e, o = Element.prototype; o !== n; ) {
    r = un(n);
    for (var a in r)
      r[a].set && t.push(a);
    n = zr(n);
  }
  return t;
}
function Hn(e, t, r = t) {
  var n = Et();
  ca(e, "input", (o) => {
    var a = o ? e.defaultValue : e.value;
    if (a = ir(e) ? lr(a) : a, r(a), n && a !== (a = t())) {
      var s = e.selectionStart, i = e.selectionEnd;
      e.value = a ?? "", i !== null && (e.selectionStart = s, e.selectionEnd = Math.min(i, e.value.length));
    }
  }), // If we are hydrating and the value has since changed,
  // then use the updated value from the input instead.
  (A && e.defaultValue !== e.value || // If defaultValue is set, then value == defaultValue
  // TODO Svelte 6: remove input.value check and set to empty string?
  Ue(t) == null && e.value) && r(ir(e) ? lr(e.value) : e.value), vt(() => {
    var o = t();
    ir(e) && o === lr(e.value) || e.type === "date" && !o && !e.value || o !== e.value && (e.value = o ?? "");
  });
}
function ir(e) {
  var t = e.type;
  return t === "number" || t === "range";
}
function lr(e) {
  return e === "" ? null : +e;
}
function en(e, t) {
  return e === t || (e == null ? void 0 : e[Ke]) === t;
}
function tt(e = {}, t, r, n) {
  return Mr(() => {
    var o, a;
    return vt(() => {
      o = a, a = [], Ue(() => {
        e !== r(...a) && (t(e, ...a), o && en(r(...o), e) && t(null, ...o));
      });
    }), () => {
      Kt(() => {
        a && en(r(...a), e) && t(null, ...a);
      });
    };
  }), e;
}
function za(e, t, r, n, o) {
  var a = () => {
    n(r[e]);
  };
  r.addEventListener(t, a), o ? vt(() => {
    r[e] = o();
  }) : a(), (r === document.body || r === window || r === document) && tr(() => {
    r.removeEventListener(t, a);
  });
}
function Fn(e = !1) {
  const t = (
    /** @type {ComponentContextLegacy} */
    U
  ), r = t.l.u;
  if (!r) return;
  let n = () => ta(t.s);
  if (e) {
    let o = 0, a = (
      /** @type {Record<string, any>} */
      {}
    );
    const s = /* @__PURE__ */ ee(() => {
      let i = !1;
      const u = t.s;
      for (const c in u)
        u[c] !== a[c] && (a[c] = u[c], i = !0);
      return i && o++, o;
    });
    n = () => h(s);
  }
  r.b.length && na(() => {
    tn(t, n), Dt(r.b);
  }), et(() => {
    const o = Ue(() => r.m.map(Eo));
    return () => {
      for (const a of o)
        typeof a == "function" && a();
    };
  }), r.a.length && et(() => {
    tn(t, n), Dt(r.a);
  });
}
function tn(e, t) {
  if (e.l.s)
    for (const r of e.l.s) h(r);
  t();
}
function Sa(e) {
  var t = ve(0);
  return function() {
    return arguments.length === 1 ? (T(t, h(t) + 1), arguments[0]) : (h(t), e());
  };
}
function rr(e) {
  U === null && Pn(), ft && U.l !== null ? qa(U).m.push(e) : et(() => {
    const t = Ue(e);
    if (typeof t == "function") return (
      /** @type {() => void} */
      t
    );
  });
}
function Ea(e) {
  U === null && Pn(), rr(() => () => Ue(e));
}
function qa(e) {
  var t = (
    /** @type {ComponentContextLegacy} */
    e.l
  );
  return t.u ?? (t.u = { a: [], b: [], m: [] });
}
let Pt = !1;
function Ia(e) {
  var t = Pt;
  try {
    return Pt = !1, [e(), Pt];
  } finally {
    Pt = t;
  }
}
function rn(e) {
  var t;
  return ((t = e.ctx) == null ? void 0 : t.d) ?? !1;
}
function q(e, t, r, n) {
  var R;
  var o = (r & vo) !== 0, a = !ft || (r & ho) !== 0, s = (r & go) !== 0, i = (r & po) !== 0, u = !1, c;
  s ? [c, u] = Ia(() => (
    /** @type {V} */
    e[t]
  )) : c = /** @type {V} */
  e[t];
  var v = Ke in e || ln in e, m = s && (((R = Ge(e, t)) == null ? void 0 : R.set) ?? (v && t in e && ((M) => e[t] = M))) || void 0, l = (
    /** @type {V} */
    n
  ), f = !0, d = !1, p = () => (d = !0, f && (f = !1, i ? l = Ue(
    /** @type {() => V} */
    n
  ) : l = /** @type {V} */
  n), l);
  c === void 0 && n !== void 0 && (m && a && Ao(), c = p(), m && m(c));
  var w;
  if (a)
    w = () => {
      var M = (
        /** @type {V} */
        e[t]
      );
      return M === void 0 ? p() : (f = !0, d = !1, M);
    };
  else {
    var I = (o ? ee : me)(
      () => (
        /** @type {V} */
        e[t]
      )
    );
    I.f |= ko, w = () => {
      var M = h(I);
      return M !== void 0 && (l = /** @type {V} */
      void 0), M === void 0 ? l : M;
    };
  }
  if ((r & mo) === 0)
    return w;
  if (m) {
    var _ = e.$$legacy;
    return function(M, b) {
      return arguments.length > 0 ? ((!a || !b || _ || u) && m(b ? w() : M), M) : w();
    };
  }
  var k = !1, y = /* @__PURE__ */ Gt(c), g = /* @__PURE__ */ ee(() => {
    var M = w(), b = h(y);
    return k ? (k = !1, b) : y.v = M;
  });
  return s && h(g), o || (g.equals = Sr), function(M, b) {
    if (arguments.length > 0) {
      const P = b ? h(g) : a && s ? J(M) : M;
      if (!g.equals(P)) {
        if (k = !0, T(y, P), d && l !== void 0 && (l = P), rn(g))
          return M;
        Ue(() => h(g));
      }
      return M;
    }
    return rn(g) ? g.v : h(g);
  };
}
function Ma(e) {
  return new Pa(e);
}
var Ne, xe;
class Pa {
  /**
   * @param {ComponentConstructorOptions & {
   *  component: any;
   * }} options
   */
  constructor(t) {
    /** @type {any} */
    ar(this, Ne);
    /** @type {Record<string, any>} */
    ar(this, xe);
    var a;
    var r = /* @__PURE__ */ new Map(), n = (s, i) => {
      var u = /* @__PURE__ */ Gt(i);
      return r.set(s, u), u;
    };
    const o = new Proxy(
      { ...t.props || {}, $$events: {} },
      {
        get(s, i) {
          return h(r.get(i) ?? n(i, Reflect.get(s, i)));
        },
        has(s, i) {
          return i === ln ? !0 : (h(r.get(i) ?? n(i, Reflect.get(s, i))), Reflect.has(s, i));
        },
        set(s, i, u) {
          return T(r.get(i) ?? n(i, u), u), Reflect.set(s, i, u);
        }
      }
    );
    sr(this, xe, (t.hydrate ? fa : Ln)(t.component, {
      target: t.target,
      anchor: t.anchor,
      props: o,
      context: t.context,
      intro: t.intro ?? !1,
      recover: t.recover
    })), (!((a = t == null ? void 0 : t.props) != null && a.$$host) || t.sync === !1) && E(), sr(this, Ne, o.$$events);
    for (const s of Object.keys(de(this, xe)))
      s === "$set" || s === "$destroy" || s === "$on" || Lt(this, s, {
        get() {
          return de(this, xe)[s];
        },
        /** @param {any} value */
        set(i) {
          de(this, xe)[s] = i;
        },
        enumerable: !0
      });
    de(this, xe).$set = /** @param {Record<string, any>} next */
    (s) => {
      Object.assign(o, s);
    }, de(this, xe).$destroy = () => {
      va(de(this, xe));
    };
  }
  /** @param {Record<string, any>} props */
  $set(t) {
    de(this, xe).$set(t);
  }
  /**
   * @param {string} event
   * @param {(...args: any[]) => any} callback
   * @returns {any}
   */
  $on(t, r) {
    de(this, Ne)[t] = de(this, Ne)[t] || [];
    const n = (...o) => r.call(this, ...o);
    return de(this, Ne)[t].push(n), () => {
      de(this, Ne)[t] = de(this, Ne)[t].filter(
        /** @param {any} fn */
        (o) => o !== n
      );
    };
  }
  $destroy() {
    de(this, xe).$destroy();
  }
}
Ne = new WeakMap(), xe = new WeakMap();
let Vn;
typeof HTMLElement == "function" && (Vn = class extends HTMLElement {
  /**
   * @param {*} $$componentCtor
   * @param {*} $$slots
   * @param {*} use_shadow_dom
   */
  constructor(t, r, n) {
    super();
    /** The Svelte component constructor */
    oe(this, "$$ctor");
    /** Slots */
    oe(this, "$$s");
    /** @type {any} The Svelte component instance */
    oe(this, "$$c");
    /** Whether or not the custom element is connected */
    oe(this, "$$cn", !1);
    /** @type {Record<string, any>} Component props data */
    oe(this, "$$d", {});
    /** `true` if currently in the process of reflecting component props back to attributes */
    oe(this, "$$r", !1);
    /** @type {Record<string, CustomElementPropDefinition>} Props definition (name, reflected, type etc) */
    oe(this, "$$p_d", {});
    /** @type {Record<string, EventListenerOrEventListenerObject[]>} Event listeners */
    oe(this, "$$l", {});
    /** @type {Map<EventListenerOrEventListenerObject, Function>} Event listener unsubscribe functions */
    oe(this, "$$l_u", /* @__PURE__ */ new Map());
    /** @type {any} The managed render effect for reflecting attributes */
    oe(this, "$$me");
    this.$$ctor = t, this.$$s = r, n && this.attachShadow({ mode: "open" });
  }
  /**
   * @param {string} type
   * @param {EventListenerOrEventListenerObject} listener
   * @param {boolean | AddEventListenerOptions} [options]
   */
  addEventListener(t, r, n) {
    if (this.$$l[t] = this.$$l[t] || [], this.$$l[t].push(r), this.$$c) {
      const o = this.$$c.$on(t, r);
      this.$$l_u.set(r, o);
    }
    super.addEventListener(t, r, n);
  }
  /**
   * @param {string} type
   * @param {EventListenerOrEventListenerObject} listener
   * @param {boolean | AddEventListenerOptions} [options]
   */
  removeEventListener(t, r, n) {
    if (super.removeEventListener(t, r, n), this.$$c) {
      const o = this.$$l_u.get(r);
      o && (o(), this.$$l_u.delete(r));
    }
  }
  async connectedCallback() {
    if (this.$$cn = !0, !this.$$c) {
      let t = function(o) {
        return (a) => {
          const s = document.createElement("slot");
          o !== "default" && (s.name = o), $(a, s);
        };
      };
      if (await Promise.resolve(), !this.$$cn || this.$$c)
        return;
      const r = {}, n = Na(this);
      for (const o of this.$$s)
        o in n && (o === "default" && !this.$$d.children ? (this.$$d.children = t(o), r.default = !0) : r[o] = t(o));
      for (const o of this.attributes) {
        const a = this.$$g_p(o.name);
        a in this.$$d || (this.$$d[a] = Rt(a, o.value, this.$$p_d, "toProp"));
      }
      for (const o in this.$$p_d)
        !(o in this.$$d) && this[o] !== void 0 && (this.$$d[o] = this[o], delete this[o]);
      this.$$c = Ma({
        component: this.$$ctor,
        target: this.shadowRoot || this,
        props: {
          ...this.$$d,
          $$slots: r,
          $$host: this
        }
      }), this.$$me = oa(() => {
        vt(() => {
          var o;
          this.$$r = !0;
          for (const a of At(this.$$c)) {
            if (!((o = this.$$p_d[a]) != null && o.reflect)) continue;
            this.$$d[a] = this.$$c[a];
            const s = Rt(
              a,
              this.$$d[a],
              this.$$p_d,
              "toAttribute"
            );
            s == null ? this.removeAttribute(this.$$p_d[a].attribute || a) : this.setAttribute(this.$$p_d[a].attribute || a, s);
          }
          this.$$r = !1;
        });
      });
      for (const o in this.$$l)
        for (const a of this.$$l[o]) {
          const s = this.$$c.$on(o, a);
          this.$$l_u.set(a, s);
        }
      this.$$l = {};
    }
  }
  // We don't need this when working within Svelte code, but for compatibility of people using this outside of Svelte
  // and setting attributes through setAttribute etc, this is helpful
  /**
   * @param {string} attr
   * @param {string} _oldValue
   * @param {string} newValue
   */
  attributeChangedCallback(t, r, n) {
    var o;
    this.$$r || (t = this.$$g_p(t), this.$$d[t] = Rt(t, n, this.$$p_d, "toProp"), (o = this.$$c) == null || o.$set({ [t]: this.$$d[t] }));
  }
  disconnectedCallback() {
    this.$$cn = !1, Promise.resolve().then(() => {
      !this.$$cn && this.$$c && (this.$$c.$destroy(), this.$$me(), this.$$c = void 0);
    });
  }
  /**
   * @param {string} attribute_name
   */
  $$g_p(t) {
    return At(this.$$p_d).find(
      (r) => this.$$p_d[r].attribute === t || !this.$$p_d[r].attribute && r.toLowerCase() === t
    ) || t;
  }
});
function Rt(e, t, r, n) {
  var a;
  const o = (a = r[e]) == null ? void 0 : a.type;
  if (t = o === "Boolean" && typeof t != "boolean" ? t != null : t, !n || !r[e])
    return t;
  if (n === "toAttribute")
    switch (o) {
      case "Object":
      case "Array":
        return t == null ? null : JSON.stringify(t);
      case "Boolean":
        return t ? "" : null;
      case "Number":
        return t ?? null;
      default:
        return t;
    }
  else
    switch (o) {
      case "Object":
      case "Array":
        return t && JSON.parse(t);
      case "Boolean":
        return t;
      // conversion already handled above
      case "Number":
        return t != null ? +t : t;
      default:
        return t;
    }
}
function Na(e) {
  const t = {};
  return e.childNodes.forEach((r) => {
    t[
      /** @type {Element} node */
      r.slot || "default"
    ] = !0;
  }), t;
}
function ae(e, t, r, n, o, a) {
  let s = class extends Vn {
    constructor() {
      super(e, r, o), this.$$p_d = t;
    }
    static get observedAttributes() {
      return At(t).map(
        (i) => (t[i].attribute || i).toLowerCase()
      );
    }
  };
  return At(t).forEach((i) => {
    Lt(s.prototype, i, {
      get() {
        return this.$$c && i in this.$$c ? this.$$c[i] : this.$$d[i];
      },
      set(u) {
        var m;
        u = Rt(i, u, t), this.$$d[i] = u;
        var c = this.$$c;
        if (c) {
          var v = (m = Ge(c, i)) == null ? void 0 : m.get;
          v ? c[i] = u : c.$set({ [i]: u });
        }
      }
    });
  }), n.forEach((i) => {
    Lt(s.prototype, i, {
      get() {
        var u;
        return (u = this.$$c) == null ? void 0 : u[i];
      }
    });
  }), e.element = /** @type {any} */
  s, s;
}
const Z = J({
  open: !1,
  currentCommentId: 0,
  showResolvedOnly: !1,
  pulseMarkerId: 0
}), Je = J({ open: !1 });
let st = ue("");
const lt = {
  get value() {
    return h(st);
  },
  set(e) {
    T(st, J(e)), typeof window < "u" && sessionStorage.setItem("loop-guest-name", e);
  },
  get() {
    return !h(st) && typeof window < "u" && T(st, J(sessionStorage.getItem("loop-guest-name") || "")), h(st);
  },
  clear() {
    T(st, ""), typeof window < "u" && sessionStorage.removeItem("loop-guest-name");
  }
};
let Or = ue(J({}));
const O = (e, t) => h(Or)[e] || t || e, ur = (e, t, r) => {
  let n = h(Or)[e] || t || e;
  for (const [o, a] of Object.entries(r))
    n = n.replace(`{${o}}`, a);
  return n;
}, Ra = (e) => {
  T(Or, J(e));
};
Vo();
var Ta = /* @__PURE__ */ qt('<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" class="svelte-1q9atlb"><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.5"></path><path d="M15 12H12M12 12H9M12 12V9M12 12V15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg>');
const Oa = {
  hash: "svelte-1q9atlb",
  code: "svg.svelte-1q9atlb{width:var(--size,var(--icon-size));height:var(--size,var(--icon-size))}"
};
function Bn(e) {
  se(e, Oa);
  var t = Ta();
  $(e, t);
}
ae(Bn, {}, [], [], !0);
var Aa = /* @__PURE__ */ W('<span class="svelte-1qoozz7"><!></span>'), La = /* @__PURE__ */ W("<button><!> <!></button>");
const Da = {
  hash: "svelte-1qoozz7",
  code: "button.svelte-1qoozz7{appearance:none;background-color:var(--button-background);color:var(--button-color);padding:var(--button-padding);font-family:var(--font-family);letter-spacing:.01em;border-radius:var(--button-border-radius);gap:var(--button-gap);cursor:pointer;font-size:var(--button-font-size);font-weight:var(--button-font-weight);transition:var(--button-transition);white-space:nowrap;height:var(--button-height);outline-color:var(--button-outline-color);border:0;flex:none;justify-content:center;align-items:center;line-height:1;display:inline-flex}button.svelte-1qoozz7:focus-visible{outline-offset:var(--button-outline-offset)}button.svelte-1qoozz7:hover,button.svelte-1qoozz7:focus-visible{color:var(--button-hover-color);background-color:var(--button-hover-background)}button.svelte-1qoozz7 span:where(.svelte-1qoozz7){text-overflow:ellipsis;min-width:0;display:block;overflow-x:clip}button.button--header.svelte-1qoozz7{--icon-size:1.25rem;background-color:var(--button-header-background);height:var(--button-header-height);padding:var(--button-header-padding);mix-blend-mode:var(--button-header-blend-mode);border:0;border-radius:0}button.button--header.svelte-1qoozz7:first-child{border-top-left-radius:var(--border-radius-rounded);border-bottom-left-radius:var(--border-radius-rounded)}button.button--header.svelte-1qoozz7:hover,button.button--header.svelte-1qoozz7:focus-visible{background-color:var(--button-header-hover-background)}button.button--panel.svelte-1qoozz7{background-color:var(--button-panel-background);height:auto;padding:var(--button-panel-padding);border-radius:var(--border-radius-rounded);border:0}button.button--panel.svelte-1qoozz7 span:where(.svelte-1qoozz7){overflow:visible}button.button--solid.svelte-1qoozz7{background-color:var(--button-solid-background)}button.button--solid.svelte-1qoozz7:hover,button.button--solid.svelte-1qoozz7:focus-visible{color:var(--button-solid-hover-color);background-color:var(--button-solid-hover-background)}button.button--small.svelte-1qoozz7{height:var(--button-small-height);font-size:var(--button-small-font-size)}button.button--icon.svelte-1qoozz7{background-color:var(--button-icon-background);color:var(--button-icon-color);height:var(--button-icon-height);box-shadow:var(--button-icon-shadow);aspect-ratio:1;font-size:var(--button-icon-font-size);border-radius:var(--button-icon-border-radius);border:0;padding:0}button.button--icon.svelte-1qoozz7:hover,button.button--icon.svelte-1qoozz7:focus-visible{background-color:var(--button-icon-hover-background);color:var(--button-icon-hover-color)}button.button--marker.svelte-1qoozz7{background-color:var(--button-marker-background);color:var(--button-marker-color);height:var(--marker-size);width:var(--marker-size);font-weight:var(--button-marker-font-weight);border-radius:var(--button-marker-border-radius);border:0;padding:0}button.button--marker.svelte-1qoozz7 :where(.svelte-1qoozz7){pointer-events:none}button.button--marker.button--marker-highlighted.svelte-1qoozz7{background-color:var(--button-marker-highlighted-background);color:var(--button-marker-highlighted-color)}button.button--marker-open.svelte-1qoozz7{background-color:var(--color-accent);color:var(--color-accent-dark)}button.button--filter.svelte-1qoozz7{background-color:var(--button-filter-background);color:var(--button-filter-color);height:var(--button-filter-height);font-size:var(--button-filter-font-size);padding:var(--button-filter-padding);border-radius:var(--button-filter-border-radius);flex:1}button.button--filter.svelte-1qoozz7:hover,button.button--filter.svelte-1qoozz7:focus-visible{color:var(--button-filter-hover-color);background-color:var(--button-filter-hover-background)}button.button--filter.button--filter-active.svelte-1qoozz7{background-color:var(--button-filter-active-background);color:var(--button-filter-active-color);font-weight:var(--button-filter-active-font-weight)}button.button--filter.button--filter-active.svelte-1qoozz7:hover,button.button--filter.button--filter-active.svelte-1qoozz7:focus-visible{background-color:var(--button-filter-active-background);color:var(--button-filter-active-color)}button.button--menu-item.svelte-1qoozz7{background-color:var(--button-menu-item-background);color:var(--button-menu-item-color);width:100%;padding:var(--button-menu-item-padding);border-radius:var(--button-menu-item-border-radius);font-size:var(--button-menu-item-font-size);justify-content:flex-start;gap:var(--button-menu-item-gap)}button.button--menu-item.svelte-1qoozz7:hover,button.button--menu-item.svelte-1qoozz7:focus-visible{background-color:var(--button-menu-item-hover-background);color:var(--button-menu-item-hover-color)}button.button--menu-item.button--menu-item-active.svelte-1qoozz7{background-color:var(--button-menu-item-active-background);color:var(--button-menu-item-active-color);font-weight:var(--button-menu-item-active-font-weight)}button.button--menu-item.button--menu-item-active.svelte-1qoozz7:hover,button.button--menu-item.button--menu-item-active.svelte-1qoozz7:focus-visible{background-color:var(--button-menu-item-active-background);color:var(--button-menu-item-active-color)}button.is-active.svelte-1qoozz7{background-color:var(--button-active-background);color:var(--button-active-color)}button.is-active.svelte-1qoozz7:hover,button.is-active.svelte-1qoozz7:focus-visible{color:var(--button-active-color);background-color:var(--button-active-background)}button.svelte-1qoozz7:disabled{opacity:var(--button-disabled-opacity);cursor:not-allowed}button.svelte-1qoozz7:disabled:hover{color:var(--button-disabled-hover-color);background-color:var(--button-disabled-hover-background)}"
};
function te(e, t) {
  const r = pa(t);
  we(t, !0), se(e, Da);
  const n = q(t, "onclick", 7), o = q(t, "onmouseenter", 7), a = q(t, "onmouseout", 7), s = q(t, "onblur", 7), i = q(t, "active", 7, !1), u = q(t, "type", 7, "button"), c = q(t, "style", 7, ""), v = q(t, "disabled", 7, !1), m = q(t, "ariaLabel", 7, ""), l = q(t, "id", 7, ""), f = q(t, "ariaHaspopup", 7, ""), d = q(t, "ariaExpanded", 7, ""), p = q(t, "ariaControls", 7, "");
  var w = La();
  w.__click = function(...g) {
    var R;
    (R = n()) == null || R.apply(this, g);
  };
  let I;
  w.__mouseout = function(...g) {
    var R;
    (R = a()) == null || R.apply(this, g);
  };
  var _ = C(w);
  Kr(_, t, "icon", {});
  var k = V(_, 2);
  {
    var y = (g) => {
      var R = Aa(), M = C(R);
      Kr(M, t, "default", {}), x(R), $(g, R);
    };
    $e(k, (g) => {
      r.default && g(y);
    });
  }
  return x(w), j(
    (g) => {
      I = ot(w, 1, `button ${c() ?? ""}`, "svelte-1qoozz7", I, g), G(w, "type", u()), G(w, "aria-label", m()), G(w, "id", l()), G(w, "aria-haspopup", f() === "menu" ? "menu" : null), G(w, "aria-expanded", d() === "true" ? !0 : d() === "false" ? !1 : null), G(w, "aria-controls", p() || null), w.disabled = v();
    },
    [() => ({ "is-active": i() })]
  ), Ze("mouseenter", w, function(...g) {
    var R;
    (R = o()) == null || R.apply(this, g);
  }), Ze("blur", w, function(...g) {
    var R;
    (R = s()) == null || R.apply(this, g);
  }), $(e, w), _e({
    get onclick() {
      return n();
    },
    set onclick(g) {
      n(g), E();
    },
    get onmouseenter() {
      return o();
    },
    set onmouseenter(g) {
      o(g), E();
    },
    get onmouseout() {
      return a();
    },
    set onmouseout(g) {
      a(g), E();
    },
    get onblur() {
      return s();
    },
    set onblur(g) {
      s(g), E();
    },
    get active() {
      return i();
    },
    set active(g = !1) {
      i(g), E();
    },
    get type() {
      return u();
    },
    set type(g = "button") {
      u(g), E();
    },
    get style() {
      return c();
    },
    set style(g = "") {
      c(g), E();
    },
    get disabled() {
      return v();
    },
    set disabled(g = !1) {
      v(g), E();
    },
    get ariaLabel() {
      return m();
    },
    set ariaLabel(g = "") {
      m(g), E();
    },
    get id() {
      return l();
    },
    set id(g = "") {
      l(g), E();
    },
    get ariaHaspopup() {
      return f();
    },
    set ariaHaspopup(g = "") {
      f(g), E();
    },
    get ariaExpanded() {
      return d();
    },
    set ariaExpanded(g = "") {
      d(g), E();
    },
    get ariaControls() {
      return p();
    },
    set ariaControls(g = "") {
      p(g), E();
    }
  });
}
On(["click", "mouseout"]);
ae(
  te,
  {
    onclick: {},
    onmouseenter: {},
    onmouseout: {},
    onblur: {},
    active: {},
    type: {},
    style: {},
    disabled: {},
    ariaLabel: {},
    id: {},
    ariaHaspopup: {},
    ariaExpanded: {},
    ariaControls: {}
  },
  ["icon", "default"],
  [],
  !0
);
var ja = /* @__PURE__ */ qt('<svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" class="svelte-1q9atlb"><path d="M15.359 16.9999L12.8975 14.5385L12.1459 15.2902C11.3764 16.0597 10.9916 16.4444 10.5781 16.3536C10.1645 16.2628 9.97641 15.7522 9.60019 14.731L8.34544 11.3253C7.59492 9.28816 7.21966 8.2696 7.74463 7.74463C8.2696 7.21966 9.28816 7.59492 11.3253 8.34544L14.731 9.60019C15.7522 9.97641 16.2628 10.1645 16.3536 10.5781C16.4444 10.9916 16.0597 11.3764 15.2902 12.1459L14.5385 12.8975L16.9999 15.359C17.2548 15.6138 17.3822 15.7413 17.4411 15.8834C17.5196 16.073 17.5196 16.2859 17.4411 16.4755C17.3822 16.6176 17.2548 16.7451 16.9999 16.9999C16.7451 17.2548 16.6176 17.3822 16.4755 17.4411C16.2859 17.5196 16.073 17.5196 15.8834 17.4411C15.7413 17.3822 15.6138 17.2548 15.359 16.9999Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M2.5 12.5C2.5 7.78595 2.5 5.42893 3.96447 3.96447C5.42893 2.5 7.78595 2.5 12.5 2.5C17.214 2.5 19.5711 2.5 21.0355 3.96447C22.5 5.42893 22.5 7.78595 22.5 12.5C22.5 17.214 22.5 19.5711 21.0355 21.0355C19.5711 22.5 17.214 22.5 12.5 22.5C7.78595 22.5 5.42893 22.5 3.96447 21.0355C2.5 19.5711 2.5 17.214 2.5 12.5Z" stroke="currentColor" stroke-width="1.5"></path></svg>');
const Ha = {
  hash: "svelte-1q9atlb",
  code: "svg.svelte-1q9atlb{width:var(--size,var(--icon-size));height:var(--size,var(--icon-size))}"
};
function Yn(e) {
  se(e, Ha);
  var t = ja();
  $(e, t);
}
ae(Yn, {}, [], [], !0);
var Fa = /* @__PURE__ */ W('<svelte-css-wrapper style="display: contents"><!></svelte-css-wrapper>', 1), Va = /* @__PURE__ */ W('<svelte-css-wrapper style="display: contents"><!></svelte-css-wrapper>', 1), Ba = /* @__PURE__ */ W('<span class="count svelte-12aqd5e"> </span>'), Ya = /* @__PURE__ */ W('<header><div class="toggle svelte-12aqd5e"><!> <!></div> <!></header>');
const Wa = {
  hash: "svelte-12aqd5e",
  code: ".toggle.svelte-12aqd5e{display:flex}header.svelte-12aqd5e{position:var(--header-position);top:var(--header-top);max-width:100%;transform:var(--header-transform);color:var(--header-color);border-radius:var(--header-border-radius);z-index:var(--header-z-index);-webkit-backdrop-filter:var(--header-backdrop-filter);backdrop-filter:var(--header-backdrop-filter);box-shadow:var(--shadow-l),var(--shadow-light-edge),var(--shadow-dark-edge);background:var(--header-background);justify-content:space-between;align-items:stretch;display:flex;left:50%}header.bottom.svelte-12aqd5e{top:auto;bottom:var(--header-bottom-position)}.count.svelte-12aqd5e{width:var(--header-count-size);height:var(--header-count-size);border-radius:var(--header-count-border-radius);-webkit-backdrop-filter:var(--header-count-backdrop-filter);backdrop-filter:var(--header-count-backdrop-filter);box-shadow:var(--shadow-s),var(--shadow-light-edge),var(--shadow-dark-edge);background:var(--header-count-background);justify-content:center;align-items:center;display:flex}"
};
function Wn(e, t) {
  we(t, !0), se(e, Wa);
  const r = q(t, "position", 7), n = q(t, "commentsCount", 7);
  var o = Ya();
  let a;
  var s = C(o), i = C(s);
  const u = /* @__PURE__ */ ee(() => !Je.open);
  te(i, {
    onclick: () => {
      Je.open = !1;
    },
    get active() {
      return h(u);
    },
    style: "button--header",
    children: (l, f) => {
      ge();
      var d = le();
      j((p) => F(d, p), [() => O("ui.header.browse.mode", "Browse")]), $(l, d);
    },
    $$slots: {
      default: !0,
      icon: (l, f) => {
        var d = Fa(), p = Oe(d);
        Zr(p, () => ({ "--size": "1.5em" })), Yn(p.lastChild), x(p), $(l, d);
      }
    }
  });
  var c = V(i, 2);
  te(c, {
    onclick: () => {
      Je.open = !0;
    },
    style: "button--header",
    get active() {
      return Je.open;
    },
    children: (l, f) => {
      ge();
      var d = le();
      j((p) => F(d, p), [
        () => O("ui.header.comment.mode", "Comment")
      ]), $(l, d);
    },
    $$slots: {
      default: !0,
      icon: (l, f) => {
        var d = Va(), p = Oe(d);
        Zr(p, () => ({ "--size": "1.5em" })), Bn(p.lastChild), x(p), $(l, d);
      }
    }
  }), x(s);
  var v = V(s, 2);
  const m = /* @__PURE__ */ ee(() => `${n()} ${O("ui.header.aria.count", "unresolved comments")}`);
  return te(v, {
    onclick: () => Z.open = !Z.open,
    style: "button--panel",
    get ariaLabel() {
      return h(m);
    },
    children: (l, f) => {
      var d = Ba(), p = C(d, !0);
      x(d), j(() => F(p, n())), $(l, d);
    },
    $$slots: { default: !0 }
  }), x(o), j((l) => a = ot(o, 1, "svelte-12aqd5e", null, a, l), [
    () => ({ bottom: r() === "bottom" })
  ]), $(e, o), _e({
    get position() {
      return r();
    },
    set position(l) {
      r(l), E();
    },
    get commentsCount() {
      return n();
    },
    set commentsCount(l) {
      n(l), E();
    }
  });
}
ae(Wn, { position: {}, commentsCount: {} }, [], [], !0);
class Xa {
  constructor(t = {}) {
    oe(this, "callbacks", /* @__PURE__ */ new Set());
    oe(this, "debounceTimer", null);
    oe(this, "rafId", null);
    oe(this, "isListening", !1);
    oe(this, "options");
    oe(this, "handleResize", () => {
      this.debounceTimer && clearTimeout(this.debounceTimer), this.rafId && cancelAnimationFrame(this.rafId), this.debounceTimer = window.setTimeout(() => {
        this.options.useRAF ? this.rafId = requestAnimationFrame(() => {
          this.executeCallbacks();
        }) : this.executeCallbacks();
      }, this.options.debounceDelay);
    });
    this.options = {
      debounceDelay: t.debounceDelay ?? 100,
      useRAF: t.useRAF ?? !0
    };
  }
  executeCallbacks() {
    this.callbacks.forEach((t) => {
      try {
        t();
      } catch (r) {
        console.error("Error in resize callback:", r);
      }
    });
  }
  startListening() {
    this.isListening || (window.addEventListener("resize", this.handleResize, { passive: !0 }), this.isListening = !0);
  }
  stopListening() {
    this.isListening && (window.removeEventListener("resize", this.handleResize), this.isListening = !1);
  }
  /**
   * Add a callback to be executed on resize
   */
  subscribe(t) {
    return this.callbacks.add(t), this.startListening(), () => {
      this.callbacks.delete(t), this.callbacks.size === 0 && this.stopListening();
    };
  }
  /**
   * Clean up all resources
   */
  destroy() {
    this.callbacks.clear(), this.stopListening(), this.debounceTimer && clearTimeout(this.debounceTimer), this.rafId && cancelAnimationFrame(this.rafId);
  }
}
const Ua = new Xa();
function Za(e, t) {
  return Ua.subscribe(e);
}
const Xn = () => {
  const e = document.body, t = document.documentElement;
  return Math.max(
    e.scrollHeight,
    e.offsetHeight,
    t.clientHeight,
    t.scrollHeight,
    t.offsetHeight
  );
};
var Ja = /* @__PURE__ */ W("<div><!></div>");
const Ka = {
  hash: "svelte-4f1edl",
  code: ".marker.svelte-4f1edl{position:var(--marker-position);z-index:var(--marker-z-index);transform:var(--marker-transform);border-radius:var(--marker-border-radius)}.marker--pulse.svelte-4f1edl{animation:1.5s ease-in-out infinite svelte-4f1edl-kirby-loop-pulse}@keyframes svelte-4f1edl-kirby-loop-pulse{0%{box-shadow:0 0 0 0 var(--color-accent),0 0 0 0 #8080804d,0 0 0 0 #80808033}30%{box-shadow:0 0 0 8px #0000,0 0 #8080804d,0 0 #80808033}60%{box-shadow:0 0 0 8px #80808026,0 0 0 12px #0000,0 0 #80808033}to{box-shadow:0 0 0 16px #0000,0 0 0 12px #0000,0 0 0 8px #0000}}"
};
function Un(e, t) {
  we(t, !0), se(e, Ka);
  const r = q(t, "comment", 7);
  let n = ue(!1);
  et(() => {
    T(n, Z.pulseMarkerId === r().id);
  });
  let o = ue(null), a = ue(null), s = ue(null);
  rr(() => {
    requestAnimationFrame(() => {
      i();
    }), T(s, J(Za(() => {
      i();
    })));
  }), Ea(() => {
    h(s) && h(s)();
  });
  function i() {
    if (!(!r() || !h(o)))
      try {
        T(a, J(document.querySelector(r().selector)));
        let d, p;
        if (h(a)) {
          const Q = h(a).getBoundingClientRect(), re = Q.width * r().selectorOffsetX / 100, z = Q.height * r().selectorOffsetY / 100;
          d = Q.left + window.scrollX + re, p = Q.top + window.scrollY + z;
        } else
          d = Number(r().pagePositionX), p = Number(r().pagePositionY);
        const w = h(o).getBoundingClientRect(), I = w.width || 32, _ = w.height || 32, k = I / 2, y = _ / 2, g = Xn(), R = window.innerWidth, M = k, b = R - k, P = y, N = g - y, L = Math.max(M, Math.min(b, d)), K = Math.max(P, Math.min(N, p));
        h(o).style.left = `${L}px`, h(o).style.top = `${K}px`;
      } catch (d) {
        console.error("Error positioning marker:", d);
      }
  }
  function u(d) {
    Z.currentCommentId = d;
  }
  function c() {
    Z.currentCommentId = 0;
  }
  function v() {
    var p, w;
    Z.open = !0;
    const d = (w = (p = document.querySelector("loop")) == null ? void 0 : p.shadowRoot) == null ? void 0 : w.querySelector(`#comment-${r().id}`);
    d && d.scrollIntoView({ behavior: "smooth", block: "start" });
  }
  var m = Rr(), l = Oe(m);
  {
    var f = (d) => {
      var p = Ja();
      let w;
      var I = C(p);
      te(I, {
        onmouseenter: () => u(r().id),
        onmouseout: c,
        onblur: c,
        onclick: v,
        get style() {
          return `button--marker button--marker-${r().status ?? ""}`;
        },
        children: (_, k) => {
          ge();
          var y = le();
          j(() => F(y, r().id)), $(_, y);
        },
        $$slots: { default: !0 }
      }), x(p), tt(p, (_) => T(o, _), () => h(o)), j(
        (_) => {
          w = ot(p, 1, `marker marker--${r().status ?? ""}`, "svelte-4f1edl", w, _), G(p, "id", `marker-${r().id ?? ""}`);
        },
        [
          () => ({ "marker--pulse": h(n) })
        ]
      ), $(d, p);
    };
    $e(l, (d) => {
      r() && d(f);
    });
  }
  return $(e, m), _e({
    get comment() {
      return r();
    },
    set comment(d) {
      r(d), E();
    }
  });
}
ae(Un, { comment: {} }, [], [], !0);
const qe = J({ comments: [] }), Ga = "loop", ut = document.querySelector("kirby-loop"), Qa = (ut == null ? void 0 : ut.getAttribute("csrf-token")) || "", es = (ut == null ? void 0 : ut.getAttribute("apibase")) || "/", ht = {
  "Content-Type": "application/json",
  "X-CSRF-Token": Qa || ""
}, mt = (e) => {
  const t = new URL(`${es}/${Ga}/${e}`, window.location.origin), r = new URLSearchParams(window.location.search), n = r.get("token") || r.get("_token");
  return n && t.searchParams.set(r.has("token") ? "token" : "_token", n), t.toString();
}, ts = async (e) => {
  const t = mt(`comments/${e}`), n = await (await fetch(t, { headers: ht })).json();
  return n.status === "ok" && (qe.comments = n.comments), n.status === "ok";
}, rs = async (e) => {
  const t = mt("comment/new"), n = await (await fetch(t, {
    method: "POST",
    headers: ht,
    body: JSON.stringify(e)
  })).json();
  n.status === "ok" && (qe.comments = [n.comment, ...qe.comments]);
}, ns = async (e) => {
  const t = mt("comment/resolve"), n = await (await fetch(t, {
    method: "POST",
    headers: ht,
    body: JSON.stringify({ id: e.id })
  })).json();
  if (n.success) {
    const o = qe.comments.findIndex((a) => a.id === e.id);
    o !== -1 && (qe.comments[o].status = "RESOLVED");
  }
  return n.success;
}, os = async (e) => {
  const t = mt("comment/unresolve"), n = await (await fetch(t, {
    method: "POST",
    headers: ht,
    body: JSON.stringify({ id: e.id })
  })).json();
  if (n.success) {
    const o = qe.comments.findIndex((a) => a.id === e.id);
    o !== -1 && (qe.comments[o].status = "OPEN");
  }
  return n.success;
}, as = async (e) => await (await fetch(mt("guest/name"), {
  method: "POST",
  headers: ht,
  body: JSON.stringify({ name: e })
})).json(), ss = async (e) => {
  const t = mt("comment/reply"), n = await (await fetch(t, {
    method: "POST",
    headers: ht,
    body: JSON.stringify(e)
  })).json();
  if (n.status === "ok") {
    const o = qe.comments.find((a) => a.id === n.reply.parentId);
    o && (o.replies = [...o.replies, n.reply]);
  }
}, ct = J({ text: "", parentId: null }), cr = () => {
  ct.text = "", ct.parentId = null;
};
function is(e) {
  var t;
  if ((e.metaKey || e.ctrlKey) && e.key === "Enter") {
    e.preventDefault();
    const r = (t = e.currentTarget) == null ? void 0 : t.closest("form");
    r && r.requestSubmit();
  }
}
var ls = /* @__PURE__ */ W('<form method="POST" class="svelte-w3h34c"><div class="input"><textarea name="comment" required class="svelte-w3h34c"></textarea></div> <div class="keyboard-hint svelte-w3h34c"> </div> <footer class="svelte-w3h34c"><!> <!></footer></form>');
const us = {
  hash: "svelte-w3h34c",
  code: "form.svelte-w3h34c{cursor:auto;background-color:var(--comment-form-background);color:var(--comment-form-color);border-radius:var(--comment-form-border-radius);border:var(--comment-form-border);padding:0;overflow:hidden}textarea.svelte-w3h34c{width:100%;height:var(--comment-form-textarea-height);resize:none;padding:var(--comment-form-textarea-padding);box-sizing:border-box;background-color:var(--comment-form-textarea-background);font-family:var(--comment-form-textarea-font-family);font-size:var(--comment-form-textarea-font-size);color:currentColor;border:0;margin:0}textarea.svelte-w3h34c:focus-visible,textarea.svelte-w3h34c:focus{outline:0}footer.svelte-w3h34c{padding:var(--comment-form-footer-padding);gap:var(--comment-form-footer-gap);display:flex}footer.svelte-w3h34c button{flex:1}.keyboard-hint.svelte-w3h34c{font-size:var(--comment-form-hint-font-size);color:var(--comment-form-hint-color);padding:var(--comment-form-hint-padding);white-space:nowrap;align-self:center;margin-left:auto}"
};
function Ar(e, t) {
  we(t, !0), se(e, us);
  const r = q(t, "handleSubmit", 7), n = q(t, "cancel", 7), o = q(t, "parentId", 7, null);
  ct.parentId = o() ? Number(o()) : null;
  var a = ls(), s = C(a), i = C(s);
  ua(i), i.__keydown = [is], x(s);
  var u = V(s, 2), c = C(u, !0);
  x(u);
  var v = V(u, 2), m = C(v);
  te(m, {
    type: "submit",
    style: "button--solid",
    children: (f, d) => {
      ge();
      var p = le();
      j((w) => F(p, w), [
        () => o() ? O("ui.reply.submit", "Reply") : O("ui.comment.submit", "Submit")
      ]), $(f, p);
    },
    $$slots: { default: !0 }
  });
  var l = V(m, 2);
  return te(l, {
    get onclick() {
      return n();
    },
    children: (f, d) => {
      ge();
      var p = le();
      j((w) => F(p, w), [() => O("ui.comment.cancel", "Cancel")]), $(f, p);
    },
    $$slots: { default: !0 }
  }), x(v), x(a), j(
    (f, d) => {
      G(i, "placeholder", f), F(c, d);
    },
    [
      () => o() ? O("ui.reply.placeholder", "Write a reply...") : O("ui.comment.placeholder", "Enter your comment..."),
      () => O("ui.comment.keyboardHint", "⌘+Enter or Ctrl+Enter to submit")
    ]
  ), Ze("submit", a, function(...f) {
    var d;
    (d = r()) == null || d.apply(this, f);
  }), Hn(i, () => ct.text, (f) => ct.text = f), $(e, a), _e({
    get handleSubmit() {
      return r();
    },
    set handleSubmit(f) {
      r(f), E();
    },
    get cancel() {
      return n();
    },
    set cancel(f) {
      n(f), E();
    },
    get parentId() {
      return o();
    },
    set parentId(f = null) {
      o(f), E();
    }
  });
}
On(["keydown"]);
ae(Ar, { handleSubmit: {}, cancel: {}, parentId: {} }, [], [], !0);
var cs = /* @__PURE__ */ W('<div class="author svelte-1xlsy1x"> </div>');
const ds = {
  hash: "svelte-1xlsy1x",
  code: ".author.svelte-1xlsy1x{font-size:var(--author-avatar-font-size);text-transform:uppercase;color:var(--author-avatar-color);background-color:var(--author-avatar-background-color);aspect-ratio:1;flex:0 0 var(--author-avatar-size);border-radius:var(--author-avatar-border-radius);justify-content:center;align-items:center;display:flex}"
};
function Zn(e, t) {
  we(t, !0), se(e, ds);
  const r = q(t, "initials", 7);
  var n = cs(), o = C(n, !0);
  return x(n), j(() => F(o, r())), $(e, n), _e({
    get initials() {
      return r();
    },
    set initials(a) {
      r(a), E();
    }
  });
}
ae(Zn, { initials: {} }, [], [], !0);
function Wt(e, t = !0) {
  const r = new Date(e * 1e3), o = (/* @__PURE__ */ new Date()).getTime() - r.getTime(), a = Math.floor(o / (1e3 * 60)), s = Math.floor(o / (1e3 * 60 * 60)), i = Math.floor(o / (1e3 * 60 * 60 * 24));
  return t && i <= 3 ? a < 1 ? O("ui.time.just_now", "just now") : a === 1 ? O("ui.time.minute_ago", "a minute ago") : a < 60 ? ur("ui.time.minutes_ago", "{count} minutes ago", { count: a.toString() }) : s === 1 ? O("ui.time.hour_ago", "an hour ago") : s < 24 ? ur("ui.time.hours_ago", "{count} hours ago", { count: s.toString() }) : i === 1 ? O("ui.time.yesterday", "yesterday") : ur("ui.time.days_ago", "{count} days ago", { count: i.toString() }) : r.toLocaleString(void 0, { dateStyle: "short", timeStyle: "short" });
}
function Jn(e) {
  return new Date(e * 1e3).toISOString();
}
function Xt(e) {
  const t = {
    "&amp;": "&",
    "&lt;": "<",
    "&gt;": ">",
    "&quot;": '"',
    "&#x27;": "'",
    "&#x2F;": "/",
    "&#x60;": "`",
    "&#x3D;": "="
  };
  return e.replace(/&[#\w]+;/g, (r) => t[r] || r);
}
var fs = /* @__PURE__ */ W('<article class="reply svelte-1nsulj7"><!> <div class="reply__content svelte-1nsulj7"><header class="svelte-1nsulj7"><strong> </strong> <time class="svelte-1nsulj7"> </time></header> <div class="reply__text svelte-1nsulj7"> </div></div></article>');
const vs = {
  hash: "svelte-1nsulj7",
  code: ".reply.svelte-1nsulj7{gap:var(--reply-gap);flex-direction:row;align-items:start;display:flex}.reply__content.svelte-1nsulj7{padding:var(--reply-content-padding);background-color:var(--reply-content-background);border-radius:var(--reply-content-border-radius)}.reply__content.svelte-1nsulj7 header:where(.svelte-1nsulj7){gap:var(--reply-header-gap);margin-bottom:var(--reply-header-margin-bottom);justify-content:flex-start;align-items:center;display:flex}.reply__content.svelte-1nsulj7 header:where(.svelte-1nsulj7) time:where(.svelte-1nsulj7){font-size:var(--reply-timestamp-font-size);color:var(--reply-timestamp-color)}@media (prefers-color-scheme:dark){.reply__content.svelte-1nsulj7{background-color:var(--reply-content-background-dark)}}.reply__text.svelte-1nsulj7{white-space:pre-line}"
};
function Kn(e, t) {
  we(t, !1), se(e, vs);
  let r = q(t, "reply", 12);
  Fn();
  var n = fs(), o = C(n);
  const a = /* @__PURE__ */ me(() => r().author.substring(0, 1));
  Zn(o, {
    get initials() {
      return h(a);
    }
  });
  var s = V(o, 2), i = C(s), u = C(i), c = C(u, !0);
  x(u);
  var v = V(u, 2), m = C(v, !0);
  x(v), x(i);
  var l = V(i, 2), f = C(l, !0);
  return x(l), x(s), x(n), j(
    (d, p, w, I, _, k) => {
      G(n, "data-id", r().id), G(n, "aria-label", `${d ?? ""} ${r().author ?? ""}: ${p ?? ""}`), F(c, r().author), G(v, "datetime", w), G(v, "title", I), F(m, _), F(f, k);
    },
    [
      () => O("ui.reply.aria.label", "Reply by"),
      () => Xt(r().comment),
      () => Jn(r().timestamp),
      () => Wt(r().timestamp, !1),
      () => Wt(r().timestamp),
      () => Xt(r().comment)
    ],
    me
  ), $(e, n), _e({
    get reply() {
      return r();
    },
    set reply(d) {
      r(d), E();
    }
  });
}
ae(Kn, { reply: {} }, [], [], !0);
var hs = /* @__PURE__ */ W("<li><!></li>"), ms = /* @__PURE__ */ W('<ul class="comment__replies svelte-6fqqrp"></ul>'), gs = /* @__PURE__ */ W("<!> <!>", 1), ps = /* @__PURE__ */ W('<div class="buttons svelte-6fqqrp"><!></div>'), bs = /* @__PURE__ */ W('<details><summary class="comment__header svelte-6fqqrp"><!> <div class="comment__content svelte-6fqqrp"><header class="svelte-6fqqrp"><strong> </strong> <time class="svelte-6fqqrp"> </time></header> <div class="comment__text svelte-6fqqrp"> </div></div> <!></summary> <!> <footer class="svelte-6fqqrp"><!></footer></details>');
const ws = {
  hash: "svelte-6fqqrp",
  code: '.comment.svelte-6fqqrp{--loop-marker-background:var(--comment-marker-background);--loop-marker-color:var(--comment-marker-color);--marker-size:var(--comment-avatar-size);position:relative}.comment.svelte-6fqqrp>:where(.svelte-6fqqrp){z-index:1;position:relative}.comment.svelte-6fqqrp:after{content:"";left:var(--comment-line-offset);width:var(--comment-line-width);background-color:var(--comment-line-background);z-index:0;height:calc(100% - 4rem);position:absolute;top:1.5rem}.comment.svelte-6fqqrp:not([open]):after{height:calc(100% - 2.75rem)}.comment__header.svelte-6fqqrp{font-size:var(--comment-header-font-size);padding:var(--comment-header-padding);align-items:flex-start;gap:var(--comment-header-gap);cursor:pointer;border-radius:var(--comment-header-border-radius);display:flex}.comment__header.svelte-6fqqrp:focus-visible{outline:2px solid var(--comment-header-outline-color);outline-offset:var(--comment-header-outline-offset)}.comment__header.svelte-6fqqrp .comment__replies-count{bottom:0;left:var(--space-s);min-width:var(--comment-avatar-size);position:absolute}.comment__header.svelte-6fqqrp header:where(.svelte-6fqqrp){gap:var(--comment-author-gap);margin-bottom:var(--comment-author-margin-bottom);justify-content:flex-start;align-items:center;display:flex}.comment__header.svelte-6fqqrp header:where(.svelte-6fqqrp) time:where(.svelte-6fqqrp){font-size:var(--comment-timestamp-font-size);color:var(--comment-timestamp-color)}.comment__header.svelte-6fqqrp .comment__content:where(.svelte-6fqqrp){padding:var(--comment-content-padding);background-color:var(--comment-content-background);border-radius:var(--comment-content-border-radius);flex:1}@media (prefers-color-scheme:dark){.comment__header.svelte-6fqqrp .comment__content:where(.svelte-6fqqrp){background-color:var(--comment-content-background-dark)}}.comment__header.svelte-6fqqrp .comment__text:where(.svelte-6fqqrp){white-space:pre-line}.comment__replies.svelte-6fqqrp{padding:var(--comment-replies-padding);gap:var(--comment-replies-gap);flex-direction:column;margin:0;list-style:none;display:flex}footer.svelte-6fqqrp{gap:var(--comment-footer-gap);padding:var(--comment-footer-padding);flex-direction:column;display:flex}footer.svelte-6fqqrp .buttons:where(.svelte-6fqqrp){gap:var(--comment-buttons-gap);align-items:flex-end;display:flex}.is-hidden.svelte-6fqqrp{display:none}'
};
function Gn(e, t) {
  var z;
  we(t, !0), se(e, ws);
  const r = q(t, "comment", 7), n = q(t, "scrollIntoView", 7), o = q(t, "handleSubmit", 7), a = q(t, "cancel", 7);
  let s = ue(!1), i = ue(J(((z = r().replies) == null ? void 0 : z.length) > 0 && !Z.showResolvedOnly));
  var u = bs();
  let c;
  var v = C(u), m = C(v);
  const l = /* @__PURE__ */ ee(() => r().status), f = /* @__PURE__ */ ee(() => Z.currentCommentId === r().id ? "button--marker-highlighted" : ""), d = /* @__PURE__ */ ee(() => `${O("ui.comment.maker.aria.label", "Jump to marker")} ${r().id}`);
  te(m, {
    get style() {
      return `button--marker button--marker-${h(l) ?? ""} ${h(f) ?? ""}`;
    },
    onclick: () => n()(r().id),
    onmouseenter: () => Z.pulseMarkerId = r().id,
    onmouseout: () => Z.pulseMarkerId = 0,
    get ariaLabel() {
      return h(d);
    },
    children: (S, D) => {
      ge();
      var X = le();
      j(() => F(X, r().id)), $(S, X);
    },
    $$slots: { default: !0 }
  });
  var p = V(m, 2), w = C(p), I = C(w), _ = C(I, !0);
  x(I);
  var k = V(I, 2), y = C(k, !0);
  x(k), x(w);
  var g = V(w, 2), R = C(g, !0);
  x(g), x(p);
  var M = V(p, 2);
  {
    var b = (S) => {
      const D = /* @__PURE__ */ ee(() => `${O("ui.comment.replies.aria.label", "Show replies")} ${r().id}`);
      te(S, {
        style: "button--solid button--small comment__replies-count",
        get ariaLabel() {
          return h(D);
        },
        onclick: () => {
          T(i, !h(i));
        },
        children: (X, ce) => {
          ge();
          var ye = le();
          j(() => {
            var ne;
            return F(ye, ((ne = r().replies) == null ? void 0 : ne.length) > 0 ? `+${r().replies.length}` : "+");
          }), $(X, ye);
        },
        $$slots: { default: !0 }
      });
    };
    $e(M, (S) => {
      h(i) || S(b);
    });
  }
  x(v);
  var P = V(v, 2);
  {
    var N = (S) => {
      var D = ms();
      Tr(D, 21, () => r().replies, (X) => X.id, (X, ce) => {
        var ye = hs(), ne = C(ye);
        Kn(ne, {
          get reply() {
            return h(ce);
          }
        }), x(ye), $(X, ye);
      }), x(D), $(S, D);
    };
    $e(P, (S) => {
      var D;
      ((D = r().replies) == null ? void 0 : D.length) > 0 && S(N);
    });
  }
  var L = V(P, 2), K = C(L);
  {
    var Q = (S) => {
      Ar(S, {
        handleSubmit: (D) => {
          T(s, !1), o()(D);
        },
        cancel: () => {
          T(s, !1), a()();
        },
        get parentId() {
          return r().id;
        }
      });
    }, re = (S) => {
      var D = ps(), X = C(D);
      {
        var ce = (ne) => {
          var He = gs(), nr = Oe(He);
          te(nr, {
            style: "button--solid",
            onclick: () => T(s, !0),
            children: (gt, so) => {
              ge();
              var pt = le();
              j((or) => F(pt, or), [() => O("ui.reply.submit", "Reply")]), $(gt, pt);
            },
            $$slots: { default: !0 }
          });
          var It = V(nr, 2);
          te(It, {
            onclick: () => ns(r()),
            children: (gt, so) => {
              ge();
              var pt = le();
              j((or) => F(pt, or), [
                () => O("ui.comment.mark.solved", "Resolve")
              ]), $(gt, pt);
            },
            $$slots: { default: !0 }
          }), $(ne, He);
        }, ye = (ne) => {
          te(ne, {
            onclick: () => os(r()),
            children: (He, nr) => {
              ge();
              var It = le();
              j((gt) => F(It, gt), [
                () => O("ui.comment.mark.unsolved", "Reopen")
              ]), $(He, It);
            },
            $$slots: { default: !0 }
          });
        };
        $e(X, (ne) => {
          r().status === "OPEN" ? ne(ce) : ne(ye, !1);
        });
      }
      x(D), $(S, D);
    };
    $e(K, (S) => {
      h(s) ? S(Q) : S(re, !1);
    });
  }
  return x(L), x(u), j(
    (S, D, X, ce, ye, ne, He) => {
      G(u, "id", `comment-${r().id ?? ""}`), c = ot(u, 1, `comment comment--${r().status ?? ""}`, "svelte-6fqqrp", c, S), G(v, "aria-label", `${D ?? ""} ${r().author ?? ""}: ${X ?? ""}`), F(_, r().author), G(k, "datetime", ce), G(k, "title", ye), F(y, ne), F(R, He);
    },
    [
      () => ({
        "comment--current": Z.currentCommentId === r().id
      }),
      () => O("ui.comment.summary.aria.label", "Comment by"),
      () => Xt(r().comment),
      () => Jn(r().timestamp),
      () => Wt(r().timestamp, !1),
      () => Wt(r().timestamp),
      () => Xt(r().comment)
    ]
  ), za("open", "toggle", u, (S) => T(i, S), () => h(i)), $(e, u), _e({
    get comment() {
      return r();
    },
    set comment(S) {
      r(S), E();
    },
    get scrollIntoView() {
      return n();
    },
    set scrollIntoView(S) {
      n(S), E();
    },
    get handleSubmit() {
      return o();
    },
    set handleSubmit(S) {
      o(S), E();
    },
    get cancel() {
      return a();
    },
    set cancel(S) {
      a(S), E();
    }
  });
}
ae(
  Gn,
  {
    comment: {},
    scrollIntoView: {},
    handleSubmit: {},
    cancel: {}
  },
  [],
  [],
  !0
);
var _s = /* @__PURE__ */ qt('<svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" class="svelte-1q9atlb"><path d="M5.44067 14.4885C6.54524 14.4885 7.44067 13.5931 7.44067 12.4885C7.44067 11.384 6.54524 10.4885 5.44067 10.4885C4.3361 10.4885 3.44067 11.384 3.44067 12.4885C3.44067 13.5931 4.3361 14.4885 5.44067 14.4885Z" stroke="currentColor" stroke-width="1.5"></path><path d="M12.4407 14.4885C13.5452 14.4885 14.4407 13.5931 14.4407 12.4885C14.4407 11.384 13.5452 10.4885 12.4407 10.4885C11.3361 10.4885 10.4407 11.384 10.4407 12.4885C10.4407 13.5931 11.3361 14.4885 12.4407 14.4885Z" stroke="currentColor" stroke-width="1.5"></path><path d="M19.4407 14.4885C20.5452 14.4885 21.4407 13.5931 21.4407 12.4885C21.4407 11.384 20.5452 10.4885 19.4407 10.4885C18.3361 10.4885 17.4407 11.384 17.4407 12.4885C17.4407 13.5931 18.3361 14.4885 19.4407 14.4885Z" stroke="currentColor" stroke-width="1.5"></path></svg>');
const ys = {
  hash: "svelte-1q9atlb",
  code: "svg.svelte-1q9atlb{width:var(--size,var(--icon-size));height:var(--size,var(--icon-size))}"
};
function ks(e) {
  se(e, ys);
  var t = _s();
  $(e, t);
}
ae(ks, {}, [], [], !0);
var xs = /* @__PURE__ */ qt('<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" class="svelte-1q9atlb"><path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="1.5"></path><path d="M13.7654 2.15224C13.3978 2 12.9319 2 12 2C11.0681 2 10.6022 2 10.2346 2.15224C9.74457 2.35523 9.35522 2.74458 9.15223 3.23463C9.05957 3.45834 9.0233 3.7185 9.00911 4.09799C8.98826 4.65568 8.70226 5.17189 8.21894 5.45093C7.73564 5.72996 7.14559 5.71954 6.65219 5.45876C6.31645 5.2813 6.07301 5.18262 5.83294 5.15102C5.30704 5.08178 4.77518 5.22429 4.35436 5.5472C4.03874 5.78938 3.80577 6.1929 3.33983 6.99993C2.87389 7.80697 2.64092 8.21048 2.58899 8.60491C2.51976 9.1308 2.66227 9.66266 2.98518 10.0835C3.13256 10.2756 3.3397 10.437 3.66119 10.639C4.1338 10.936 4.43789 11.4419 4.43786 12C4.43783 12.5581 4.13375 13.0639 3.66118 13.3608C3.33965 13.5629 3.13248 13.7244 2.98508 13.9165C2.66217 14.3373 2.51966 14.8691 2.5889 15.395C2.64082 15.7894 2.87379 16.193 3.33973 17C3.80568 17.807 4.03865 18.2106 4.35426 18.4527C4.77508 18.7756 5.30694 18.9181 5.83284 18.8489C6.07289 18.8173 6.31632 18.7186 6.65204 18.5412C7.14547 18.2804 7.73556 18.27 8.2189 18.549C8.70224 18.8281 8.98826 19.3443 9.00911 19.9021C9.02331 20.2815 9.05957 20.5417 9.15223 20.7654C9.35522 21.2554 9.74457 21.6448 10.2346 21.8478C10.6022 22 11.0681 22 12 22C12.9319 22 13.3978 22 13.7654 21.8478C14.2554 21.6448 14.6448 21.2554 14.8477 20.7654C14.9404 20.5417 14.9767 20.2815 14.9909 19.902C15.0117 19.3443 15.2977 18.8281 15.781 18.549C16.2643 18.2699 16.8544 18.2804 17.3479 18.5412C17.6836 18.7186 17.927 18.8172 18.167 18.8488C18.6929 18.9181 19.2248 18.7756 19.6456 18.4527C19.9612 18.2105 20.1942 17.807 20.6601 16.9999C21.1261 16.1929 21.3591 15.7894 21.411 15.395C21.4802 14.8691 21.3377 14.3372 21.0148 13.9164C20.8674 13.7243 20.6602 13.5628 20.3387 13.3608C19.8662 13.0639 19.5621 12.558 19.5621 11.9999C19.5621 11.4418 19.8662 10.9361 20.3387 10.6392C20.6603 10.4371 20.8675 10.2757 21.0149 10.0835C21.3378 9.66273 21.4803 9.13087 21.4111 8.60497C21.3592 8.21055 21.1262 7.80703 20.6602 7C20.1943 6.19297 19.9613 5.78945 19.6457 5.54727C19.2249 5.22436 18.693 5.08185 18.1671 5.15109C17.9271 5.18269 17.6837 5.28136 17.3479 5.4588C16.8545 5.71959 16.2644 5.73002 15.7811 5.45096C15.2977 5.17191 15.0117 4.65566 14.9909 4.09794C14.9767 3.71848 14.9404 3.45833 14.8477 3.23463C14.6448 2.74458 14.2554 2.35523 13.7654 2.15224Z" stroke="currentColor" stroke-width="1.5"></path></svg>');
const $s = {
  hash: "svelte-1q9atlb",
  code: "svg.svelte-1q9atlb{width:var(--size,var(--icon-size));height:var(--size,var(--icon-size))}"
};
function Qn(e) {
  se(e, $s);
  var t = xs();
  $(e, t);
}
ae(Qn, {}, [], [], !0);
var it = Sa(() => Z), Cs = /* @__PURE__ */ W('<span class="filter-dot filter-dot--open svelte-15wfo7b" slot="icon" aria-hidden="true"></span>'), zs = /* @__PURE__ */ W('<span class="filter-dot filter-dot--resolved svelte-15wfo7b" slot="icon" aria-hidden="true"></span>'), Ss = /* @__PURE__ */ W('<div class="context-menu-container svelte-15wfo7b"><div class="context-menu-trigger svelte-15wfo7b"><!></div> <div class="context-menu svelte-15wfo7b" popover="auto" role="menu" aria-labelledby="context-menu-trigger" id="context-menu"><div class="menu-section svelte-15wfo7b"><div class="menu-section-title svelte-15wfo7b"> </div> <div class="filter-options svelte-15wfo7b"><!> <!></div></div></div></div>');
const Es = {
  hash: "svelte-15wfo7b",
  code: ".context-menu-container.svelte-15wfo7b{bottom:var(--context-menu-container-bottom);right:var(--context-menu-container-right);z-index:var(--context-menu-container-z-index);position:absolute}.context-menu-trigger.svelte-15wfo7b{width:var(--context-menu-trigger-size);height:var(--context-menu-trigger-size);border-radius:var(--context-menu-trigger-border-radius);justify-content:center;align-items:center;display:flex}.context-menu.svelte-15wfo7b{background:var(--context-menu-background);border-radius:var(--context-menu-border-radius);box-shadow:var(--context-menu-shadow);padding:var(--context-menu-padding);min-width:var(--context-menu-min-width);border:0;margin:0;position:fixed}.context-menu.svelte-15wfo7b::backdrop{background:var(--context-menu-backdrop-background)}.menu-section.svelte-15wfo7b{gap:var(--context-menu-section-gap);flex-direction:column;display:flex}.menu-section-title.svelte-15wfo7b{font-size:var(--context-menu-title-font-size);font-weight:var(--context-menu-title-font-weight);color:var(--context-menu-title-color);margin-bottom:var(--context-menu-title-margin-bottom);text-transform:uppercase;letter-spacing:var(--context-menu-title-letter-spacing);padding:0}.filter-options.svelte-15wfo7b{gap:var(--context-menu-filter-gap);flex-direction:column;display:flex}.filter-dot.svelte-15wfo7b{width:var(--context-menu-filter-dot-size);height:var(--context-menu-filter-dot-size);border-radius:var(--context-menu-filter-dot-border-radius);margin-right:var(--context-menu-filter-dot-margin-right);display:inline-block}.filter-dot--open.svelte-15wfo7b{background:var(--context-menu-filter-dot-open-background)}.filter-dot--resolved.svelte-15wfo7b{background:var(--context-menu-filter-dot-resolved-background)}"
};
function eo(e, t) {
  we(t, !1), se(e, Es);
  let r = Vr(), n = Vr();
  const o = () => {
    h(r).matches(":popover-open") ? h(r).hidePopover() : (h(r).showPopover(), a());
  }, a = () => {
    if (!h(n) || !h(r)) return;
    const b = h(n).getBoundingClientRect(), P = h(r).getBoundingClientRect(), N = b.top - P.height - 8, L = b.left - P.width + b.width;
    Mt(r, h(r).style.position = "fixed"), Mt(r, h(r).style.top = `${Math.max(8, N)}px`), Mt(r, h(r).style.left = `${Math.max(8, L)}px`), Mt(r, h(r).style.margin = "0");
  }, s = () => {
    h(r).hidePopover();
  }, i = (b) => {
    it(it().showResolvedOnly = b), s();
  };
  Fn();
  var u = Ss(), c = C(u), v = C(c);
  const m = /* @__PURE__ */ me(() => O("ui.panel.menu.open", "Open menu")), l = /* @__PURE__ */ me(() => {
    var b;
    return (b = h(r)) != null && b.matches(":popover-open") ? "true" : "false";
  });
  te(v, {
    onclick: o,
    get ariaLabel() {
      return h(m);
    },
    style: "button--icon",
    id: "context-menu-trigger",
    ariaHaspopup: "menu",
    get ariaExpanded() {
      return h(l);
    },
    ariaControls: "context-menu",
    $$slots: {
      icon: (b, P) => {
        Qn(b);
      }
    }
  }), x(c), tt(c, (b) => T(n, b), () => h(n));
  var f = V(c, 2), d = C(f), p = C(d), w = C(p, !0);
  x(p);
  var I = V(p, 2), _ = C(I);
  const k = /* @__PURE__ */ me(() => it().showResolvedOnly ? "" : "button--menu-item-active"), y = /* @__PURE__ */ me(() => it().showResolvedOnly ? O("ui.panel.filter.open.inactive", "Show open comments") : O("ui.panel.filter.open.active", "Show open comments (currently selected)"));
  te(_, {
    get style() {
      return `button--menu-item ${h(k) ?? ""}`;
    },
    onclick: () => i(!1),
    get ariaLabel() {
      return h(y);
    },
    children: (b, P) => {
      ge();
      var N = le();
      j((L) => F(N, L), [() => O("ui.panel.filter.open", "Open")], me), $(b, N);
    },
    $$slots: {
      default: !0,
      icon: (b, P) => {
        var N = Cs();
        $(b, N);
      }
    }
  });
  var g = V(_, 2);
  const R = /* @__PURE__ */ me(() => it().showResolvedOnly ? "button--menu-item-active" : ""), M = /* @__PURE__ */ me(() => it().showResolvedOnly ? O("ui.panel.filter.resolved.active", "Show resolved comments (currently selected)") : O("ui.panel.filter.resolved.inactive", "Show resolved comments"));
  te(g, {
    get style() {
      return `button--menu-item ${h(R) ?? ""}`;
    },
    onclick: () => i(!0),
    get ariaLabel() {
      return h(M);
    },
    children: (b, P) => {
      ge();
      var N = le();
      j(
        (L) => F(N, L),
        [
          () => O("ui.panel.filter.resolved", "Resolved")
        ],
        me
      ), $(b, N);
    },
    $$slots: {
      default: !0,
      icon: (b, P) => {
        var N = zs();
        $(b, N);
      }
    }
  }), x(I), x(d), x(f), tt(f, (b) => T(r, b), () => h(r)), x(u), j(
    (b) => F(w, b),
    [
      () => O("ui.panel.menu.filter.title", "Show Comments")
    ],
    me
  ), $(e, u), _e();
}
ae(eo, {}, [], [], !0);
var qs = /* @__PURE__ */ qt('<svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" class="svelte-1q9atlb"><path d="M12.5 22.5C18.0228 22.5 22.5 18.0228 22.5 12.5C22.5 6.97715 18.0228 2.5 12.5 2.5C6.97715 2.5 2.5 6.97715 2.5 12.5C2.5 14.0997 2.87562 15.6116 3.54346 16.9525C3.72094 17.3088 3.78001 17.7161 3.67712 18.1006L3.08151 20.3267C2.82295 21.293 3.70701 22.177 4.67335 21.9185L6.89939 21.3229C7.28393 21.22 7.69121 21.2791 8.04753 21.4565C9.38837 22.1244 10.9003 22.5 12.5 22.5Z" stroke="currentColor" stroke-width="1.5"></path><path d="M8.5 11H16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M8.5 14.5H14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg>');
const Is = {
  hash: "svelte-1q9atlb",
  code: "svg.svelte-1q9atlb{width:var(--size,var(--icon-size));height:var(--size,var(--icon-size))}"
};
function to(e) {
  se(e, Is);
  var t = qs();
  $(e, t);
}
ae(to, {}, [], [], !0);
var Ms = /* @__PURE__ */ W('<li class="no-threads svelte-1nwyuw"><p> </p></li>'), Ps = /* @__PURE__ */ W('<li class="no-threads svelte-1nwyuw"><p> </p></li>'), Ns = /* @__PURE__ */ W('<li class="svelte-1nwyuw"><!></li>'), Rs = /* @__PURE__ */ W('<dialog><header class="svelte-1nwyuw"><!></header> <ul class="threads svelte-1nwyuw" data-lenis-prevent=""><!></ul> <!></dialog>');
const Ts = {
  hash: "svelte-1nwyuw",
  code: ".panel.svelte-1nwyuw{position:var(--panel-position);right:var(--panel-right);top:var(--panel-top);transform:var(--panel-transform-closed);width:var(--panel-width);max-width:none;height:var(--panel-height);color:var(--panel-color);border-radius:var(--panel-border-radius);border-top-left-radius:var(--panel-border-top-left-radius);transition:var(--panel-transition);z-index:var(--panel-z-index);cursor:auto;background:0 0;border:0;flex-direction:column;justify-content:flex-start;align-items:flex-start;margin:0;padding:0;display:flex;bottom:auto;left:auto}@media screen and (max-width:600px){.panel.svelte-1nwyuw{width:var(--panel-mobile-width)}}.panel.open.svelte-1nwyuw{transform:var(--panel-transform-open);box-shadow:var(--panel-shadow)}.panel.open.svelte-1nwyuw header:where(.svelte-1nwyuw){transform:var(--panel-header-transform-open)}.panel.svelte-1nwyuw header:where(.svelte-1nwyuw){transform:var(--panel-header-transform-closed);border-top-left-radius:var(--panel-header-border-radius);border-bottom-left-radius:var(--panel-header-border-radius);gap:var(--panel-header-gap);-webkit-backdrop-filter:var(--panel-header-backdrop-filter);backdrop-filter:var(--panel-header-backdrop-filter);background:var(--panel-header-background);box-shadow:var(--shadow-l),var(--shadow-light-edge),var(--shadow-dark-edge);transition:transform var(--transition-duration)var(--transition-easing);border-top-right-radius:0;border-bottom-right-radius:0;flex-direction:column;display:flex;position:absolute}.panel.svelte-1nwyuw header:where(.svelte-1nwyuw):hover,.panel.svelte-1nwyuw header:where(.svelte-1nwyuw):focus-visible{transform:var(--panel-header-transform-hover)}.threads.svelte-1nwyuw{overscroll-behavior:contain;padding:var(--panel-threads-padding);box-sizing:border-box;background-color:var(--panel-threads-background);width:100%;-webkit-backdrop-filter:var(--panel-threads-backdrop);backdrop-filter:var(--panel-threads-backdrop);z-index:2;border-radius:var(--panel-threads-border-radius);border-top-left-radius:var(--panel-threads-border-top-left-radius);scrollbar-width:var(--panel-threads-scrollbar-width);scrollbar-gutter:stable;flex-direction:column;flex:100%;margin:0;list-style:none;display:flex;overflow-y:auto}.threads.svelte-1nwyuw li:where(.svelte-1nwyuw)+li:where(.svelte-1nwyuw){margin-top:var(--panel-threads-item-margin)}.threads.svelte-1nwyuw .no-threads:where(.svelte-1nwyuw){text-align:center;padding:var(--panel-no-threads-padding);font-size:var(--panel-no-threads-font-size);color:var(--panel-no-threads-color);margin-block:auto}"
};
function ro(e, t) {
  we(t, !0), se(e, Ts);
  const r = q(t, "scrollIntoView", 7), n = q(t, "handleSubmit", 7), o = q(t, "cancel", 7);
  let a;
  const s = /* @__PURE__ */ ee(() => Z.showResolvedOnly ? qe.comments.filter((_) => _.status === "RESOLVED") : qe.comments.filter((_) => _.status === "OPEN"));
  et(() => {
    a && (Z.open && !a.open ? a.show() : !Z.open && a.open && a.close());
  }), rr(() => {
    const _ = (k) => {
      k.key === "Escape" && Z.open && (Z.open = !1);
    };
    return document.addEventListener("keydown", _), () => document.removeEventListener("keydown", _);
  });
  function i() {
    Z.open = !1;
  }
  var u = Rs();
  let c;
  var v = C(u), m = C(v);
  const l = /* @__PURE__ */ ee(() => O("ui.panel.open", "Open comments"));
  te(m, {
    onclick: () => Z.open = !Z.open,
    style: "button--header",
    get ariaLabel() {
      return h(l);
    },
    $$slots: {
      icon: (_, k) => {
        to(_);
      }
    }
  }), x(v);
  var f = V(v, 2), d = C(f);
  {
    var p = (_) => {
      var k = Ms(), y = C(k), g = C(y, !0);
      x(y), x(k), j((R) => F(g, R), [
        () => O("ui.panel.no.comments", "No comments yet.")
      ]), $(_, k);
    }, w = (_, k) => {
      {
        var y = (R) => {
          var M = Ps(), b = C(M), P = C(b, !0);
          x(b), x(M), j((N) => F(P, N), [
            () => O("ui.panel.no.resolved", "No resolved comments yet.")
          ]), $(R, M);
        }, g = (R) => {
          var M = Rr(), b = Oe(M);
          Tr(b, 17, () => h(s), (P) => P.id, (P, N) => {
            var L = Ns(), K = C(L);
            Gn(K, {
              get comment() {
                return h(N);
              },
              get scrollIntoView() {
                return r();
              },
              get cancel() {
                return o();
              },
              get handleSubmit() {
                return n();
              }
            }), x(L), $(P, L);
          }), $(R, M);
        };
        $e(
          _,
          (R) => {
            h(s).length === 0 && Z.showResolvedOnly ? R(y) : R(g, !1);
          },
          k
        );
      }
    };
    $e(d, (_) => {
      h(s).length === 0 ? _(p) : _(w, !1);
    });
  }
  x(f);
  var I = V(f, 2);
  return eo(I, {}), x(u), tt(u, (_) => a = _, () => a), j(
    (_) => {
      c = ot(u, 1, "panel svelte-1nwyuw", null, c, _), f.inert = !Z.open;
    },
    [() => ({ open: Z.open })]
  ), Ze("close", u, i), $(e, u), _e({
    get scrollIntoView() {
      return r();
    },
    set scrollIntoView(_) {
      r(_), E();
    },
    get handleSubmit() {
      return n();
    },
    set handleSubmit(_) {
      n(_), E();
    },
    get cancel() {
      return o();
    },
    set cancel(_) {
      o(_), E();
    }
  });
}
ae(
  ro,
  {
    scrollIntoView: {},
    handleSubmit: {},
    cancel: {}
  },
  [],
  [],
  !0
);
function Os(e) {
  const t = e.target;
  if (!t)
    throw new Error("No target element found in event");
  const r = As();
  for (const n of r)
    try {
      const o = n.generator(t);
      if (o && Hs(o, t))
        return o;
    } catch (o) {
      console.warn(`Strategy ${n.name} failed:`, o);
    }
  return Ls(t);
}
function As() {
  return [
    {
      name: "ID",
      priority: 1,
      generator: (e) => e.id && Lr(e.id) ? `#${CSS.escape(e.id)}` : null
    },
    {
      name: "Unique Attributes",
      priority: 2,
      generator: (e) => {
        const t = ["data-testid", "data-id", "name", "for"];
        for (const r of t) {
          const n = e.getAttribute(r);
          if (n) {
            const o = `${e.tagName.toLowerCase()}[${r}="${CSS.escape(n)}"]`;
            if (Fe(o))
              return o;
          }
        }
        return null;
      }
    },
    {
      name: "Semantic Attributes",
      priority: 3,
      generator: (e) => {
        const t = [
          "aria-label",
          "aria-labelledby",
          "role",
          "type",
          "placeholder",
          "title",
          "alt"
        ], n = [e.tagName.toLowerCase()];
        for (const o of t) {
          const a = e.getAttribute(o);
          a && n.push(`[${o}="${CSS.escape(a)}"]`);
        }
        if (n.length > 1) {
          const o = n.join("");
          if (Fe(o))
            return o;
        }
        return null;
      }
    },
    {
      name: "Structural Attributes",
      priority: 4,
      generator: (e) => {
        const t = ["href", "src", "action", "value"], r = e.tagName.toLowerCase();
        for (const n of t) {
          const o = e.getAttribute(n);
          if (o && o.length > 0) {
            const a = `${r}[${n}="${CSS.escape(o)}"]`;
            if (Fe(a))
              return a;
          }
        }
        return null;
      }
    },
    {
      name: "Class Combinations",
      priority: 5,
      generator: (e) => {
        const t = Ut(e);
        if (t.length === 0)
          return null;
        const r = e.tagName.toLowerCase();
        for (const n of t) {
          const o = `${r}.${CSS.escape(n)}`;
          if (Fe(o))
            return o;
        }
        if (t.length >= 2) {
          const n = t.slice(0, 3).map((a) => `.${CSS.escape(a)}`).join(""), o = `${r}${n}`;
          if (Fe(o))
            return o;
        }
        return null;
      }
    },
    {
      name: "Parent Context",
      priority: 6,
      generator: (e) => {
        const t = e.parentElement;
        if (!t) return null;
        const r = Ds(t);
        if (!r) return null;
        const n = e.tagName.toLowerCase(), o = no(e);
        if (o > 0) {
          const s = `${r} > ${n}:nth-of-type(${o})`;
          if (Fe(s))
            return s;
        }
        const a = Ut(e);
        if (a.length > 0) {
          const s = `${r} > ${n}.${CSS.escape(a[0])}`;
          if (Fe(s))
            return s;
        }
        return null;
      }
    }
  ];
}
function Ut(e) {
  const t = Array.from(e.classList), r = [
    /^(is-|has-|js-)/,
    // State prefixes
    /^(active|disabled|loading|selected|hover|focus)/,
    // State classes
    /^[a-z]+-[0-9]+$/,
    // Generated classes like 'item-123'
    /^(sm-|md-|lg-|xl-)/,
    // Responsive utilities
    /^(m-|p-|w-|h-|text-|bg-)/,
    // Tailwind-like utilities
    /^[a-f0-9]{6,}$/
    // Hash-like classes
  ];
  return t.filter((n) => !r.some((o) => o.test(n)));
}
function Ls(e) {
  const t = [];
  let r = e;
  for (; r && r !== document.body && t.length < 5; ) {
    const n = js(r);
    t.unshift(n);
    const o = t.join(" > ");
    if (Fe(o))
      return o;
    r = r.parentElement;
  }
  return t.join(" > ");
}
function Ds(e) {
  if (e.id && Lr(e.id))
    return `#${CSS.escape(e.id)}`;
  const t = ["data-testid", "data-id", "name"];
  for (const o of t) {
    const a = e.getAttribute(o);
    if (a)
      return `[${o}="${CSS.escape(a)}"]`;
  }
  const r = e.tagName.toLowerCase(), n = Ut(e);
  return n.length > 0 ? `${r}.${CSS.escape(n[0])}` : null;
}
function js(e) {
  const t = e.tagName.toLowerCase();
  if (e.id && Lr(e.id))
    return `#${CSS.escape(e.id)}`;
  const r = Ut(e);
  if (r.length > 0)
    return `${t}.${CSS.escape(r[0])}`;
  const n = no(e);
  return n > 1 ? `${t}:nth-of-type(${n})` : t;
}
function no(e) {
  let t = 1, r = e.previousElementSibling;
  for (; r; )
    r.tagName === e.tagName && t++, r = r.previousElementSibling;
  return t;
}
function Hs(e, t) {
  try {
    const r = document.querySelectorAll(e);
    return r.length === 1 && r[0] === t;
  } catch (r) {
    return console.warn(`Invalid selector: ${e}`, r), !1;
  }
}
function Fe(e) {
  try {
    return document.querySelectorAll(e).length === 1;
  } catch {
    return !1;
  }
}
function Lr(e) {
  return ![
    /^[a-f0-9]{8,}$/,
    // Hex strings
    /^(ember|react|vue)[0-9]+/,
    // Framework generated
    /^[0-9]+$/,
    // Pure numbers
    /^temp-/
    // Temporary prefixes
  ].some((r) => r.test(e));
}
const Fs = (e, t) => {
  const r = e.pageX, n = e.pageY, o = t.getBoundingClientRect(), a = o.left + window.scrollX, s = o.top + window.scrollY, i = r - a, u = n - s;
  let c = i / t.offsetWidth * 100, v = u / t.offsetHeight * 100;
  return c = Number(c.toFixed(2)), v = Number(v.toFixed(2)), {
    selectorOffsetX: c,
    selectorOffsetY: v
  };
}, Vs = (e) => {
  const t = Os(e), r = document.querySelector(t);
  if (!r) return;
  const { selectorOffsetX: n, selectorOffsetY: o } = Fs(e, r);
  let a = e.pageX, s = e.pageY;
  return a = Number(a.toFixed(2)), s = Number(s.toFixed(2)), {
    selector: t,
    selectorOffsetX: n,
    selectorOffsetY: o,
    pagePositionX: a,
    pagePositionY: s
  };
}, Bs = () => {
  const e = document.body, t = document.documentElement;
  return Math.max(
    e.scrollWidth,
    e.offsetWidth,
    t.clientWidth,
    t.scrollWidth,
    t.offsetWidth
  );
}, Ys = (e, t) => {
  let r = 0, n = 0;
  if (!e || !t) return { left: r, top: n };
  r = e.pagePositionX, n = e.pagePositionY;
  const o = t.offsetWidth, a = t.offsetHeight, s = Bs(), i = Xn();
  return r + o > s && (r = s - o), n + a > i && (n = i - a), r = Math.max(0, r), n = Math.max(0, n), { left: r, top: n };
};
var Ws = /* @__PURE__ */ W("<dialog><!></dialog>");
const Xs = {
  hash: "svelte-iihm64",
  code: "dialog.svelte-iihm64{--loop-textarea-font-size:var(--comment-dialog-textarea-font-size);position:var(--comment-dialog-position);top:var(--top);left:var(--left);max-width:var(--comment-dialog-max-width);border-radius:var(--comment-dialog-border-radius);visibility:hidden;width:100%;max-height:none;box-shadow:var(--comment-dialog-shadow);border:0;margin:0;padding:0;overflow:hidden}dialog.is-visible.svelte-iihm64{visibility:visible}dialog.svelte-iihm64::backdrop{background-color:var(--comment-dialog-backdrop-background)}"
};
function oo(e, t) {
  we(t, !0), se(e, Xs);
  const r = q(t, "handleSubmit", 7), n = q(t, "showModal", 7), o = q(t, "newMarker", 7), a = q(t, "cancel", 7);
  let s, i = ue(J({ left: 0, top: 0 })), u = ue(!1);
  et(() => {
    n() ? (s.showModal(), T(i, J(Ys(o(), s))), T(u, !0)) : (s.close(), T(u, !1));
  });
  var c = Ws();
  let v;
  var m = C(c);
  return Ar(m, {
    get handleSubmit() {
      return r();
    },
    get cancel() {
      return a();
    }
  }), x(c), tt(c, (l) => s = l, () => s), j(
    (l) => {
      _a(c, `--left: ${h(i).left ?? ""}px; --top: ${h(i).top ?? ""}px;`), v = ot(c, 1, "svelte-iihm64", null, v, l);
    },
    [() => ({ "is-visible": h(u) })]
  ), Ze("close", c, function(...l) {
    var f;
    (f = a()) == null || f.apply(this, l);
  }), $(e, c), _e({
    get handleSubmit() {
      return r();
    },
    set handleSubmit(l) {
      r(l), E();
    },
    get showModal() {
      return n();
    },
    set showModal(l) {
      n(l), E();
    },
    get newMarker() {
      return o();
    },
    set newMarker(l) {
      o(l), E();
    },
    get cancel() {
      return a();
    },
    set cancel(l) {
      a(l), E();
    }
  });
}
ae(
  oo,
  {
    handleSubmit: {},
    showModal: {},
    newMarker: {},
    cancel: {}
  },
  [],
  [],
  !0
);
var Us = /* @__PURE__ */ W('<h2 class="svelte-1y3js7r"> </h2> <p class="welcome-text svelte-1y3js7r"> </p>', 1), Zs = /* @__PURE__ */ W('<div><div class="input"><input type="text" required class="svelte-1y3js7r"></div></div>'), Js = /* @__PURE__ */ W('<dialog class="svelte-1y3js7r"><form class="svelte-1y3js7r"><!> <!> <footer class="svelte-1y3js7r"><!> <!></footer></form></dialog>');
const Ks = {
  hash: "svelte-1y3js7r",
  code: "dialog.svelte-1y3js7r{-webkit-backdrop-filter:var(--welcome-dialog-backdrop-filter);backdrop-filter:var(--welcome-dialog-backdrop-filter);border:var(--welcome-dialog-border);border-radius:var(--welcome-dialog-border-radius);box-shadow:var(--welcome-dialog-shadow);width:100%;max-width:var(--welcome-dialog-max-width);background:var(--welcome-dialog-background);padding:0}dialog.svelte-1y3js7r::backdrop{background:var(--welcome-dialog-backdrop-background);-webkit-backdrop-filter:var(--welcome-dialog-backdrop-backdrop-filter);backdrop-filter:var(--welcome-dialog-backdrop-backdrop-filter)}form.svelte-1y3js7r{padding:var(--welcome-dialog-form-padding)}h2.svelte-1y3js7r{margin:var(--welcome-dialog-title-margin);font-size:var(--welcome-dialog-title-font-size);color:var(--welcome-dialog-title-color);font-weight:var(--welcome-dialog-title-font-weight)}.welcome-text.svelte-1y3js7r{margin:var(--welcome-dialog-text-margin);font-size:var(--welcome-dialog-text-font-size);color:var(--welcome-dialog-text-color);line-height:var(--welcome-dialog-text-line-height)}.name-section.svelte-1y3js7r{margin-bottom:var(--welcome-dialog-name-section-margin)}.name-section.no-welcome.svelte-1y3js7r{border-top:none;padding-top:0}input.svelte-1y3js7r{border:var(--welcome-dialog-input-border);border-radius:var(--welcome-dialog-input-border-radius);width:100%;padding:var(--welcome-dialog-input-padding);box-sizing:border-box;font-family:var(--welcome-dialog-input-font-family);font-size:var(--welcome-dialog-input-font-size);color:var(--welcome-dialog-input-color);background:var(--welcome-dialog-input-background)}input.svelte-1y3js7r:focus-visible{outline-color:var(--welcome-dialog-input-outline-color);outline-offset:var(--welcome-dialog-input-outline-offset)}footer.svelte-1y3js7r{gap:var(--welcome-dialog-footer-gap);display:flex}footer.svelte-1y3js7r button{flex:1}"
};
function ao(e, t) {
  we(t, !0), se(e, Ks);
  const r = q(t, "headline", 7), n = q(t, "text", 7), o = q(t, "authenticated", 7), a = q(t, "welcomeEnabled", 7, !0), s = q(t, "onDismiss", 7);
  let i, u = ue(""), c = ue(!1);
  const v = () => i == null ? void 0 : i.showModal(), m = () => i == null ? void 0 : i.close();
  async function l(b) {
    var P;
    if (b.preventDefault(), o())
      (P = s()) == null || P(), i == null || i.close();
    else if (h(u).trim() && !h(c)) {
      T(c, !0);
      try {
        await as(h(u).trim()), lt.set(h(u).trim()), i == null || i.close();
      } catch (N) {
        console.error("Failed to set guest name:", N);
      } finally {
        T(c, !1);
      }
    }
  }
  function f() {
    i == null || i.close();
  }
  function d() {
    T(u, ""), T(c, !1);
  }
  var p = Js(), w = C(p), I = C(w);
  {
    var _ = (b) => {
      var P = Us(), N = Oe(P), L = C(N, !0);
      x(N);
      var K = V(N, 2), Q = C(K, !0);
      x(K), j(() => {
        F(L, r()), F(Q, n());
      }), $(b, P);
    };
    $e(I, (b) => {
      a() && b(_);
    });
  }
  var k = V(I, 2);
  {
    var y = (b) => {
      var P = Zs();
      let N;
      var L = C(P), K = C(L);
      xa(K), x(L), x(P), j(
        (Q, re) => {
          N = ot(P, 1, "name-section svelte-1y3js7r", null, N, Q), G(K, "placeholder", re);
        },
        [
          () => ({ "no-welcome": !a() }),
          () => O("ui.welcome.guest.name.placeholder", "Enter your name")
        ]
      ), Hn(K, () => h(u), (Q) => T(u, Q)), $(b, P);
    };
    $e(k, (b) => {
      o() || b(y);
    });
  }
  var g = V(k, 2), R = C(g);
  te(R, {
    type: "submit",
    style: "button--solid",
    get disabled() {
      return h(c);
    },
    children: (b, P) => {
      var N = Rr(), L = Oe(N);
      {
        var K = (re) => {
          var z = le();
          j((S) => F(z, S), [
            () => h(c) ? "Saving..." : O("ui.welcome.continue", "Continue")
          ]), $(re, z);
        }, Q = (re) => {
          var z = le();
          j((S) => F(z, S), [() => O("ui.welcome.continue", "Continue")]), $(re, z);
        };
        $e(L, (re) => {
          o() ? re(Q, !1) : re(K);
        });
      }
      $(b, N);
    },
    $$slots: { default: !0 }
  });
  var M = V(R, 2);
  return te(M, {
    onclick: f,
    get disabled() {
      return h(c);
    },
    children: (b, P) => {
      ge();
      var N = le();
      j((L) => F(N, L), [() => O("ui.welcome.dismiss", "Dismiss")]), $(b, N);
    },
    $$slots: { default: !0 }
  }), x(g), x(w), x(p), tt(p, (b) => i = b, () => i), Ze("close", p, d), Ze("submit", w, l), $(e, p), _e({
    showModal: v,
    close: m,
    get headline() {
      return r();
    },
    set headline(b) {
      r(b), E();
    },
    get text() {
      return n();
    },
    set text(b) {
      n(b), E();
    },
    get authenticated() {
      return o();
    },
    set authenticated(b) {
      o(b), E();
    },
    get welcomeEnabled() {
      return a();
    },
    set welcomeEnabled(b = !0) {
      a(b), E();
    },
    get onDismiss() {
      return s();
    },
    set onDismiss(b) {
      s(b), E();
    }
  });
}
ae(
  ao,
  {
    headline: {},
    text: {},
    authenticated: {},
    welcomeEnabled: {},
    onDismiss: {}
  },
  [],
  ["showModal", "close"],
  !0
);
var Gs = /* @__PURE__ */ W("<!> <!> <!> <!>", 1), Qs = /* @__PURE__ */ W("<!> <!>", 1);
function ei(e, t) {
  we(t, !0);
  const r = q(t, "position", 7), n = q(t, "language", 7), o = q(t, "apibase", 7), a = q(t, "pageId", 7), s = q(t, "authenticated", 7), i = q(t, "welcome-enabled", 7), u = q(t, "welcome-headline", 7), c = q(t, "welcome-text", 7), v = q(t, "translations", 7);
  let m = ue(!1), l = ue(!1), f, d = /* @__PURE__ */ ee(() => s() === "true"), p = /* @__PURE__ */ ee(() => i() === "true");
  const w = /* @__PURE__ */ ee(() => qe.comments.filter((z) => z.status !== "RESOLVED")), I = "loop-welcome-dismissed", _ = () => h(d) ? sessionStorage.getItem(I) === "true" : !1, k = () => {
    h(d) && sessionStorage.setItem(I, "true");
  };
  let y = ue(null);
  const g = (z) => {
    var D;
    const S = (D = t.$$host.shadowRoot) == null ? void 0 : D.getElementById(`marker-${z}`);
    S && S.scrollIntoView({ behavior: "smooth", block: "center" });
  }, R = (z) => {
    var ce;
    const S = z.target, D = S.nodeName === "KIRBY-LOOP" || ((ce = S.parentElement) == null ? void 0 : ce.closest("loop"));
    if (!Je.open || D) return;
    if (!h(d) && !lt.get()) {
      f == null || f.showModal();
      return;
    }
    const X = Vs(z);
    X && (T(y, J(X)), T(l, !0));
  }, M = () => {
    T(l, !1), cr();
  }, b = (z) => {
    if (z.preventDefault(), !h(d) && !lt.get()) {
      f == null || f.showModal();
      return;
    }
    const { text: S, parentId: D } = ct;
    if (D)
      ss({ parentId: D, comment: S }), cr();
    else {
      if (!h(y)) return;
      const X = n() || "", ce = {
        url: window.location.href,
        comment: S,
        parentId: null,
        lang: X,
        pageId: a(),
        ...h(y)
      };
      T(l, !1), rs(ce), cr();
    }
  };
  rr(async () => {
    const z = JSON.parse(v() || "{}");
    Ra(z), T(m, J(await ts(a()))), lt.get(), h(p) && h(m) ? (h(d) && !_() || !h(d) && !lt.get()) && (f == null || f.showModal()) : !h(d) && !lt.get() && h(m) && (f == null || f.showModal());
  }), et(() => {
    Je.open ? document.body.style.setProperty("cursor", `url("data:image/svg+xml,%3Csvg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M12 23C18.0751 23 23 18.0751 23 12C23 5.92487 18.0751 1 12 1C5.92487 1 1 5.92487 1 12C1 18.0751 5.92487 23 12 23Z' stroke='white' stroke-width='0.75'/%3E%3Cpath d='M15 12H12M12 12H9M12 12V9M12 12V15' stroke='white' stroke-width='3' stroke-linecap='round'/%3E%3Cpath d='M15 12H12M12 12H9M12 12V9M12 12V15' stroke='black' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E"), auto`) : document.body.style.removeProperty("cursor"), document.documentElement.classList.toggle("loop-overlay-open", Je.open);
  });
  var P = Qs();
  Ze("click", pn, R);
  var N = Oe(P);
  {
    var L = (z) => {
      var S = Gs(), D = Oe(S);
      Wn(D, {
        get position() {
          return r();
        },
        get commentsCount() {
          return h(w).length;
        }
      });
      var X = V(D, 2);
      ro(X, { scrollIntoView: g, handleSubmit: b, cancel: M });
      var ce = V(X, 2);
      Tr(ce, 17, () => h(w), (ne) => ne.id, (ne, He) => {
        Un(ne, {
          get comment() {
            return h(He);
          }
        });
      });
      var ye = V(ce, 2);
      oo(ye, {
        handleSubmit: b,
        get showModal() {
          return h(l);
        },
        get newMarker() {
          return h(y);
        },
        cancel: M
      }), $(z, S);
    };
    $e(N, (z) => {
      h(m) && z(L);
    });
  }
  var K = V(N, 2);
  const Q = /* @__PURE__ */ ee(() => u() || ""), re = /* @__PURE__ */ ee(() => c() || "");
  return tt(
    ao(K, {
      get headline() {
        return h(Q);
      },
      get text() {
        return h(re);
      },
      get authenticated() {
        return h(d);
      },
      get welcomeEnabled() {
        return h(p);
      },
      onDismiss: k
    }),
    (z) => f = z,
    () => f
  ), $(e, P), _e({
    get position() {
      return r();
    },
    set position(z) {
      r(z), E();
    },
    get language() {
      return n();
    },
    set language(z) {
      n(z), E();
    },
    get apibase() {
      return o();
    },
    set apibase(z) {
      o(z), E();
    },
    get pageId() {
      return a();
    },
    set pageId(z) {
      a(z), E();
    },
    get authenticated() {
      return s();
    },
    set authenticated(z) {
      s(z), E();
    },
    get "welcome-enabled"() {
      return i();
    },
    set "welcome-enabled"(z) {
      i(z), E();
    },
    get "welcome-headline"() {
      return u();
    },
    set "welcome-headline"(z) {
      u(z), E();
    },
    get "welcome-text"() {
      return c();
    },
    set "welcome-text"(z) {
      c(z), E();
    },
    get translations() {
      return v();
    },
    set translations(z) {
      v(z), E();
    }
  });
}
customElements.define("kirby-loop", ae(
  ei,
  {
    position: {},
    language: {},
    apibase: {},
    pageId: {},
    authenticated: {},
    "welcome-enabled": {},
    "welcome-headline": {},
    "welcome-text": {},
    translations: {}
  },
  [],
  [],
  !0
));
export {
  ei as default
};
