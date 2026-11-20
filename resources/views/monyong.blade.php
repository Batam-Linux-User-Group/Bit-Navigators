<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PROTOCOL: MONYONG // SECRET</title>
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=fira-code:400,600,700|inter:400,600,800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
:root{
  --bg:#04060e;
  --green:#50dc64;
  --cyan:#00e5ff;
  --danger:#ff4444;
  --yellow:#f4c430;
  --mono:'Fira Code',monospace;
  --sans:'Inter',sans-serif;
}
body{
  background:var(--bg);
  color:#c8e0c8;
  font-family:var(--sans);
  min-height:100vh;
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  overflow:hidden;
  position:relative;
  cursor:none;
}

/* Custom Cursor */
#cursor{
  position:fixed;width:20px;height:20px;
  border:2px solid var(--green);border-radius:50%;
  pointer-events:none;z-index:9999;
  transform:translate(-50%,-50%);
  mix-blend-mode:screen;
}
#cursor-dot{
  position:fixed;width:5px;height:5px;
  background:var(--green);border-radius:50%;
  pointer-events:none;z-index:9999;
  transform:translate(-50%,-50%);
}

/* Background Vignette & Blur */
.bg-fx{
  position:fixed;inset:0;
  background:
    radial-gradient(circle at center, rgba(80,220,100,0.12) 0%, transparent 60%),
    radial-gradient(circle at 80% 20%, rgba(255,68,68,0.1) 0%, transparent 40%),
    #04060e;
  z-index:0;
}

/* VHS Overlay */
#vhs-overlay{
  position:fixed;inset:0;z-index:9990;
  pointer-events:none;opacity:0;visibility:hidden;
  transition:opacity .15s ease,visibility .15s;
}
#vhs-overlay.active{
  opacity:1;visibility:visible;
  animation:vhsFlicker .25s steps(2) infinite;
}
.vhs-scanlines{
  position:absolute;inset:0;
  background:repeating-linear-gradient(
    0deg,
    rgba(0,0,0,0.85) 0px,
    rgba(0,0,0,0.85) 2px,
    transparent 2px,
    transparent 4px
  );
}
.vhs-tracking{
  position:absolute;left:0;right:0;height:24px;
  background:linear-gradient(180deg,transparent,rgba(0,229,255,.5),rgba(255,68,68,.5),transparent);
  animation:trackAnim .7s infinite;
}
@keyframes trackAnim{
  0%{top:-20px;}100%{top:100vh;}
}
@keyframes vhsFlicker{
  0%{transform:translate(0,0);filter:contrast(1.1);}
  33%{transform:translate(-3px,1px);filter:contrast(1.4) drop-shadow(4px 0 #00e5ff);}
  66%{transform:translate(3px,-1px);filter:contrast(1.2) drop-shadow(-4px 0 #ff4444);}
  100%{transform:translate(0,0);filter:contrast(1.1);}
}

.vhs-hud{
  position:absolute;top:2rem;left:2.5rem;right:2.5rem;
  display:flex;justify-content:space-between;align-items:center;
  font-family:var(--mono);font-size:1.1rem;font-weight:700;
  color:#50dc64;text-shadow:0 0 10px rgba(80,220,100,.8), 2px 0 #ff4444;
}

/* Video Stage */
.stage{
  position:relative;z-index:10;
  display:flex;flex-direction:column;
  align-items:center;
  max-width:1180px;width:95%;
  text-align:center;
  animation:popIn .6s cubic-bezier(.16,1,.3,1) forwards;
  padding:1rem 0;
}
@keyframes popIn{
  from{opacity:0;transform:scale(.9) translateY(20px);}
  to{opacity:1;transform:scale(1) translateY(0);}
}

.alert-badge{
  font-family:var(--mono);font-size:.85rem;
  color:var(--danger);
  border:1px solid var(--danger);
  background:rgba(255,68,68,0.12);
  padding:.4rem 1.2rem;border-radius:4px;
  letter-spacing:.2em;text-transform:uppercase;
  box-shadow:0 0 20px rgba(255,68,68,.3);
  margin-bottom:1rem;
  animation:pulseDanger 1.5s infinite;
}
@keyframes pulseDanger{
  0%,100%{box-shadow:0 0 15px rgba(255,68,68,.25);}
  50%{box-shadow:0 0 35px rgba(255,68,68,.6);}
}

.secret-title{
  font-family:var(--mono);font-size:clamp(2rem,5vw,3.2rem);
  font-weight:800;color:var(--green);
  text-shadow:0 0 30px rgba(80,220,100,.5);
  margin-bottom:.4rem;letter-spacing:.05em;
}
.secret-subtitle{
  font-size:1rem;color:#7a9c7a;margin-bottom:1.4rem;
  font-family:var(--mono);
}

/* CRT Screen Frame - Bigger & Immersive */
.video-frame{
  width:100%;max-width:960px;
  background:#000;
  border:2px solid var(--green);
  border-radius:14px;
  overflow:hidden;
  box-shadow:
    0 0 50px rgba(80,220,100,.35),
    0 30px 80px rgba(0,0,0,.95);
  position:relative;
}
.video-frame::before{
  content:'';position:absolute;inset:0;
  background:linear-gradient(rgba(18,16,16,0) 50%, rgba(0,0,0,0.25) 50%);
  background-size:100% 4px;
  pointer-events:none;z-index:3;opacity:.5;
}
.video-frame video{
  width:100%;height:auto;display:block;
  max-height:72vh;object-fit:contain;
  background:#000;
}

/* Controls / Back button */
.action-bar{
  margin-top:1.8rem;display:flex;gap:1.2rem;justify-content:center;
}
.btn-back{
  display:inline-flex;align-items:center;gap:.6rem;
  padding:.8rem 2rem;border-radius:6px;
  background:rgba(80,220,100,0.15);
  border:2px solid var(--green);
  color:var(--green);
  font-family:var(--mono);font-size:.9rem;font-weight:600;
  text-decoration:none;letter-spacing:.1em;
  transition:all .3s ease;cursor:none;
  box-shadow:0 0 20px rgba(80,220,100,.2);
}
.btn-back:hover{
  background:var(--green);color:#04060e;
  box-shadow:0 0 40px rgba(80,220,100,.6);
  transform:translateY(-2px);
}
</style>
</head>
<body>

<div id="cursor"></div>
<div id="cursor-dot"></div>
<div class="bg-fx"></div>

<!-- VHS Overlay -->
<div id="vhs-overlay">
  <div class="vhs-scanlines"></div>
  <div class="vhs-tracking"></div>
  <div class="vhs-hud">
    <div>SECRET_TAPE &#9654; SP</div>
    <div>REC &#9679; 00:00:42</div>
  </div>
</div>

<div class="stage">
  <div class="alert-badge">&#9888; PROTOCOL MONYONG ACTIVATED</div>
  <h1 class="secret-title">// CLASSIFIED TAPE //</h1>
  <p class="secret-subtitle">Secret execution triggered from terminal. Enjoy the show.</p>

  <div class="video-frame">
    <video controls autoplay loop playsinline>
      <source src="/videos/monyong.mp4" type="video/mp4">
      Your browser does not support video playback.
    </video>
  </div>

  <div class="action-bar">
    <a href="/" class="btn-back" id="btn-return">&#8592; RETURN TO TERMINAL</a>
  </div>
</div>

<script>
// Cursor
const cursor = document.getElementById('cursor');
const cursorDot = document.getElementById('cursor-dot');
let mx = -100, my = -100;
document.addEventListener('mousemove', e => {
  mx = e.clientX; my = e.clientY;
  cursorDot.style.left = mx + 'px';
  cursorDot.style.top  = my + 'px';
});
let cx = -100, cy = -100;
function animCursor() {
  cx += (mx - cx) * 0.18;
  cy += (my - cy) * 0.18;
  cursor.style.left = cx + 'px';
  cursor.style.top  = cy + 'px';
  requestAnimationFrame(animCursor);
}
animCursor();

// Hover cursor expansion
document.querySelectorAll('a, button, video').forEach(el => {
  el.addEventListener('mouseenter', () => {
    cursor.style.transform = 'translate(-50%,-50%) scale(2)';
    cursor.style.borderColor = '#00e5ff';
  });
  el.addEventListener('mouseleave', () => {
    cursor.style.transform = 'translate(-50%,-50%) scale(1)';
    cursor.style.borderColor = '#50dc64';
  });
});

// VHS Glitch Transition on entry
const vhs = document.getElementById('vhs-overlay');
window.addEventListener('DOMContentLoaded', () => {
  vhs.classList.add('active');
  setTimeout(() => vhs.classList.remove('active'), 850);
});

// Return button with VHS glitch
document.getElementById('btn-return').addEventListener('click', function(e) {
  e.preventDefault();
  vhs.classList.add('active');
  setTimeout(() => {
    window.location.href = '/';
  }, 500);
});
</script>

</body>
</html>
