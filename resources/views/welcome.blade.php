<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Linux — The Open Source Revolution</title>
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=fira-code:400,500,700|inter:300,400,600,700,800&display=swap" rel="stylesheet">
<style>
/* ══════════════════════════════════════════
   RESET & BASE
══════════════════════════════════════════ */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
:root{
  --bg:        #04060e;
  --surface:   rgba(8,12,24,0.88);
  --border:    rgba(80,220,100,0.2);
  --green:     #50dc64;
  --green-dim: rgba(80,220,100,0.5);
  --green-glow:rgba(80,220,100,0.15);
  --cyan:      #00e5ff;
  --cyan-glow: rgba(0,229,255,0.15);
  --yellow:    #f4c430;
  --text:      #c8e0c8;
  --text-dim:  #567056;
  --danger:    #ff4444;
  --mono:      'Fira Code',monospace;
  --sans:      'Inter',sans-serif;
}
html{scroll-behavior:smooth;}
body{
  font-family:var(--sans);
  background:var(--bg);
  color:var(--text);
  overflow-x:hidden;
  cursor:none;
}

/* ══════════════════════════════════════════
   CUSTOM CURSOR
══════════════════════════════════════════ */
#cursor{
  position:fixed;width:20px;height:20px;
  border:2px solid var(--green);border-radius:50%;
  pointer-events:none;z-index:9999;
  transform:translate(-50%,-50%);
  transition:transform .1s ease,border-color .3s;
  mix-blend-mode:screen;
}
#cursor-dot{
  position:fixed;width:5px;height:5px;
  background:var(--green);border-radius:50%;
  pointer-events:none;z-index:9999;
  transform:translate(-50%,-50%);
}

/* ══════════════════════════════════════════
   BACKGROUND LAYER (blur + dark vignette)
══════════════════════════════════════════ */
#bg-blur{
  position:fixed;inset:0;z-index:0;
  background:
    radial-gradient(ellipse 70% 60% at 50% 50%, rgba(0,30,15,0.7) 0%, transparent 70%),
    radial-gradient(ellipse 100% 100% at 50% 50%, rgba(4,6,14,0.95) 0%, rgba(4,6,14,0.5) 100%);
  backdrop-filter:blur(0px);
  pointer-events:none;
}

/* ══════════════════════════════════════════
   PARTICLE CANVAS (behind everything)
══════════════════════════════════════════ */
#particle-canvas{
  position:fixed;inset:0;z-index:1;
  pointer-events:none;
  filter:blur(1.5px);  /* soft-blur the balls themselves */
}

/* ══════════════════════════════════════════
   CONTENT
══════════════════════════════════════════ */
.content{position:relative;z-index:10;}

/* ══════════════════════════════════════════
   NAVBAR
══════════════════════════════════════════ */
nav{
  position:fixed;top:0;left:0;right:0;z-index:200;
  display:flex;align-items:center;justify-content:space-between;
  padding:1rem 3rem;
  background:rgba(4,6,14,0.92);
  backdrop-filter:blur(20px);
  border-bottom:1px solid var(--border);
}
.nav-logo{
  font-family:var(--mono);font-size:1.1rem;color:var(--green);
  letter-spacing:.08em;display:flex;align-items:center;gap:.6rem;
}
.nav-logo::before{content:'$ ';color:var(--text-dim);}
.nav-links{display:flex;gap:2.4rem;list-style:none;}
.nav-links a{
  color:var(--text-dim);text-decoration:none;
  font-size:.85rem;font-weight:500;letter-spacing:.08em;
  text-transform:uppercase;position:relative;
  transition:color .25s;cursor:none;
}
.nav-links a::after{
  content:'';position:absolute;left:0;bottom:-4px;
  height:1px;width:0;background:var(--green);
  transition:width .3s;
}
.nav-links a:hover{color:var(--green);}
.nav-links a:hover::after{width:100%;}

/* Language Switcher */
.nav-right{
  display:flex;align-items:center;gap:.8rem;
}
.lang-btn{
  background:rgba(80,220,100,0.08);
  border:1px solid rgba(80,220,100,0.35);
  border-radius:999px;
  padding:.32rem .85rem;
  display:inline-flex;align-items:center;gap:.35rem;
  font-family:var(--mono);font-size:.8rem;
  color:var(--text-dim);cursor:pointer;
  transition:all .25s ease;user-select:none;
  backdrop-filter:blur(8px);
}
.lang-btn:hover{
  background:rgba(80,220,100,0.18);
  border-color:var(--green);
  box-shadow:0 0 16px rgba(80,220,100,0.35);
  color:#fff;
  transform:translateY(-1px);
}
.lang-globe{font-size:.95rem;line-height:1;}
.lang-badge{
  font-weight:600;letter-spacing:.05em;
  padding:.1rem .35rem;border-radius:4px;
  transition:all .2s ease;color:var(--text-dim);
}
.lang-badge.active{
  background:var(--green);color:#04060e;
  font-weight:700;box-shadow:0 0 8px rgba(80,220,100,0.5);
}
.lang-divider{color:rgba(255,255,255,0.25);font-size:.75rem;}

/* ══════════════════════════════════════════
   HERO
══════════════════════════════════════════ */
#hero{
  min-height:100vh;display:flex;flex-direction:column;
  align-items:center;justify-content:center;
  padding:8rem 2rem 4rem;text-align:center;
}
.hero-tux{
  font-size:7rem;margin-bottom:1rem;
  filter:drop-shadow(0 0 24px rgba(80,220,100,.5));
  opacity:0;animation:tuxEntry .8s .1s cubic-bezier(.16,1,.3,1) forwards;
}
@keyframes tuxEntry{
  0%{opacity:0;transform:scale(.4) rotate(-15deg);}
  60%{opacity:1;transform:scale(1.08) rotate(3deg);}
  100%{opacity:1;transform:scale(1) rotate(0);}
}
.terminal-prompt{
  font-family:var(--mono);font-size:.9rem;color:var(--text-dim);
  margin-bottom:1.5rem;opacity:0;
  animation:fadeUp .5s .4s ease forwards;
}
.terminal-prompt span{color:var(--green);}

/* GLITCH TITLE */
.hero-title-wrap{
  display:flex;flex-direction:column;align-items:center;
  gap:.4rem;margin-bottom:.5rem;
}
.hero-title-tag{
  font-family:var(--mono);font-size:.76rem;
  color:var(--green-dim);letter-spacing:.32em;text-transform:uppercase;
  opacity:0;animation:fadeUp .4s .3s ease forwards;
}
.hero-title{
  font-family:var(--mono);
  font-size:clamp(4rem,10vw,8.5rem);
  font-weight:800;color:var(--green);line-height:1;
  letter-spacing:.14em;position:relative;display:inline-block;
  padding:0 .2em;
  text-shadow:
    0 0 25px rgba(80,220,100,.6),
    0 0 60px rgba(80,220,100,.25),
    0 0 100px rgba(80,220,100,.1);
  opacity:0;
  animation:heroEntry 1.4s .5s cubic-bezier(.16,1,.3,1) forwards,
            jitter .12s 2.2s infinite;
}
/* Clean accent bar under title */
.hero-title-bar{
  display:flex;align-items:center;gap:.8rem;width:100%;max-width:440px;
  opacity:0;animation:fadeUp .4s 1.2s ease forwards;margin-top:.2rem;
}
.hero-title-bar::before,
.hero-title-bar::after{
  content:'';flex:1;height:1px;
  background:linear-gradient(90deg,transparent,var(--green-dim),transparent);
}
.hero-title-bar span{
  font-family:var(--mono);font-size:.7rem;
  color:var(--text-dim);letter-spacing:.25em;text-transform:uppercase;
  white-space:nowrap;
}
.hero-title::before{
  content:attr(data-text);position:absolute;inset:0;
  color:var(--cyan);
  animation:gSlice1 4s 2s infinite linear alternate-reverse;
  clip-path:polygon(0 0,100% 0,100% 32%,0 32%);
  pointer-events:none;
}
.hero-title::after{
  content:attr(data-text);position:absolute;inset:0;
  color:var(--danger);
  animation:gSlice2 3.5s 2s infinite linear alternate-reverse;
  clip-path:polygon(0 65%,100% 65%,100% 100%,0 100%);
  pointer-events:none;
}
@keyframes heroEntry{
  0%{opacity:0;transform:translate(-40px,20px) skewX(25deg) scaleY(1.5);
     filter:blur(6px) drop-shadow(-25px 0 0 var(--cyan)) drop-shadow(25px 0 0 var(--danger));
     clip-path:polygon(0 20%,100% 20%,100% 45%,0 45%);}
  12%{opacity:1;transform:translate(28px,-14px) skewX(-18deg);
      clip-path:polygon(0 60%,100% 60%,100% 90%,0 90%);}
  25%{opacity:.15;transform:translate(-18px,8px) skewX(14deg);
      clip-path:polygon(0 0,100% 0,100% 20%,0 20%);}
  40%{opacity:1;transform:translate(14px,-6px) skewX(-10deg);
      filter:drop-shadow(-12px 0 0 var(--cyan)) drop-shadow(12px 0 0 var(--danger));
      clip-path:inset(0 0 0 0);}
  70%{opacity:.9;transform:translate(-4px,2px);}
  100%{opacity:1;transform:translate(0,0) skewX(0);filter:none;clip-path:inset(0 0 0 0);}
}
@keyframes jitter{
  0%{transform:translate(0,0);}20%{transform:translate(-1px,1px);}
  40%{transform:translate(1px,-1px);}60%{transform:translate(-1px,0);}
  80%{transform:translate(1px,0);}100%{transform:translate(-1px,1px);}
}
@keyframes gSlice1{
  0%{clip-path:polygon(0 2%,100% 2%,100% 6%,0 6%);transform:translate(-4px,-2px);}
  10%{clip-path:polygon(0 20%,100% 20%,100% 25%,0 25%);transform:translate(4px,1px);}
  20%{clip-path:polygon(0 50%,100% 50%,100% 56%,0 56%);transform:translate(-3px,2px);}
  30%{clip-path:polygon(0 0,0 0,0 0,0 0);transform:translate(0);}
  100%{clip-path:polygon(0 0,0 0,0 0,0 0);transform:translate(0);}
}
@keyframes gSlice2{
  0%{clip-path:polygon(0 72%,100% 72%,100% 78%,0 78%);transform:translate(4px,2px);}
  8%{clip-path:polygon(0 88%,100% 88%,100% 95%,0 95%);transform:translate(-3px,-1px);}
  18%{clip-path:polygon(0 42%,100% 42%,100% 48%,0 48%);transform:translate(-4px,0);}
  26%{clip-path:polygon(0 0,0 0,0 0,0 0);transform:translate(0);}
  100%{clip-path:polygon(0 0,0 0,0 0,0 0);transform:translate(0);}
}

.hero-subtitle{
  font-size:clamp(1rem,2vw,1.3rem);color:var(--text-dim);
  margin-top:1.5rem;max-width:580px;line-height:1.65;
  opacity:0;animation:fadeUp .5s 1.4s ease forwards;
}
.hero-subtitle .hl{color:var(--green);font-weight:600;}
.hero-cta{
  display:flex;gap:1rem;margin-top:2.5rem;flex-wrap:wrap;
  justify-content:center;opacity:0;
  animation:fadeUp .5s 1.7s ease forwards;
}
.btn{
  display:inline-flex;align-items:center;gap:.5rem;
  padding:.75rem 1.8rem;border-radius:4px;
  font-family:var(--mono);font-size:.88rem;font-weight:500;
  letter-spacing:.08em;cursor:none;text-decoration:none;
  transition:all .3s ease;
}
.btn-primary{
  background:var(--green);color:#04060e;border:2px solid var(--green);
  box-shadow:0 0 25px rgba(80,220,100,.35);
}
.btn-primary:hover{background:transparent;color:var(--green);box-shadow:0 0 40px rgba(80,220,100,.5);}
.btn-secondary{background:transparent;color:var(--green);border:2px solid var(--border);}
.btn-secondary:hover{border-color:var(--green);box-shadow:0 0 20px rgba(80,220,100,.2);}

/* ══════════════════════════════════════════
   SHARED SECTION STYLES
══════════════════════════════════════════ */
section{padding:6rem 2rem;max-width:1120px;margin:0 auto;}
.section-label{
  font-family:var(--mono);font-size:.78rem;color:var(--green);
  letter-spacing:.25em;text-transform:uppercase;
  margin-bottom:.6rem;display:flex;align-items:center;gap:.8rem;
}
.section-label::before{content:'//';color:var(--text-dim);}
.section-title{
  font-size:clamp(2rem,4vw,3rem);font-weight:800;
  color:var(--text);line-height:1.1;margin-bottom:1rem;
}
.section-title .accent{color:var(--green);}
.section-desc{font-size:1.05rem;color:var(--text-dim);line-height:1.8;max-width:680px;}

/* Scroll reveal */
.reveal{
  opacity:0;transform:translateY(32px);
  transition:opacity .7s ease,transform .7s ease;
}
.reveal.visible{opacity:1;transform:translateY(0);}

/* ══════════════════════════════════════════
   WHAT IS LINUX
══════════════════════════════════════════ */
#what{border-top:1px solid var(--border);}
.what-grid{display:grid;grid-template-columns:1fr 1fr;gap:3rem;margin-top:3rem;align-items:start;}
.what-text p{font-size:1rem;color:var(--text-dim);line-height:1.85;margin-bottom:1.2rem;}
.what-text p strong{color:var(--text);font-weight:600;}
.what-text p .g{color:var(--green);}

/* Terminal box */
.terminal-box{
  background:rgba(0,0,0,.7);border:1px solid var(--border);
  border-radius:8px;overflow:hidden;
  box-shadow:0 0 40px rgba(0,0,0,.7),0 0 20px var(--green-glow);
  font-family:var(--mono);
}
.terminal-bar{
  background:rgba(80,220,100,.08);padding:.6rem 1rem;
  display:flex;align-items:center;gap:.5rem;
  border-bottom:1px solid var(--border);font-size:.72rem;color:var(--text-dim);
}
.dot{width:10px;height:10px;border-radius:50%;}
.dot.r{background:#ff5f57;}.dot.y{background:#febc2e;}.dot.g{background:#28c840;}
.terminal-body{padding:1.4rem 1.6rem;font-size:.86rem;line-height:2;}
.t-p{color:var(--green);} .t-c{color:var(--text);} .t-o{color:var(--text-dim);}
.t-b{color:var(--cyan);} .t-y{color:var(--yellow);}
.t-cursor{display:inline-block;width:9px;height:1.1em;background:var(--green);
  vertical-align:middle;animation:blink 1s infinite;}
@keyframes blink{0%,49%{opacity:1;}50%,100%{opacity:0;}}

/* ══════════════════════════════════════════
   CREATOR
══════════════════════════════════════════ */
#creator{border-top:1px solid var(--border);}
.creator-card{
  display:flex;gap:3rem;align-items:flex-start;margin-top:3rem;
  background:var(--surface);border:1px solid var(--border);
  border-radius:12px;padding:2.5rem;
  box-shadow:0 0 40px rgba(0,0,0,.5),0 0 20px var(--green-glow);
}
.creator-avatar{font-size:7rem;flex-shrink:0;filter:drop-shadow(0 0 18px rgba(80,220,100,.35));}
.creator-info h3{font-family:var(--mono);font-size:1.9rem;color:var(--green);margin-bottom:.4rem;}
.creator-info .born{font-family:var(--mono);font-size:.78rem;color:var(--text-dim);letter-spacing:.1em;margin-bottom:1.2rem;}
.creator-info p{font-size:1rem;color:var(--text-dim);line-height:1.8;margin-bottom:1rem;}
.creator-info p strong{color:var(--text);}
.creator-info p .g{color:var(--green);}
.timeline{margin-top:1.5rem;display:flex;flex-direction:column;gap:.8rem;}
.tl-item{display:flex;align-items:flex-start;gap:1rem;}
.tl-year{
  font-family:var(--mono);font-size:.78rem;color:var(--green);
  border:1px solid var(--border);border-radius:4px;
  padding:.2rem .6rem;white-space:nowrap;flex-shrink:0;
}
.tl-text{font-size:.9rem;color:var(--text-dim);line-height:1.6;}

/* ══════════════════════════════════════════
   DISTROS — CAROUSEL
══════════════════════════════════════════ */
#distros{border-top:1px solid var(--border);}

.carousel-wrapper{
  position:relative;margin-top:3rem;overflow:hidden;
}

.carousel-track{
  display:flex;
  transition:transform .55s cubic-bezier(.4,0,.2,1);
  will-change:transform;
}

.distro-slide{
  flex:0 0 100%;
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:2.4rem;
  padding:0 .5rem;
}

.distro-card{
  background:rgba(8,14,28,.9);
  border:1px solid var(--border);
  border-radius:12px;padding:2rem 1.8rem;
  position:relative;overflow:hidden;
  transition:all .4s cubic-bezier(.4,0,.2,1);
  cursor:none;
}
.distro-card::before{
  content:'';position:absolute;inset:0;
  background:linear-gradient(135deg,transparent 55%,rgba(80,220,100,.08));
  opacity:0;transition:opacity .4s;
}
.distro-card::after{
  content:'';position:absolute;inset:-1px;border-radius:12px;
  background:linear-gradient(135deg,var(--green),var(--cyan));
  opacity:0;z-index:-1;transition:opacity .4s;
}
.distro-card:hover{
  transform:translateY(-8px) scale(1.02);
  box-shadow:0 20px 50px rgba(0,0,0,.7),0 0 30px var(--green-glow);
  border-color:var(--green);
}
.distro-card:hover::before{opacity:1;}
.distro-card:hover::after{opacity:1;}

.distro-icon{font-size:3rem;margin-bottom:1rem;}
.distro-name{font-family:var(--mono);font-size:1.15rem;font-weight:700;color:var(--green);margin-bottom:.3rem;}
.distro-based{font-family:var(--mono);font-size:.7rem;color:var(--text-dim);letter-spacing:.08em;margin-bottom:.8rem;}
.distro-desc{font-size:.9rem;color:var(--text-dim);line-height:1.65;}
.distro-tags{display:flex;flex-wrap:wrap;gap:.4rem;margin-top:1rem;}
.tag{
  font-family:var(--mono);font-size:.7rem;color:var(--green);
  border:1px solid var(--border);border-radius:999px;
  padding:.2rem .65rem;letter-spacing:.06em;
  background:rgba(80,220,100,.06);
}

/* Carousel Controls */
.carousel-controls{
  display:flex;align-items:center;justify-content:center;
  gap:1.5rem;margin-top:2rem;
}
.carousel-btn{
  background:rgba(80,220,100,.1);border:1px solid var(--border);
  color:var(--green);font-family:var(--mono);font-size:1.3rem;
  width:48px;height:48px;border-radius:50%;
  cursor:none;display:flex;align-items:center;justify-content:center;
  transition:all .3s ease;
}
.carousel-btn:hover{
  background:var(--green);color:#04060e;
  box-shadow:0 0 20px rgba(80,220,100,.4);
  transform:scale(1.1);
}
.carousel-dots{display:flex;gap:.6rem;}
.carousel-dot{
  width:10px;height:10px;border-radius:50%;
  background:var(--border);border:1px solid var(--border);
  cursor:none;transition:all .3s ease;
}
.carousel-dot.active{
  background:var(--green);
  box-shadow:0 0 10px rgba(80,220,100,.6);
  transform:scale(1.2);
}
.carousel-counter{
  font-family:var(--mono);font-size:.8rem;color:var(--text-dim);
  letter-spacing:.1em;
}

/* ══════════════════════════════════════════
   USES
══════════════════════════════════════════ */
#uses{border-top:1px solid var(--border);}
.uses-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1.4rem;margin-top:3rem;}
.use-card{
  background:var(--surface);border:1px solid var(--border);
  border-radius:10px;padding:2rem 1.6rem;text-align:center;
  transition:all .3s ease;cursor:none;
}
.use-card:hover{
  border-color:var(--cyan);transform:translateY(-6px);
  box-shadow:0 10px 35px rgba(0,0,0,.5),0 0 20px var(--cyan-glow);
}
.use-icon{font-size:3rem;margin-bottom:1rem;display:block;}
.use-title{font-family:var(--mono);font-size:1rem;font-weight:600;color:var(--cyan);margin-bottom:.6rem;}
.use-desc{font-size:.9rem;color:var(--text-dim);line-height:1.65;}

/* ══════════════════════════════════════════
   STATS BANNER
══════════════════════════════════════════ */
.stats-banner{
  border-top:1px solid var(--border);border-bottom:1px solid var(--border);
  background:rgba(80,220,100,.03);padding:4rem 2rem;
}
.stats-inner{max-width:1120px;margin:0 auto;display:grid;grid-template-columns:repeat(4,1fr);gap:2rem;text-align:center;}
.stat-num{
  font-family:var(--mono);font-size:clamp(2.5rem,5vw,4rem);font-weight:700;
  color:var(--green);line-height:1;text-shadow:0 0 25px rgba(80,220,100,.45);
}
.stat-label{font-size:.85rem;color:var(--text-dim);margin-top:.4rem;letter-spacing:.05em;}

/* ══════════════════════════════════════════
   FOOTER
══════════════════════════════════════════ */
footer{
  border-top:1px solid var(--border);padding:2.5rem 3rem;
  display:flex;justify-content:space-between;align-items:center;
  font-family:var(--mono);font-size:.8rem;color:var(--text-dim);
}
footer .g{color:var(--green);}

/* ══════════════════════════════════════════
   VHS / GLITCH TRANSITION
══════════════════════════════════════════ */
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
    rgba(0,0,0,0.8) 0px,
    rgba(0,0,0,0.8) 2px,
    transparent 2px,
    transparent 4px
  );
  opacity:.9;
}
.vhs-noise{
  position:absolute;inset:0;
  background:radial-gradient(ellipse at center, rgba(80,220,100,0.1) 0%, rgba(0,0,0,0.6) 100%);
  mix-blend-mode:color-dodge;
}
.vhs-tracking-bar{
  position:absolute;left:0;right:0;height:24px;
  background:linear-gradient(
    180deg,
    transparent,
    rgba(80,220,100,.3),
    rgba(0,229,255,.6),
    rgba(255,60,60,.4),
    transparent
  );
  filter:blur(2px);
  top:-100px;
}
#vhs-overlay.active .vhs-tracking-bar{
  animation:vhsTrack .7s ease-in-out infinite;
}
@keyframes vhsTrack{
  0%{top:-30px;opacity:0;}
  20%{opacity:1;}
  80%{opacity:.9;}
  100%{top:100vh;opacity:0;}
}
@keyframes vhsFlicker{
  0%{transform:translate(0,0);filter:contrast(1.1) brightness(1);}
  25%{transform:translate(-3px,1px);filter:contrast(1.4) brightness(1.3) drop-shadow(4px 0 #00e5ff);}
  50%{transform:translate(2px,-2px);filter:contrast(1.2) brightness(0.9) drop-shadow(-4px 0 #ff4444);}
  75%{transform:translate(-1px,-1px);filter:contrast(1.5) brightness(1.2);}
  100%{transform:translate(0,0);filter:contrast(1.1) brightness(1);}
}
.vhs-hud{
  position:absolute;top:2rem;left:2.5rem;right:2.5rem;
  display:flex;justify-content:space-between;align-items:center;
  font-family:var(--mono);font-size:1.15rem;font-weight:700;
  color:#50dc64;text-shadow:0 0 10px rgba(80,220,100,.8), 2px 0 #ff4444;
  letter-spacing:.2em;
}
.vhs-hud-left{display:flex;align-items:center;gap:.6rem;}
.vhs-sp{
  background:#50dc64;color:#04060e;padding:.1rem .45rem;
  border-radius:2px;font-size:.8rem;font-weight:800;
}

/* ══════════════════════════════════════════
   DRAGGABLE & RESIZABLE CMD PROMPT WINDOW
══════════════════════════════════════════ */
#cmd-container{
  position:fixed;inset:0;z-index:600;
  pointer-events:none;
}
#cmd-window{
  position:fixed;
  top:95px;right:40px;
  width:min(660px,94vw);
  height:440px;
  min-width:340px;
  min-height:220px;
  max-width:96vw;
  max-height:90vh;
  resize:both; /* Native scale / resize handle */
  overflow:hidden;
  background:rgba(6,10,8,0.95);
  border:1px solid var(--green);
  border-radius:10px;
  box-shadow:0 15px 50px rgba(0,0,0,.85), 0 0 35px rgba(80,220,100,.25);
  backdrop-filter:blur(16px);
  pointer-events:all;
  display:none;
  flex-direction:column;
  user-select:none;
  transition:box-shadow .2s ease;
  z-index:610;
}
#cmd-window.show{
  display:flex;
  animation:winPop .3s cubic-bezier(.16,1,.3,1) forwards;
}
@keyframes winPop{
  from{opacity:0;transform:scale(.92) translateY(15px);}
  to{opacity:1;transform:scale(1) translateY(0);}
}
#cmd-window.minimized #cmd-body,
#cmd-window.minimized .cmd-input-container,
#cmd-window.minimized .win-resize-handle{
  display:none !important;
}
#cmd-window.minimized{
  height:auto !important;
  resize:none !important;
  box-shadow:0 6px 20px rgba(0,0,0,.6);
}
#cmd-window.maximized{
  top:30px !important;
  left:3vw !important;
  right:3vw !important;
  width:94vw !important;
  height:calc(100vh - 60px) !important;
  max-width:none !important;
  max-height:none !important;
}

#cmd-titlebar{
  background:rgba(80,220,100,.14);
  padding:.7rem 1rem;
  display:flex;align-items:center;justify-content:space-between;
  border-bottom:1px solid var(--border);
  font-family:var(--mono);font-size:.78rem;color:var(--text);
  cursor:grab;
  flex-shrink:0;
}
#cmd-titlebar:active{cursor:grabbing;}
.win-controls{display:flex;align-items:center;gap:.5rem;}
.dot{
  width:12px;height:12px;border-radius:50%;display:inline-block;
  cursor:pointer;transition:transform .15s ease, filter .15s;
}
.dot:hover{transform:scale(1.2);}
.dot.r{background:#ff5f57;}.dot.y{background:#febc2e;}.dot.g{background:#28c840;}
.win-title-text{
  font-family:var(--mono);font-size:.76rem;color:var(--green);
  letter-spacing:.05em;display:flex;align-items:center;gap:.4rem;
}
.win-drag-hint{
  font-size:.68rem;color:var(--text-dim);letter-spacing:.05em;
}

#cmd-body{
  padding:1.2rem 1.4rem;
  font-family:var(--mono);font-size:.84rem;line-height:1.85;
  flex:1;
  overflow-y:auto;
  color:var(--text-dim);
  user-select:text;
}
#cmd-body .cmd-line{color:var(--green);}
#cmd-body .cmd-out{color:var(--text-dim);}
#cmd-body .cmd-hl{color:var(--cyan);}
#cmd-body .cmd-warn{color:var(--yellow);}
#cmd-body .cmd-err{color:var(--danger);}

#cmd-cursor{
  display:inline-block;width:8px;height:1em;
  background:var(--green);vertical-align:middle;
  animation:blink 1s infinite;
}

/* Interactive Command Input Row */
.cmd-input-container{
  display:flex;align-items:center;gap:.6rem;
  padding:.6rem 1rem;
  background:rgba(4,8,6,0.9);
  border-top:1px solid var(--border);
  flex-shrink:0;
  position:relative;
}
.cmd-input-prefix{
  font-family:var(--mono);font-size:.84rem;
  color:var(--green);font-weight:600;
  white-space:nowrap;
}
.cmd-user-input{
  flex:1;
  background:transparent;
  border:none;
  outline:none;
  color:var(--text);
  font-family:var(--mono);font-size:.84rem;
  caret-color:var(--green);
}
.cmd-user-input::placeholder{
  color:rgba(86,112,86,0.6);
  font-size:.78rem;
}
.cmd-send-btn{
  background:rgba(80,220,100,0.15);
  border:1px solid var(--border);
  color:var(--green);
  font-family:var(--mono);font-size:.85rem;
  padding:.2rem .6rem;border-radius:4px;
  cursor:pointer;transition:all .2s;
}
.cmd-send-btn:hover{
  background:var(--green);color:#04060e;
}

/* Custom Corner Resize Grip Handle */
.win-resize-handle{
  position:absolute;right:3px;bottom:3px;
  width:16px;height:16px;
  cursor:nwse-resize;
  pointer-events:all;
  display:flex;align-items:flex-end;justify-content:flex-end;
  font-family:var(--mono);font-size:10px;color:var(--green-dim);
  opacity:.6;user-select:none;line-height:1;
}
.win-resize-handle:hover{opacity:1;color:var(--green);}

/* ══════════════════════════════════════════
   UTILITY
══════════════════════════════════════════ */
@keyframes fadeUp{
  from{opacity:0;transform:translateY(16px);}
  to{opacity:1;transform:translateY(0);}
}

/* ══════════════════════════════════════════
   RESPONSIVE
══════════════════════════════════════════ */
@media(max-width:860px){
  nav{padding:1rem 1.5rem;}
  .nav-links{display:none;}
  .what-grid{grid-template-columns:1fr;}
  .creator-card{flex-direction:column;gap:1.5rem;}
  .stats-inner{grid-template-columns:repeat(2,1fr);}
  .distro-slide{grid-template-columns:1fr;}
}
@media(max-width:480px){
  .stats-inner{grid-template-columns:1fr 1fr;}
  footer{flex-direction:column;gap:.5rem;text-align:center;}
}
</style>
</head>
<body>

<!-- Custom cursor -->
<div id="cursor"></div>
<div id="cursor-dot"></div>

<!-- Background blur layer -->
<div id="bg-blur"></div>

<!-- Particle canvas -->
<canvas id="particle-canvas"></canvas>

<!-- VHS Glitch Transition Overlay -->
<div id="vhs-overlay">
  <div class="vhs-scanlines"></div>
  <div class="vhs-noise"></div>
  <div class="vhs-tracking-bar"></div>
  <div class="vhs-hud">
    <div class="vhs-hud-left">
      <span>PLAY</span> &#9654; <span class="vhs-sp">SP</span>
    </div>
    <div class="vhs-hud-right" id="vhs-timer">00:00:01</div>
  </div>
</div>

<!-- ══════════ DRAGGABLE & RESIZABLE CMD WINDOW ══════════ -->
<div id="cmd-container">
  <div id="cmd-window">
    <div id="cmd-titlebar">
      <div class="win-controls">
        <span class="dot r" onclick="closeCmd()" title="Close Window"></span>
        <span class="dot y" onclick="toggleMinimizeCmd()" title="Minimize / Restore"></span>
        <span class="dot g" onclick="toggleMaximizeCmd()" title="Maximize / Restore"></span>
      </div>
      <div class="win-title-text">
        <span style="opacity:.6">user@linux:~#</span>
        <span id="cmd-title">bash — section summary</span>
      </div>
      <div class="win-drag-hint">:: DRAG ME</div>
    </div>
    <div id="cmd-body"></div>
    <div class="cmd-input-container">
      <span class="cmd-input-prefix">user@linux:~$</span>
      <input type="text" id="cmd-user-input" class="cmd-user-input" placeholder="Type command (e.g. '1 seed', 'monyong', 'help')..." autocomplete="off" spellcheck="false" />
      <button id="cmd-send-btn" class="cmd-send-btn" title="Run command" onclick="execUserInput()">↵</button>
      <div class="win-resize-handle" id="win-resize-handle" title="Drag corner to resize">///</div>
    </div>
  </div>
</div>

<div class="content">

  <!-- ══ NAVBAR ══ -->
  <nav>
    <div class="nav-logo">linux --interactive</div>
    <ul class="nav-links">
      <li><a href="#hero"    data-section="hero" data-i18n="nav_home">~/home</a></li>
      <li><a href="#what"    data-section="what" data-i18n="nav_what">What</a></li>
      <li><a href="#creator" data-section="creator" data-i18n="nav_creator">Creator</a></li>
      <li><a href="#distros" data-section="distros" data-i18n="nav_distros">Distros</a></li>
      <li><a href="#uses"    data-section="uses" data-i18n="nav_uses">Uses</a></li>
    </ul>
    <div class="nav-right">
      <button class="lang-btn" id="lang-toggle-btn" onclick="toggleLanguage()" title="Ganti Bahasa (English / Bahasa Indonesia)">
        <span class="lang-globe">🌐</span>
        <span class="lang-badge active" id="lang-badge-en">EN</span>
        <span class="lang-divider">/</span>
        <span class="lang-badge" id="lang-badge-id">ID</span>
      </button>
    </div>
  </nav>

  <!-- ══ HERO ══ -->
  <section id="hero">
    <div class="hero-tux">🐧</div>
    <div class="terminal-prompt">
      <span>user@linux</span>:~$ <span data-i18n="hero_prompt">echo "Welcome to the Linux Universe"</span>
    </div>
    <div class="hero-title-wrap">
      <div class="hero-title-tag" data-i18n="hero_title_tag">// KERNEL ARCHITECTURE //</div>
      <h1 class="hero-title" data-text="LINUX" id="hero-title">LINUX</h1>
      <div class="hero-title-bar"><span data-i18n="hero_title_bar">THE OPEN SOURCE REVOLUTION</span></div>
    </div>
    <p class="hero-subtitle" data-i18n="hero_subtitle">
      The <span class="hl">free & open-source</span> kernel powering
      everything from your pocket to <span class="hl">outer space</span>.
    </p>
    <div class="hero-cta">
      <a href="#what"    class="btn btn-primary"    data-section="what" data-i18n="hero_cta_explore">$ explore --all</a>
      <a href="#distros" class="btn btn-secondary"  data-section="distros" data-i18n="hero_cta_distros">$ list distros</a>
    </div>
  </section>

  <!-- ══ WHAT ══ -->
  <section id="what">
    <div class="reveal">
      <div class="section-label" data-i18n="what_label">01 — Definition</div>
      <h2 class="section-title" data-i18n="what_title">What is <span class="accent">Linux</span>?</h2>
      <p class="section-desc" data-i18n="what_desc">More than an OS — a philosophy of freedom, transparency, and community-driven power.</p>
    </div>
    <div class="what-grid">
      <div class="what-text reveal" style="transition-delay:.1s">
        <p data-i18n="what_p1"><strong>Linux</strong> is an open-source, Unix-like
          <span class="g">operating system kernel</span> first released in 1991 by
          Linus Torvalds. The kernel manages hardware, memory, processes,
          and system calls for the entire OS.</p>
        <p data-i18n="what_p2">When people say "Linux" they usually mean a full
          <span class="g">distribution</span>: kernel + package manager +
          desktop environment + system tools — all bundled into one
          installable OS.</p>
        <p data-i18n="what_p3">Released under the <strong>GNU General Public License (GPL)</strong>,
          anyone can read, modify, and distribute Linux freely. This radical
          openness sparked a global developer revolution and made Linux the
          <span class="g">backbone of the modern internet</span>.</p>
        <p data-i18n="what_p4"><strong>96.4%</strong> of the world's top web servers,
          <strong>all 500</strong> supercomputers, and
          <strong>Android</strong> (3.6 billion users) run Linux today.</p>
      </div>
      <div class="reveal" style="transition-delay:.25s">
        <div class="terminal-box">
          <div class="terminal-bar">
            <span class="dot r"></span><span class="dot y"></span><span class="dot g"></span>
            <span style="margin-left:.4rem">bash — 80×24</span>
          </div>
          <div class="terminal-body">
            <div><span class="t-p">user@linux:~$</span> <span class="t-c">uname -a</span></div>
            <div class="t-o">Linux hostname 6.6.0-1 #1 SMP x86_64 GNU/Linux</div><br>
            <div><span class="t-p">user@linux:~$</span> <span class="t-c">cat /etc/os-release</span></div>
            <div class="t-b">NAME="Ubuntu 24.04 LTS"</div>
            <div class="t-b">ID=ubuntu &nbsp;&nbsp;ID_LIKE=debian</div>
            <div class="t-b">PRETTY_NAME="Ubuntu Noble Numbat"</div><br>
            <div><span class="t-p">user@linux:~$</span> <span class="t-c">uptime</span></div>
            <div class="t-o"> 07:31 up 420 days, 13:37, 2 users, load 0.42</div><br>
            <div><span class="t-p">user@linux:~$</span> <span class="t-c">whoami</span></div>
            <div class="t-y">root</div><br>
            <div><span class="t-p">user@linux:~$</span> <span class="t-cursor"></span></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ══ STATS ══ -->
  <div class="stats-banner">
    <div class="stats-inner">
      <div class="reveal"><div class="stat-num">96%</div><div class="stat-label" data-i18n="stat_web">of Top Web Servers</div></div>
      <div class="reveal" style="transition-delay:.1s"><div class="stat-num">500</div><div class="stat-label" data-i18n="stat_supercomputers">of 500 Supercomputers</div></div>
      <div class="reveal" style="transition-delay:.2s"><div class="stat-num">3.6B</div><div class="stat-label" data-i18n="stat_android">Android Users</div></div>
      <div class="reveal" style="transition-delay:.3s"><div class="stat-num">1991</div><div class="stat-label" data-i18n="stat_year">Year Linux Was Born</div></div>
    </div>
  </div>

  <!-- ══ CREATOR ══ -->
  <section id="creator">
    <div class="reveal">
      <div class="section-label" data-i18n="creator_label">02 — Creator</div>
      <h2 class="section-title" data-i18n="creator_title">Who <span class="accent">Created</span> Linux?</h2>
      <p class="section-desc" data-i18n="creator_desc">A Finnish student's weekend project that changed the entire world.</p>
    </div>
    <div class="creator-card reveal" style="transition-delay:.15s">
      <div class="creator-avatar">👨‍💻</div>
      <div class="creator-info">
        <h3>Linus Benedict Torvalds</h3>
        <div class="born" data-i18n="creator_born">Born: December 28, 1969 — Helsinki, Finland</div>
        <p data-i18n="creator_p1">On <strong>August 25, 1991</strong>, Linus posted a legendary message:
          <span class="g">"I'm doing a (free) OS… just a hobby, won't be big and professional."</span>
          That hobby became the most important software in history.</p>
        <p data-i18n="creator_p2">Today Linus still leads the kernel project as its principal developer
          — assisted by thousands worldwide — and in 2005 also created
          <strong>Git</strong>, the version control system used by virtually every software project on Earth.</p>
        <div class="timeline">
          <div class="tl-item"><span class="tl-year">1991</span><span class="tl-text" data-i18n="creator_tl_1991">Released Linux kernel 0.01 to the public</span></div>
          <div class="tl-item"><span class="tl-year">1992</span><span class="tl-text" data-i18n="creator_tl_1992">Adopted GPL — made Linux truly free and open</span></div>
          <div class="tl-item"><span class="tl-year">1994</span><span class="tl-text" data-i18n="creator_tl_1994">Linux kernel 1.0 — first stable release</span></div>
          <div class="tl-item"><span class="tl-year">2005</span><span class="tl-text" data-i18n="creator_tl_2005">Created Git — world's most used version control system</span></div>
          <div class="tl-item"><span class="tl-year">2024</span><span class="tl-text" data-i18n="creator_tl_2024">Linux 6.x — still actively developed, 33 years on</span></div>
        </div>
      </div>
    </div>
  </section>

  <!-- ══ DISTROS — CAROUSEL ══ -->
  <section id="distros">
    <div class="reveal">
      <div class="section-label" data-i18n="distros_label">03 — Distributions</div>
      <h2 class="section-title" data-i18n="distros_title">Linux <span class="accent">Distros</span></h2>
      <p class="section-desc" data-i18n="distros_desc">One kernel, hundreds of flavors. Use the arrows to browse each distribution.</p>
    </div>

    <div class="carousel-wrapper reveal" style="transition-delay:.1s">
      <div class="carousel-track" id="carousel-track">

        <!-- Slide 1 -->
        <div class="distro-slide">
          <div class="distro-card">
            <div class="distro-icon">🟠</div>
            <div class="distro-name">Ubuntu</div>
            <div class="distro-based" data-i18n="distro_ubuntu_base">Base: Debian · Sponsor: Canonical</div>
            <div class="distro-desc" data-i18n="distro_ubuntu_desc">The world's most popular Linux desktop distro. Beginner-friendly with Long-Term Support (LTS) releases every 2 years. The standard for newcomers and cloud servers alike.</div>
            <div class="distro-tags" data-distro="ubuntu"><span class="tag">beginner</span><span class="tag">desktop</span><span class="tag">server</span><span class="tag">LTS</span></div>
          </div>
          <div class="distro-card">
            <div class="distro-icon">🎩</div>
            <div class="distro-name">Fedora</div>
            <div class="distro-based" data-i18n="distro_fedora_base">Base: Independent (RPM) · Sponsor: Red Hat</div>
            <div class="distro-desc" data-i18n="distro_fedora_desc">Cutting-edge open-source features with a clean GNOME desktop. Preferred by developers who want the freshest stable software without chasing bleeding-edge rolling releases.</div>
            <div class="distro-tags" data-distro="fedora"><span class="tag">developer</span><span class="tag">cutting-edge</span><span class="tag">GNOME</span></div>
          </div>
        </div>

        <!-- Slide 2 -->
        <div class="distro-slide">
          <div class="distro-card">
            <div class="distro-icon">🏹</div>
            <div class="distro-name">Arch Linux</div>
            <div class="distro-based" data-i18n="distro_arch_base">Base: Independent · Type: Rolling Release</div>
            <div class="distro-desc" data-i18n="distro_arch_desc">Build your OS entirely from scratch. Minimalist, fast, and community-driven. Home to the legendary Arch Wiki. Beloved by power users. <em>"I use Arch btw."</em></div>
            <div class="distro-tags" data-distro="arch"><span class="tag">advanced</span><span class="tag">rolling</span><span class="tag">minimal</span><span class="tag">AUR</span></div>
          </div>
          <div class="distro-card">
            <div class="distro-icon">🐉</div>
            <div class="distro-name">Kali Linux</div>
            <div class="distro-based" data-i18n="distro_kali_base">Base: Debian · Purpose: Security</div>
            <div class="distro-desc" data-i18n="distro_kali_desc">The penetration testing & cybersecurity distro, shipping with 600+ pre-installed security tools. Used by ethical hackers, forensic investigators, and security researchers worldwide.</div>
            <div class="distro-tags" data-distro="kali"><span class="tag">security</span><span class="tag">pentesting</span><span class="tag">forensics</span></div>
          </div>
        </div>

        <!-- Slide 3 -->
        <div class="distro-slide">
          <div class="distro-card">
            <div class="distro-icon">🐧</div>
            <div class="distro-name">Debian</div>
            <div class="distro-based" data-i18n="distro_debian_base">Base: Independent · Type: Stable</div>
            <div class="distro-desc" data-i18n="distro_debian_desc">The mother of many distros, including Ubuntu and Kali. Extreme stability over new features. The gold standard for production servers where reliability is non-negotiable.</div>
            <div class="distro-tags" data-distro="debian"><span class="tag">stable</span><span class="tag">server</span><span class="tag">parent-distro</span></div>
          </div>
          <div class="distro-card">
            <div class="distro-icon">🍃</div>
            <div class="distro-name">Linux Mint</div>
            <div class="distro-based" data-i18n="distro_mint_base">Base: Ubuntu / Debian</div>
            <div class="distro-desc" data-i18n="distro_mint_desc">The Windows switcher's best friend. Familiar Cinnamon desktop, polished UX, rock-stable. Perfect for everyday computing without any learning curve.</div>
            <div class="distro-tags" data-distro="mint"><span class="tag">beginner</span><span class="tag">Windows-like</span><span class="tag">Cinnamon</span></div>
          </div>
        </div>

        <!-- Slide 4 -->
        <div class="distro-slide">
          <div class="distro-card">
            <div class="distro-icon">🔵</div>
            <div class="distro-name">openSUSE</div>
            <div class="distro-based" data-i18n="distro_suse_base">Base: Independent · German Engineering</div>
            <div class="distro-desc" data-i18n="distro_suse_desc">Enterprise-grade power with two variants: Leap (stable) and Tumbleweed (rolling). YaST control center makes system administration intuitive. Used heavily in enterprise environments.</div>
            <div class="distro-tags" data-distro="suse"><span class="tag">enterprise</span><span class="tag">stable</span><span class="tag">rolling</span></div>
          </div>
          <div class="distro-card">
            <div class="distro-icon">🛡️</div>
            <div class="distro-name">Rocky Linux</div>
            <div class="distro-based" data-i18n="distro_rocky_base">Base: RHEL-compatible</div>
            <div class="distro-desc" data-i18n="distro_rocky_desc">The spiritual successor to CentOS — community-built, RHEL-compatible, enterprise-grade. Trusted for mission-critical server and data-center deployments worldwide.</div>
            <div class="distro-tags" data-distro="rocky"><span class="tag">enterprise</span><span class="tag">server</span><span class="tag">RHEL-compat</span></div>
          </div>
        </div>

      </div><!-- /track -->
    </div><!-- /wrapper -->

    <!-- Carousel controls -->
    <div class="carousel-controls">
      <button class="carousel-btn" id="prev-btn" onclick="carouselMove(-1)">◀</button>
      <div class="carousel-dots" id="carousel-dots"></div>
      <button class="carousel-btn" id="next-btn" onclick="carouselMove(1)">▶</button>
      <span class="carousel-counter" id="carousel-counter">01 / 04</span>
    </div>
  </section>

  <!-- ══ USES ══ -->
  <section id="uses">
    <div class="reveal">
      <div class="section-label" data-i18n="uses_label">04 — Use Cases</div>
      <h2 class="section-title" data-i18n="uses_title">What is Linux <span class="accent">Used For</span>?</h2>
      <p class="section-desc" data-i18n="uses_desc">From your pocket to outer space — Linux is literally everywhere.</p>
    </div>
    <div class="uses-grid">
      <div class="use-card reveal" style="transition-delay:.05s">
        <span class="use-icon">🌐</span>
        <div class="use-title" data-i18n="uses_web_title">Web Servers</div>
        <div class="use-desc" data-i18n="uses_web_desc">Apache, Nginx, and virtually every major web server runs on Linux. Google, Amazon, Facebook, and Netflix all serve billions from Linux clusters.</div>
      </div>
      <div class="use-card reveal" style="transition-delay:.1s">
        <span class="use-icon">🤖</span>
        <div class="use-title" data-i18n="uses_android_title">Android / Mobile</div>
        <div class="use-desc" data-i18n="uses_android_desc">Android is built on the Linux kernel. Every Samsung, Pixel, or Xiaomi phone is running Linux — that's 3.6 billion people, every single day.</div>
      </div>
      <div class="use-card reveal" style="transition-delay:.15s">
        <span class="use-icon">🚀</span>
        <div class="use-title" data-i18n="uses_space_title">Space & Science</div>
        <div class="use-desc" data-i18n="uses_space_desc">The ISS, NASA Mars rovers, and SpaceX Falcon 9 all rely on Linux for mission-critical real-time computing. Even the Ingenuity Mars helicopter uses Linux.</div>
      </div>
      <div class="use-card reveal" style="transition-delay:.2s">
        <span class="use-icon">🧠</span>
        <div class="use-title" data-i18n="uses_ai_title">AI & Supercomputing</div>
        <div class="use-desc" data-i18n="uses_ai_desc">All 500 of the world's top supercomputers run Linux. ChatGPT, Gemini, and every major AI model was trained on Linux-powered GPU clusters.</div>
      </div>
      <div class="use-card reveal" style="transition-delay:.25s">
        <span class="use-icon">🔐</span>
        <div class="use-title" data-i18n="uses_security_title">Cybersecurity</div>
        <div class="use-desc" data-i18n="uses_security_desc">Ethical hackers and security professionals use Kali Linux, Parrot OS, and others to protect networks, uncover vulnerabilities, and conduct forensic analysis.</div>
      </div>
      <div class="use-card reveal" style="transition-delay:.3s">
        <span class="use-icon">☁️</span>
        <div class="use-title" data-i18n="uses_cloud_title">Cloud Computing</div>
        <div class="use-desc" data-i18n="uses_cloud_desc">AWS, Google Cloud, and Azure all run Linux VMs as their primary workload. Kubernetes, Docker, and all container tech is fundamentally Linux-native.</div>
      </div>
      <div class="use-card reveal" style="transition-delay:.35s">
        <span class="use-icon">🖥️</span>
        <div class="use-title" data-i18n="uses_desktop_title">Desktop Computing</div>
        <div class="use-desc" data-i18n="uses_desktop_desc">From lightweight distros on old hardware to powerful developer workstations — Linux desktops offer speed, privacy, and total customization.</div>
      </div>
      <div class="use-card reveal" style="transition-delay:.4s">
        <span class="use-icon">📺</span>
        <div class="use-title" data-i18n="uses_iot_title">Embedded & IoT</div>
        <div class="use-desc" data-i18n="uses_iot_desc">Smart TVs, routers, cars, cameras, and medical devices run embedded Linux. Its tiny footprint and extreme stability make it ideal for IoT hardware.</div>
      </div>
    </div>
  </section>

  <!-- ══ FOOTER ══ -->
  <footer>
    <div data-i18n="footer_built">Built with <span class="g">❤</span> for the open-source community</div>
    <div><span class="g">Linux</span> <span data-i18n="footer_interactive">Interactive</span> — {{ date('Y') }}</div>
    <div>kernel <span class="g">6.6.0-lts</span></div>
  </footer>

</div><!-- /content -->

<script>
/* ══════════════════════════════════════════════════════
   CUSTOM CURSOR
══════════════════════════════════════════════════════ */
const cursor    = document.getElementById('cursor');
const cursorDot = document.getElementById('cursor-dot');
let mx = -300, my = -300;
document.addEventListener('mousemove', e => {
  mx = e.clientX; my = e.clientY;
  cursorDot.style.left = mx + 'px';
  cursorDot.style.top  = my + 'px';
});
let cx = -300, cy = -300;
function animCursor() {
  cx += (mx - cx) * 0.18;
  cy += (my - cy) * 0.18;
  cursor.style.left = cx + 'px';
  cursor.style.top  = cy + 'px';
  requestAnimationFrame(animCursor);
}
animCursor();
document.querySelectorAll('a,button,.distro-card,.use-card,.lang-btn').forEach(el => {
  el.addEventListener('mouseenter', () => {
    cursor.style.transform = 'translate(-50%,-50%) scale(2)';
    cursor.style.borderColor = '#00e5ff';
  });
  el.addEventListener('mouseleave', () => {
    cursor.style.transform = 'translate(-50%,-50%) scale(1)';
    cursor.style.borderColor = 'var(--green)';
  });
});

/* ══════════════════════════════════════════════════════
   AUTONOMOUS FLEEING BALL PARTICLES
   - Each ball moves on its own with random velocity
   - On cursor proximity → flee away
   - Random radius (small to large)
   - Glow effect per ball
══════════════════════════════════════════════════════ */
const canvas = document.getElementById('particle-canvas');
const ctx    = canvas.getContext('2d');
let W = canvas.width  = window.innerWidth;
let H = canvas.height = window.innerHeight;
window.addEventListener('resize', () => {
  W = canvas.width  = window.innerWidth;
  H = canvas.height = window.innerHeight;
});

const FLEE_RADIUS = 160;
const FLEE_FORCE  = 16;
const NUM_BALLS   = 55;

const PALETTES = [
  { core:'rgba(80,220,100,0.85)', glow:'rgba(80,220,100,0.35)', size:1.0 },
  { core:'rgba(80,220,100,0.6)',  glow:'rgba(80,220,100,0.2)',  size:0.65 },
  { core:'rgba(0,229,255,0.85)',  glow:'rgba(0,229,255,0.3)',   size:0.95 },
  { core:'rgba(0,229,255,0.5)',   glow:'rgba(0,229,255,0.18)',  size:0.55 },
  { core:'rgba(244,196,48,0.75)', glow:'rgba(244,196,48,0.25)', size:0.75 },
  { core:'rgba(255,255,255,0.5)', glow:'rgba(255,255,255,0.15)',size:0.45 },
];

class Ball {
  constructor() { this.init(); }
  init() {
    this.r = 3.5 + Math.random() * 20;
    this.x = this.r + Math.random() * (W - this.r * 2);
    this.y = this.r + Math.random() * (H - this.r * 2);
    
    // Constant roaming speed
    this.cruiseSpeed = 0.8 + Math.random() * 1.6;
    this.angle       = Math.random() * Math.PI * 2;
    this.vx          = Math.cos(this.angle) * this.cruiseSpeed;
    this.vy          = Math.sin(this.angle) * this.cruiseSpeed;
    this.wanderAngle = this.angle;

    const pal  = PALETTES[Math.floor(Math.random() * PALETTES.length)];
    this.core  = pal.core;
    this.glow  = pal.glow;
    this.scale = pal.size;
    this.dr    = this.r * this.scale;
    this.phase = Math.random() * Math.PI * 2;
    this.pulseSpeed = 0.02 + Math.random() * 0.03;
  }
  update(frame) {
    // 1. Autonomous wander: gently steer direction over time
    this.wanderAngle += (Math.random() - 0.5) * 0.12;
    this.vx += Math.cos(this.wanderAngle) * 0.08;
    this.vy += Math.sin(this.wanderAngle) * 0.08;

    // 2. Flee away if cursor approaches
    const dx   = this.x - mx;
    const dy   = this.y - my;
    const dist = Math.sqrt(dx * dx + dy * dy);

    if (dist < FLEE_RADIUS && dist > 0) {
      const force = (1 - dist / FLEE_RADIUS) * FLEE_FORCE;
      this.vx += (dx / dist) * force;
      this.vy += (dy / dist) * force;
      // Redirect wander heading to flee vector so it CONTINUES in escape direction!
      this.wanderAngle = Math.atan2(this.vy, this.vx);
    }

    // 3. Speed management: clamp max & preserve minimum cruising velocity
    let curSpd = Math.sqrt(this.vx * this.vx + this.vy * this.vy);
    const maxSpd = 12;
    if (curSpd > maxSpd) {
      this.vx = (this.vx / curSpd) * maxSpd;
      this.vy = (this.vy / curSpd) * maxSpd;
      curSpd = maxSpd;
    }

    if (curSpd > this.cruiseSpeed) {
      this.vx *= 0.96; // smoothly return to cruise speed
      this.vy *= 0.96;
    } else if (curSpd < this.cruiseSpeed * 0.8) {
      const boost = this.cruiseSpeed / (curSpd || 0.1);
      this.vx *= boost;
      this.vy *= boost;
    }

    this.x += this.vx;
    this.y += this.vy;

    // 4. Boundary bouncing
    if (this.x - this.r < 0) {
      this.x = this.r;
      this.vx = Math.abs(this.vx);
      this.wanderAngle = Math.atan2(this.vy, this.vx);
    } else if (this.x + this.r > W) {
      this.x = W - this.r;
      this.vx = -Math.abs(this.vx);
      this.wanderAngle = Math.atan2(this.vy, this.vx);
    }
    if (this.y - this.r < 0) {
      this.y = this.r;
      this.vy = Math.abs(this.vy);
      this.wanderAngle = Math.atan2(this.vy, this.vx);
    } else if (this.y + this.r > H) {
      this.y = H - this.r;
      this.vy = -Math.abs(this.vy);
      this.wanderAngle = Math.atan2(this.vy, this.vx);
    }

    this.phase += this.pulseSpeed;
  }
  draw() {
    const pulseFactor = 0.8 + 0.2 * Math.sin(this.phase);
    const drawR = this.dr * pulseFactor;

    // Outer glow
    const g2 = ctx.createRadialGradient(this.x, this.y, 0, this.x, this.y, drawR * 5);
    g2.addColorStop(0,   this.glow);
    g2.addColorStop(0.4, this.glow.replace(/[\d.]+\)$/, '0.1)'));
    g2.addColorStop(1,   'transparent');
    ctx.beginPath();
    ctx.arc(this.x, this.y, drawR * 5, 0, Math.PI*2);
    ctx.fillStyle = g2;
    ctx.fill();

    // Inner glow halo
    const g1 = ctx.createRadialGradient(this.x, this.y, 0, this.x, this.y, drawR * 2.2);
    g1.addColorStop(0,   this.core.replace(/[\d.]+\)$/, '0.95)'));
    g1.addColorStop(0.5, this.glow.replace(/[\d.]+\)$/, '0.45)'));
    g1.addColorStop(1,   'transparent');
    ctx.beginPath();
    ctx.arc(this.x, this.y, drawR * 2.2, 0, Math.PI*2);
    ctx.fillStyle = g1;
    ctx.fill();

    // Core ball
    ctx.beginPath();
    ctx.arc(this.x, this.y, drawR, 0, Math.PI*2);
    ctx.fillStyle = this.core;
    ctx.fill();

    // Specular highlight
    ctx.beginPath();
    ctx.arc(this.x - drawR * 0.3, this.y - drawR * 0.3, drawR * 0.25, 0, Math.PI*2);
    ctx.fillStyle = 'rgba(255,255,255,0.5)';
    ctx.fill();
  }
}

// Connection lines between close balls
function drawConnections(balls) {
  const MAX_DIST = 95;
  for (let i = 0; i < balls.length; i++) {
    for (let j = i+1; j < balls.length; j++) {
      const dx = balls[i].x - balls[j].x;
      const dy = balls[i].y - balls[j].y;
      const d  = Math.sqrt(dx*dx + dy*dy);
      if (d < MAX_DIST) {
        ctx.beginPath();
        ctx.moveTo(balls[i].x, balls[i].y);
        ctx.lineTo(balls[j].x, balls[j].y);
        ctx.strokeStyle = `rgba(80,220,100,${(1 - d/MAX_DIST) * 0.16})`;
        ctx.lineWidth = 1;
        ctx.stroke();
      }
    }
  }
}

const balls = Array.from({ length: NUM_BALLS }, () => new Ball());
let frame = 0;
function particleLoop() {
  ctx.clearRect(0, 0, W, H);
  drawConnections(balls);
  balls.forEach(b => { b.update(frame); b.draw(); });
  frame++;
  requestAnimationFrame(particleLoop);
}
particleLoop();

/* ══════════════════════════════════════════════════════
   DISTRO CAROUSEL
══════════════════════════════════════════════════════ */
const track   = document.getElementById('carousel-track');
const dotsEl  = document.getElementById('carousel-dots');
const counter = document.getElementById('carousel-counter');
const slides  = track.querySelectorAll('.distro-slide');
const total   = slides.length;
let current   = 0;

// Build dots
slides.forEach((_, i) => {
  const dot = document.createElement('div');
  dot.className = 'carousel-dot' + (i === 0 ? ' active' : '');
  dot.onclick = () => carouselGoTo(i);
  dotsEl.appendChild(dot);
});

function carouselGoTo(idx) {
  current = (idx + total) % total;
  track.style.transform = `translateX(-${current * 100}%)`;
  dotsEl.querySelectorAll('.carousel-dot').forEach((d, i) => {
    d.classList.toggle('active', i === current);
  });
  counter.textContent = String(current+1).padStart(2,'0') + ' / ' + String(total).padStart(2,'0');
}
function carouselMove(dir) { carouselGoTo(current + dir); }

// Swipe/drag support
let dragStart = null;
track.addEventListener('pointerdown', e => { dragStart = e.clientX; });
track.addEventListener('pointerup',   e => {
  if (dragStart === null) return;
  const delta = e.clientX - dragStart;
  if (Math.abs(delta) > 50) carouselMove(delta < 0 ? 1 : -1);
  dragStart = null;
});

// Keyboard left/right inside distros section
document.addEventListener('keydown', e => {
  if (e.key === 'ArrowRight') carouselMove(1);
  if (e.key === 'ArrowLeft')  carouselMove(-1);
});

/* ══════════════════════════════════════════════════════
   SCROLL REVEAL
══════════════════════════════════════════════════════ */
const revealEls = document.querySelectorAll('.reveal');
const revObs = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) { e.target.classList.add('visible'); revObs.unobserve(e.target); }
  });
}, { threshold: 0.1 });
revealEls.forEach(el => revObs.observe(el));

/* ══════════════════════════════════════════════════════
   HERO TITLE SCRAMBLE ENTRY
══════════════════════════════════════════════════════ */
(function(){
  const glyphs  = '!@#$%^&*|\\/<>?~0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  const el      = document.getElementById('hero-title');
  const target  = 'LINUX';
  el.textContent = Array.from(target).map(() => glyphs[Math.floor(Math.random()*glyphs.length)]).join('');
  const start = performance.now();
  const dur   = 1200;
  function step(now) {
    const p   = Math.min((now-start)/dur, 1);
    const len = Math.floor(p * target.length);
    let str = '';
    for (let i=0;i<target.length;i++) {
      str += i < len ? target[i] : glyphs[Math.floor(Math.random()*glyphs.length)];
    }
    el.textContent = str;
    el.setAttribute('data-text', str);
    if (p < 1) requestAnimationFrame(step);
    else { el.textContent = target; el.setAttribute('data-text', target); }
  }
  setTimeout(() => requestAnimationFrame(step), 500);
})();

/* ══════════════════════════════════════════════════════
   BILINGUAL I18N SYSTEM (ENGLISH & INDONESIAN)
══════════════════════════════════════════════════════ */
const TRANSLATIONS = {
  en: {
    nav_home: '~/home',
    nav_what: 'What',
    nav_creator: 'Creator',
    nav_distros: 'Distros',
    nav_uses: 'Uses',
    hero_prompt: 'echo "Welcome to the Linux Universe"',
    hero_title_tag: '// KERNEL ARCHITECTURE //',
    hero_title_bar: 'THE OPEN SOURCE REVOLUTION',
    hero_subtitle: 'The <span class="hl">free &amp; open-source</span> kernel powering everything from your pocket to <span class="hl">outer space</span>.',
    hero_cta_explore: '$ explore --all',
    hero_cta_distros: '$ list distros',
    what_label: '01 — Definition',
    what_title: 'What is <span class="accent">Linux</span>?',
    what_desc: 'More than an OS — a philosophy of freedom, transparency, and community-driven power.',
    what_p1: '<strong>Linux</strong> is an open-source, Unix-like <span class="g">operating system kernel</span> first released in 1991 by Linus Torvalds. The kernel manages hardware, memory, processes, and system calls for the entire OS.',
    what_p2: 'When people say "Linux" they usually mean a full <span class="g">distribution</span>: kernel + package manager + desktop environment + system tools — all bundled into one installable OS.',
    what_p3: 'Released under the <strong>GNU General Public License (GPL)</strong>, anyone can read, modify, and distribute Linux freely. This radical openness sparked a global developer revolution and made Linux the <span class="g">backbone of the modern internet</span>.',
    what_p4: '<strong>96.4%</strong> of the world\'s top web servers, <strong>all 500</strong> supercomputers, and <strong>Android</strong> (3.6 billion users) run Linux today.',
    stat_web: 'of Top Web Servers',
    stat_supercomputers: 'of 500 Supercomputers',
    stat_android: 'Android Users',
    stat_year: 'Year Linux Was Born',
    creator_label: '02 — Creator',
    creator_title: 'Who <span class="accent">Created</span> Linux?',
    creator_desc: 'A Finnish student\'s weekend project that changed the entire world.',
    creator_born: 'Born: December 28, 1969 — Helsinki, Finland',
    creator_p1: 'On <strong>August 25, 1991</strong>, Linus posted a legendary message: <span class="g">"I\'m doing a (free) OS… just a hobby, won\'t be big and professional."</span> That hobby became the most important software in history.',
    creator_p2: 'Today Linus still leads the kernel project as its principal developer — assisted by thousands worldwide — and in 2005 also created <strong>Git</strong>, the version control system used by virtually every software project on Earth.',
    creator_tl_1991: 'Released Linux kernel 0.01 to the public',
    creator_tl_1992: 'Adopted GPL — made Linux truly free and open',
    creator_tl_1994: 'Linux kernel 1.0 — first stable release',
    creator_tl_2005: 'Created Git — world\'s most used version control system',
    creator_tl_2024: 'Linux 6.x — still actively developed, 33 years on',
    distros_label: '03 — Distributions',
    distros_title: 'Linux <span class="accent">Distros</span>',
    distros_desc: 'One kernel, hundreds of flavors. Use the arrows to browse each distribution.',
    distro_ubuntu_base: 'Base: Debian · Sponsor: Canonical',
    distro_ubuntu_desc: 'The world\'s most popular Linux desktop distro. Beginner-friendly with Long-Term Support (LTS) releases every 2 years. The standard for newcomers and cloud servers alike.',
    distro_fedora_base: 'Base: Independent (RPM) · Sponsor: Red Hat',
    distro_fedora_desc: 'Cutting-edge open-source features with a clean GNOME desktop. Preferred by developers who want the freshest stable software without chasing bleeding-edge rolling releases.',
    distro_arch_base: 'Base: Independent · Type: Rolling Release',
    distro_arch_desc: 'Build your OS entirely from scratch. Minimalist, fast, and community-driven. Home to the legendary Arch Wiki. Beloved by power users. <em>"I use Arch btw."</em>',
    distro_kali_base: 'Base: Debian · Purpose: Security',
    distro_kali_desc: 'The penetration testing &amp; cybersecurity distro, shipping with 600+ pre-installed security tools. Used by ethical hackers, forensic investigators, and security researchers worldwide.',
    distro_debian_base: 'Base: Independent · Type: Stable',
    distro_debian_desc: 'The mother of many distros, including Ubuntu and Kali. Extreme stability over new features. The gold standard for production servers where reliability is non-negotiable.',
    distro_mint_base: 'Base: Ubuntu / Debian',
    distro_mint_desc: 'The Windows switcher\'s best friend. Familiar Cinnamon desktop, polished UX, rock-stable. Perfect for everyday computing without any learning curve.',
    distro_suse_base: 'Base: Independent · German Engineering',
    distro_suse_desc: 'Enterprise-grade power with two variants: Leap (stable) and Tumbleweed (rolling). YaST control center makes system administration intuitive. Used heavily in enterprise environments.',
    distro_rocky_base: 'Base: RHEL-compatible',
    distro_rocky_desc: 'The spiritual successor to CentOS — community-built, RHEL-compatible, enterprise-grade. Trusted for mission-critical server and data-center deployments worldwide.',
    uses_label: '04 — Use Cases',
    uses_title: 'What is Linux <span class="accent">Used For</span>?',
    uses_desc: 'From your pocket to outer space — Linux is literally everywhere.',
    uses_web_title: 'Web Servers',
    uses_web_desc: 'Apache, Nginx, and virtually every major web server runs on Linux. Google, Amazon, Facebook, and Netflix all serve billions from Linux clusters.',
    uses_android_title: 'Android / Mobile',
    uses_android_desc: 'Android is built on the Linux kernel. Every Samsung, Pixel, or Xiaomi phone is running Linux — that\'s 3.6 billion people, every single day.',
    uses_space_title: 'Space & Science',
    uses_space_desc: 'The ISS, NASA Mars rovers, and SpaceX Falcon 9 all rely on Linux for mission-critical real-time computing. Even the Ingenuity Mars helicopter uses Linux.',
    uses_ai_title: 'AI & Supercomputing',
    uses_ai_desc: 'All 500 of the world\'s top supercomputers run Linux. ChatGPT, Gemini, and every major AI model was trained on Linux-powered GPU clusters.',
    uses_security_title: 'Cybersecurity',
    uses_security_desc: 'Ethical hackers and security professionals use Kali Linux, Parrot OS, and others to protect networks, uncover vulnerabilities, and conduct forensic analysis.',
    uses_cloud_title: 'Cloud Computing',
    uses_cloud_desc: 'AWS, Google Cloud, and Azure all run Linux VMs as their primary workload. Kubernetes, Docker, and all container tech is fundamentally Linux-native.',
    uses_desktop_title: 'Desktop Computing',
    uses_desktop_desc: 'From lightweight distros on old hardware to powerful developer workstations — Linux desktops offer speed, privacy, and total customization.',
    uses_iot_title: 'Embedded & IoT',
    uses_iot_desc: 'Smart TVs, routers, cars, cameras, and medical devices run embedded Linux. Its tiny footprint and extreme stability make it ideal for IoT hardware.',
    footer_built: 'Built with <span class="g">❤</span> for the open-source community',
    footer_interactive: 'Interactive',
    cmd_placeholder: 'Type command (try: help, who am i, ask <topic>, monyong, 1 seed, lang id)...',
  },
  id: {
    nav_home: '~/beranda',
    nav_what: 'Definisi',
    nav_creator: 'Pencipta',
    nav_distros: 'Distro',
    nav_uses: 'Kegunaan',
    hero_prompt: 'echo "Selamat Datang di Semesta Linux"',
    hero_title_tag: '// ARSITEKTUR KERNEL //',
    hero_title_bar: 'REVOLUSI SUMBER TERBUKA',
    hero_subtitle: 'Kernel <span class="hl">gratis &amp; sumber terbuka</span> yang menggerakkan segalanya dari saku Anda hingga ke <span class="hl">luar angkasa</span>.',
    hero_cta_explore: '$ jelajahi --semua',
    hero_cta_distros: '$ daftar distro',
    what_label: '01 — Definisi',
    what_title: 'Apa itu <span class="accent">Linux</span>?',
    what_desc: 'Lebih dari sekadar OS — sebuah filosofi kebebasan, transparansi, dan kekuatan berbasis komunitas.',
    what_p1: '<strong>Linux</strong> adalah <span class="g">kernel sistem operasi</span> mirip Unix yang bersifat open-source, pertama kali dirilis tahun 1991 oleh Linus Torvalds. Kernel bertugas mengelola perangkat keras, memori, proses, dan panggilan sistem untuk seluruh OS.',
    what_p2: 'Saat orang menyebut "Linux", biasanya yang dimaksud adalah <span class="g">distribusi</span> lengkap: kernel + manajer paket + lingkungan desktop + peralatan sistem — semuanya dibundel menjadi satu OS siap pasang.',
    what_p3: 'Dirilis di bawah <strong>GNU General Public License (GPL)</strong>, siapa saja bebas membaca, memodifikasi, dan mendistribusikan Linux. Keterbukaan radikal ini memicu revolusi developer global dan menjadikan Linux <span class="g">tulang punggung internet modern</span>.',
    what_p4: '<strong>96.4%</strong> server web teratas di dunia, <strong>seluruh 500</strong> superkomputer, dan <strong>Android</strong> (3.6 miliar pengguna) berjalan di atas Linux hari ini.',
    stat_web: 'Server Web Teratas Dunia',
    stat_supercomputers: 'dari 500 Superkomputer',
    stat_android: 'Pengguna Aktif Android',
    stat_year: 'Tahun Kelahiran Linux',
    creator_label: '02 — Pencipta',
    creator_title: 'Siapa <span class="accent">Pencipta</span> Linux?',
    creator_desc: 'Proyek akhir pekan seorang mahasiswa Finlandia yang mengubah dunia selamanya.',
    creator_born: 'Lahir: 28 Desember 1969 — Helsinki, Finlandia',
    creator_p1: 'Pada <strong>25 Agustus 1991</strong>, Linus mengunggah pesan legendaris: <span class="g">"Saya sedang membuat OS (gratis)... hanya hobi, tidak akan besar dan profesional."</span> Hobi tersebut menjelma menjadi perangkat lunak terpenting dalam sejarah peradaban modern.',
    creator_p2: 'Hingga kini Linus tetap memimpin proyek kernel sebagai pengembang utamanya — dibantu ribuan kontributor dunia — dan pada tahun 2005 juga menciptakan <strong>Git</strong>, sistem kontrol versi yang dipakai hampir seluruh proyek software di bumi.',
    creator_tl_1991: 'Merilis Linux kernel 0.01 ke publik',
    creator_tl_1992: 'Mengadopsi GPL — membuat Linux benar-benar bebas & terbuka',
    creator_tl_1994: 'Linux kernel 1.0 — rilis stabil pertama resmi meluncur',
    creator_tl_2005: 'Menciptakan Git — sistem kontrol versi paling populer di dunia',
    creator_tl_2024: 'Linux 6.x — masih aktif dikembangkan hingga kini, 33+ tahun berkarya',
    distros_label: '03 — Distribusi',
    distros_title: '<span class="accent">Distro</span> Linux',
    distros_desc: 'Satu kernel, ratusan varian. Gunakan tombol panah untuk menelusuri setiap distribusi.',
    distro_ubuntu_base: 'Basis: Debian · Sponsor: Canonical',
    distro_ubuntu_desc: 'Distro desktop Linux terpopuler di dunia. Sangat ramah pemula dengan rilis Long-Term Support (LTS) setiap 2 tahun. Standar emas untuk pemula maupun server cloud.',
    distro_fedora_base: 'Basis: Independen (RPM) · Sponsor: Red Hat',
    distro_fedora_desc: 'Fitur open-source mutakhir dengan desktop GNOME yang bersih. Menjadi pilihan favorit pengembang yang menginginkan teknologi terbaru yang tetap stabil.',
    distro_arch_base: 'Basis: Independen · Tipe: Rolling Release',
    distro_arch_desc: 'Bangun sistem operasi Anda sendiri dari nol. Minimalis, super cepat, dan digerakkan komunitas. Memiliki Arch Wiki legendaris dan dicintai power user. <em>"I use Arch btw."</em>',
    distro_kali_base: 'Basis: Debian · Tujuan: Keamanan Siber',
    distro_kali_desc: 'Distro pengujian penetrasi & keamanan siber dengan 600+ tools keamanan siap pakai. Digunakan oleh ethical hacker, penyidik forensik, dan peneliti keamanan di seluruh dunia.',
    distro_debian_base: 'Basis: Independen · Tipe: Stabil',
    distro_debian_desc: 'Induk dari banyak distro termasuk Ubuntu dan Kali. Mengutamakan stabilitas ekstrem di atas segalanya. Menjadi standar emas untuk server produksi yang menuntut keandalan tanpa henti.',
    distro_mint_base: 'Basis: Ubuntu / Debian',
    distro_mint_desc: 'Sahabat terbaik bagi pengguna yang baru pindah dari Windows. Tampilan Cinnamon yang familiar, antarmuka rapi, dan sangat stabil untuk pemakaian sehari-hari tanpa perlu adaptasi rumit.',
    distro_suse_base: 'Basis: Independen · Rekayasa Jerman',
    distro_suse_desc: 'Kekuatan kelas enterprise dengan dua varian: Leap (stabil) dan Tumbleweed (rolling). Pusat kontrol YaST membuat administrasi sistem sangat intuitif dan andal.',
    distro_rocky_base: 'Basis: Kompatibel RHEL',
    distro_rocky_desc: 'Penerus sejati CentOS — dibangun komunitas, 100% kompatibel dengan RHEL, kelas korporat. Sangat dipercaya untuk server mission-critical dan pusat data di seluruh dunia.',
    uses_label: '04 — Bidang Penggunaan',
    uses_title: 'Untuk Apa Saja Linux <span class="accent">Digunakan</span>?',
    uses_desc: 'Dari saku Anda hingga luar angkasa — Linux hadir di mana-mana.',
    uses_web_title: 'Server Web',
    uses_web_desc: 'Apache, Nginx, dan hampir setiap server web utama berjalan di Linux. Google, Amazon, Facebook, dan Netflix melayani miliaran pengguna dari kluster Linux.',
    uses_android_title: 'Android / Ponsel Pintar',
    uses_android_desc: 'Android dibangun langsung di atas kernel Linux. Setiap ponsel Samsung, Pixel, atau Xiaomi menjalankan Linux — dipakai oleh lebih dari 3.6 miliar orang setiap hari.',
    uses_space_title: 'Antariksa & Sains',
    uses_space_desc: 'Stasiun Luar Angkasa Internasional (ISS), rover Mars milik NASA, dan roket Falcon 9 SpaceX mengandalkan Linux untuk komputasi real-time yang krusial. Bahkan helikopter Ingenuity di Mars ditenagai Linux.',
    uses_ai_title: 'AI & Superkomputer',
    uses_ai_desc: 'Seluruh 500 superkomputer tercepat di dunia menggunakan Linux. ChatGPT, Gemini, dan model AI terbesar dilatih di atas ribuan kluster GPU bertenaga Linux.',
    uses_security_title: 'Keamanan Siber',
    uses_security_desc: 'Ethical hacker dan pakar keamanan menggunakan Kali Linux, Parrot OS, dan alat berbasis Linux lainnya untuk melindungi jaringan, menguji celah kerentanan, dan menganalisis forensik digital.',
    uses_cloud_title: 'Komputasi Awan (Cloud)',
    uses_cloud_desc: 'AWS, Google Cloud, dan Microsoft Azure menjalankan mesin virtual Linux sebagai beban kerja utama. Kubernetes, Docker, dan seluruh teknologi container adalah teknologi asli Linux.',
    uses_desktop_title: 'Komputer Desktop & Laptop',
    uses_desktop_desc: 'Mulai dari distro super ringan untuk komputer lawas hingga workstation bertenaga tinggi untuk developer — desktop Linux memberikan kecepatan, privasi penuh, dan kebebasan kustomisasi tanpa batas.',
    uses_iot_title: 'Perangkat Tertanam & IoT',
    uses_iot_desc: 'Smart TV, router Wi-Fi, mobil pintar, kamera keamanan, dan peralatan medis ditenagai Linux tertanam (embedded). Ukurannya yang ringan dan stabilitasnya yang tinggi menjadikannya pilihan utama industri.',
    footer_built: 'Dibuat dengan <span class="g">❤</span> untuk komunitas open-source',
    footer_interactive: 'Interaktif',
    cmd_placeholder: 'Ketik perintah (coba: help, who am i, tanya apa itu kernel, monyong, 1 seed, lang en)...',
  }
};

const DISTRO_TAGS = {
  en: {
    ubuntu: ['beginner', 'desktop', 'server', 'LTS'],
    fedora: ['developer', 'cutting-edge', 'GNOME'],
    arch: ['advanced', 'rolling', 'minimal', 'AUR'],
    kali: ['security', 'pentesting', 'forensics'],
    debian: ['stable', 'server', 'parent-distro'],
    mint: ['beginner', 'Windows-like', 'Cinnamon'],
    suse: ['enterprise', 'stable', 'rolling'],
    rocky: ['enterprise', 'server', 'RHEL-compat'],
  },
  id: {
    ubuntu: ['pemula', 'desktop', 'server', 'LTS'],
    fedora: ['pengembang', 'mutakhir', 'GNOME'],
    arch: ['tingkat-lanjut', 'rolling', 'minimalis', 'AUR'],
    kali: ['keamanan', 'pentest', 'forensik'],
    debian: ['stabil', 'server', 'induk-distro'],
    mint: ['pemula', 'mirip-Windows', 'Cinnamon'],
    suse: ['enterprise', 'stabil', 'rolling'],
    rocky: ['enterprise', 'server', 'kompatibel-RHEL'],
  }
};

const CMD_SUMMARIES_ID = {
  hero: {
    title: 'bash — ~/beranda',
    lines: [
      { type:'cmd',  text:'cat /home/linux/README.md' },
      { type:'out',  text:'──────────────────────────────────────────' },
      { type:'hl',   text:'🐧  LINUX — Revolusi Sumber Terbuka' },
      { type:'out',  text:'' },
      { type:'out',  text:'Selamat datang di pusat pengetahuan interaktif Linux.' },
      { type:'out',  text:'Navigasikan via menu atas atau gulir untuk menjelajahi:' },
      { type:'out',  text:'' },
      { type:'warn', text:'  [1] APA ITU LINUX?  → kernel, OS, kebebasan' },
      { type:'warn', text:'  [2] PENCIPTA        → Linus Torvalds, 1991' },
      { type:'warn', text:'  [3] DISTRIBUSI      → Ubuntu, Arch, Kali...' },
      { type:'warn', text:'  [4] KEGUNAAN        → server, AI, antariksa & lainnya' },
      { type:'out',  text:'' },
      { type:'cmd',  text:'echo "Mari jelajahi!" && ./mulai.sh' },
      { type:'hl',   text:'> Mode interaktif dimulai... berhasil ✓' },
    ]
  },
  what: {
    title: 'bash — bagian: Apa itu Linux',
    lines: [
      { type:'cmd',  text:'man linux | head -60' },
      { type:'out',  text:'──────────────────────────────────────────' },
      { type:'hl',   text:'LINUX(1)        Kernel Sumber Terbuka     LINUX(1)' },
      { type:'out',  text:'' },
      { type:'out',  text:'NAMA' },
      { type:'hl',   text:'    Linux — Kernel sistem operasi mirip UNIX bebas & terbuka' },
      { type:'out',  text:'' },
      { type:'out',  text:'DESKRIPSI' },
      { type:'out',  text:'    Dibuat pertama kali tahun 1991 oleh Linus Torvalds,' },
      { type:'out',  text:'    kernel Linux mengelola hardware, memori & panggilan sistem.' },
      { type:'out',  text:'' },
      { type:'out',  text:'FAKTA KUNCI' },
      { type:'warn', text:'    Lisensi  : GNU General Public License (GPL v2)' },
      { type:'warn', text:'    Bahasa   : C (utama), Assembly' },
      { type:'warn', text:'    Server   : 96% dari 1 juta web server teratas' },
      { type:'warn', text:'    Mobile   : Android (3.6 miliar pengguna aktif)' },
      { type:'warn', text:'    Superkom : 100% dari TOP500 superkomputer dunia' },
      { type:'out',  text:'' },
      { type:'cmd',  text:'echo $KEBEBASAN' },
      { type:'hl',   text:'> Tanpa batas. Itulah esensinya. ✓' },
    ]
  },
  creator: {
    title: 'bash — bagian: Pencipta',
    lines: [
      { type:'cmd',  text:'git log --author="Linus Torvalds" --oneline | head -10' },
      { type:'out',  text:'──────────────────────────────────────────' },
      { type:'hl',   text:'LINUS BENEDICT TORVALDS' },
      { type:'out',  text:'Lahir: 28 Desember 1969 — Helsinki, Finlandia' },
      { type:'out',  text:'' },
      { type:'warn', text:'[1991] Mengunggah pesan legendaris "hanya sebuah hobi"' },
      { type:'warn', text:'       Merilis Linux kernel v0.01 ke penjuru dunia' },
      { type:'warn', text:'[1992] Mengadopsi GPL — Linux bebas untuk selamanya' },
      { type:'warn', text:'[1994] Linux 1.0: Rilis resmi stabil pertama' },
      { type:'warn', text:'[2005] Menciptakan Git — dipakai 100 juta+ pengembang' },
      { type:'warn', text:'[2024] Linux 6.x — masih aktif memimpin pengembangan di usia 54' },
      { type:'out',  text:'' },
      { type:'out',  text:'Kontribusi: 30.000+ commit selama 33 tahun' },
      { type:'out',  text:'Juga menciptakan: Git (sistem VCS terpopuler di bumi)' },
      { type:'out',  text:'' },
      { type:'cmd',  text:'echo "Dampak Linus Torvalds"' },
      { type:'hl',   text:'> Mengubah dunia komputasi lewat proyek akhir pekan. ✓' },
    ]
  },
  distros: {
    title: 'bash — bagian: Distribusi Linux',
    lines: [
      { type:'cmd',  text:'apt list --available | wc -l' },
      { type:'hl',   text:'> 600+ distribusi Linux aktif di seluruh dunia' },
      { type:'out',  text:'──────────────────────────────────────────' },
      { type:'out',  text:'' },
      { type:'out',  text:'DISTRO POPULER (geser kartu untuk menjelajah):' },
      { type:'out',  text:'' },
      { type:'warn', text:'🟠 Ubuntu    — Paling cocok untuk pemula & cloud server' },
      { type:'warn', text:'🎩 Fedora    — Paling cocok untuk pengembang software' },
      { type:'warn', text:'🏹 Arch      — Paling digemari power user (DIY / rolling)' },
      { type:'warn', text:'🐉 Kali      — Khusus pengujian keamanan & pentesting' },
      { type:'warn', text:'🐧 Debian    — Standar emas stabilitas server produksi' },
      { type:'warn', text:'🍃 Mint      — Pilihan tepat bagi pengguna transisi Windows' },
      { type:'warn', text:'🔵 openSUSE  — Andal untuk kebutuhan korporat enterprise' },
      { type:'warn', text:'🛡️ Rocky     — Alternatif server kompatibel 100% RHEL' },
      { type:'out',  text:'' },
      { type:'out',  text:'TIPS: Gunakan tombol ◀ ▶ atau panah keyboard.' },
      { type:'cmd',  text:'echo "Pilihlah distro sesuai filosofimu."' },
      { type:'hl',   text:'> Tidak ada pilihan salah. Hanya perbedaan filosofi. ✓' },
    ]
  },
  uses: {
    title: 'bash — bagian: Kegunaan Linux',
    lines: [
      { type:'cmd',  text:'find / -name "linux" -type f 2>/dev/null | wc -l' },
      { type:'hl',   text:'> Di mana-mana. Secara harfiah.' },
      { type:'out',  text:'──────────────────────────────────────────' },
      { type:'out',  text:'' },
      { type:'out',  text:'LINUX DIGUNAKAN UNTUK...' },
      { type:'out',  text:'' },
      { type:'warn', text:'🌐 Server Web     — 96% dari 1 juta web teratas (Nginx/Apache)' },
      { type:'warn', text:'🤖 Android        — 3.6 miliar ponsel, semuanya kernel Linux' },
      { type:'warn', text:'🚀 Luar Angkasa   — ISS, roket SpaceX, NASA Mars Rover' },
      { type:'warn', text:'🧠 AI & HPC       — Pelatihan ChatGPT, seluruh 500 superkomputer' },
      { type:'warn', text:'🔐 Keamanan Siber — Kali Linux, pertahanan & audit keamanan' },
      { type:'warn', text:'☁️  Cloud          — VM utama AWS, Google Cloud, Microsoft Azure' },
      { type:'warn', text:'🖥️  Desktop        — OS komprehensif untuk kerja & gaming' },
      { type:'warn', text:'📺 IoT & Hardware — Smart TV, router Wi-Fi, mobil listrik, medis' },
      { type:'out',  text:'' },
      { type:'cmd',  text:'echo "Adakah perangkat yang tidak menjalankan Linux?"' },
      { type:'hl',   text:'> Windows. Tapi Windows pun kini memakai WSL (subsistem Linux). ✓' },
    ]
  }
};

let currentLang = localStorage.getItem('linux_lang') || 'en';
let activeSectionKey = 'hero';

function setLanguage(lang) {
  currentLang = (lang === 'id' || lang === 'en') ? lang : 'en';
  localStorage.setItem('linux_lang', currentLang);

  // Update badge UI
  const badgeEn = document.getElementById('lang-badge-en');
  const badgeId = document.getElementById('lang-badge-id');
  if (badgeEn && badgeId) {
    badgeEn.classList.toggle('active', currentLang === 'en');
    badgeId.classList.toggle('active', currentLang === 'id');
  }

  // Update all translatable elements
  const dict = TRANSLATIONS[currentLang];
  if (dict) {
    document.querySelectorAll('[data-i18n]').forEach(el => {
      const key = el.getAttribute('data-i18n');
      if (dict[key]) {
        el.innerHTML = dict[key];
      }
    });
  }

  // Update Distro tags
  const tagsDict = DISTRO_TAGS[currentLang];
  if (tagsDict) {
    document.querySelectorAll('.distro-tags[data-distro]').forEach(container => {
      const distro = container.getAttribute('data-distro');
      const tags = tagsDict[distro];
      if (tags) {
        container.innerHTML = tags.map(t => `<span class="tag">${t}</span>`).join('');
      }
    });
  }

  // Update input placeholder
  const inputEl = document.getElementById('cmd-user-input');
  if (inputEl && dict && dict.cmd_placeholder) {
    inputEl.placeholder = dict.cmd_placeholder;
  }

  // If CMD popup window is currently visible, refresh its summary content
  const win = document.getElementById('cmd-window');
  if (win && win.classList.contains('show') && activeSectionKey) {
    openCmd(activeSectionKey);
  }
}

function toggleLanguage() {
  triggerVhsGlitch(420);
  const next = currentLang === 'en' ? 'id' : 'en';
  setLanguage(next);
}

/* ══════════════════════════════════════════════════════
   CMD PROMPT POPUP SYSTEM
   Fires when any nav link (or CTA) is clicked.
   Types out an interactive summary of the target section.
══════════════════════════════════════════════════════ */
const CMD_SUMMARIES = {
  hero: {
    title: 'bash — ~/home',
    lines: [
      { type:'cmd',  text:'cat /home/linux/README.md' },
      { type:'out',  text:'──────────────────────────────────────────' },
      { type:'hl',   text:'🐧  LINUX — The Open Source Revolution' },
      { type:'out',  text:'' },
      { type:'out',  text:'Welcome to the interactive Linux knowledge base.' },
      { type:'out',  text:'Navigate using the top menu or scroll to explore:' },
      { type:'out',  text:'' },
      { type:'warn', text:'  [1] WHAT IS LINUX?  → kernel, OS, freedom' },
      { type:'warn', text:'  [2] CREATOR        → Linus Torvalds, 1991' },
      { type:'warn', text:'  [3] DISTROS        → Ubuntu, Arch, Kali...' },
      { type:'warn', text:'  [4] USES           → servers, AI, space & more' },
      { type:'out',  text:'' },
      { type:'cmd',  text:'echo "Let\'s explore!" && ./start.sh' },
      { type:'hl',   text:'> Launching interactive mode... done ✓' },
    ]
  },
  what: {
    title: 'bash — section: What is Linux',
    lines: [
      { type:'cmd',  text:'man linux | head -60' },
      { type:'out',  text:'──────────────────────────────────────────' },
      { type:'hl',   text:'LINUX(1)        Open Source Kernel        LINUX(1)' },
      { type:'out',  text:'' },
      { type:'out',  text:'NAME' },
      { type:'hl',   text:'    Linux — A free, open-source UNIX-like OS kernel' },
      { type:'out',  text:'' },
      { type:'out',  text:'DESCRIPTION' },
      { type:'out',  text:'    First created in 1991 by Linus Torvalds, the Linux' },
      { type:'out',  text:'    kernel manages hardware, processes, memory & I/O.' },
      { type:'out',  text:'' },
      { type:'out',  text:'KEY FACTS' },
      { type:'warn', text:'    License  : GNU General Public License (GPL v2)' },
      { type:'warn', text:'    Language : C (primary), Assembly' },
      { type:'warn', text:'    Servers  : 96% of top 1M web servers' },
      { type:'warn', text:'    Mobile   : Android (3.6 billion users)' },
      { type:'warn', text:'    HPC      : 100% of TOP500 supercomputers' },
      { type:'out',  text:'' },
      { type:'cmd',  text:'echo $FREEDOM' },
      { type:'hl',   text:'> Infinite. That\'s the point. ✓' },
    ]
  },
  creator: {
    title: 'bash — section: Creator',
    lines: [
      { type:'cmd',  text:'git log --author="Linus Torvalds" --oneline | head -10' },
      { type:'out',  text:'──────────────────────────────────────────' },
      { type:'hl',   text:'LINUS BENEDICT TORVALDS' },
      { type:'out',  text:'Born: December 28, 1969 — Helsinki, Finland' },
      { type:'out',  text:'' },
      { type:'warn', text:'[1991] Posted the legendary "just a hobby" message' },
      { type:'warn', text:'       Released Linux kernel v0.01 to the world' },
      { type:'warn', text:'[1992] Adopted GPL — Linux became truly free forever' },
      { type:'warn', text:'[1994] Linux 1.0: First stable official release' },
      { type:'warn', text:'[2005] Created Git — now used by 100M+ developers' },
      { type:'warn', text:'[2024] Linux 6.x — still leading development at 54' },
      { type:'out',  text:'' },
      { type:'out',  text:'Contributions: 30,000+ commits over 33 years' },
      { type:'out',  text:'Also created: Git (world\'s most used VCS)' },
      { type:'out',  text:'' },
      { type:'cmd',  text:'echo "Torvalds\' impact"' },
      { type:'hl',   text:'> Changed computing forever with a weekend project. ✓' },
    ]
  },
  distros: {
    title: 'bash — section: Distributions',
    lines: [
      { type:'cmd',  text:'apt list --available | wc -l' },
      { type:'hl',   text:'> 600+ active Linux distributions worldwide' },
      { type:'out',  text:'──────────────────────────────────────────' },
      { type:'out',  text:'' },
      { type:'out',  text:'POPULAR DISTROS (swipe → to browse all cards)' },
      { type:'out',  text:'' },
      { type:'warn', text:'🟠 Ubuntu    — Best for beginners & servers' },
      { type:'warn', text:'🎩 Fedora    — Best for developers (cutting-edge)' },
      { type:'warn', text:'🏹 Arch      — Best for power users (DIY / rolling)' },
      { type:'warn', text:'🐉 Kali      — Best for security & pentesting' },
      { type:'warn', text:'🐧 Debian    — Best for stability & servers' },
      { type:'warn', text:'🍃 Mint      — Best for Windows switchers' },
      { type:'warn', text:'🔵 openSUSE  — Best for enterprise' },
      { type:'warn', text:'🛡️ Rocky     — Best for RHEL-compatible servers' },
      { type:'out',  text:'' },
      { type:'out',  text:'TIP: Use ◀ ▶ buttons or ← → keyboard to browse.' },
      { type:'cmd',  text:'echo "Choose your distro wisely."' },
      { type:'hl',   text:'> There is no wrong answer. Only different philosophies. ✓' },
    ]
  },
  uses: {
    title: 'bash — section: Use Cases',
    lines: [
      { type:'cmd',  text:'find / -name "linux" -type f 2>/dev/null | wc -l' },
      { type:'hl',   text:'> Everywhere. Literally.' },
      { type:'out',  text:'──────────────────────────────────────────' },
      { type:'out',  text:'' },
      { type:'out',  text:'LINUX IS USED FOR...' },
      { type:'out',  text:'' },
      { type:'warn', text:'🌐 Web Servers    — 96% of top 1M sites (Nginx/Apache)' },
      { type:'warn', text:'🤖 Android        — 3.6 billion phones, all Linux kernel' },
      { type:'warn', text:'🚀 Space          — ISS, SpaceX, NASA Mars Rovers' },
      { type:'warn', text:'🧠 AI & HPC       — ChatGPT, all 500 supercomputers' },
      { type:'warn', text:'🔐 Cybersecurity  — Kali Linux, ethical hacking' },
      { type:'warn', text:'☁️  Cloud          — AWS, GCP, Azure Linux VMs' },
      { type:'warn', text:'🖥️  Desktop        — Full OS for work & gaming' },
      { type:'warn', text:'📺 Embedded/IoT   — TVs, routers, cars, cameras' },
      { type:'out',  text:'' },
      { type:'cmd',  text:'echo "Is there anything Linux doesn\'t run?"' },
      { type:'hl',   text:'> Windows. But even that uses WSL (Linux subsystem). ✓' },
    ]
  },
};

/* ══════════════════════════════════════════════════════
   VHS GLITCH TRANSITION
   - Triggers on page boot and section transitions
 ══════════════════════════════════════════════════════ */
const vhsOverlay = document.getElementById('vhs-overlay');
const vhsTimer   = document.getElementById('vhs-timer');
let vhsSeconds   = 1;

function updateVhsTimer() {
  const m = String(Math.floor(vhsSeconds / 60)).padStart(2, '0');
  const s = String(vhsSeconds % 60).padStart(2, '0');
  if (vhsTimer) vhsTimer.textContent = `00:${m}:${s}`;
  vhsSeconds++;
}
setInterval(updateVhsTimer, 1000);

function triggerVhsGlitch(duration = 750, callback = null) {
  if (!vhsOverlay) { if (callback) callback(); return; }
  vhsOverlay.classList.add('active');
  setTimeout(() => {
    vhsOverlay.classList.remove('active');
    if (callback) callback();
  }, duration);
}

// Trigger retro VHS boot on load
window.addEventListener('DOMContentLoaded', () => {
  triggerVhsGlitch(900);
});

/* ══════════════════════════════════════════════════════
   DRAGGABLE CMD WINDOW SYSTEM
   - Move everywhere like a desktop OS window
   - Minimizable, non-blocking floating window
 ══════════════════════════════════════════════════════ */
const cmdWin   = document.getElementById('cmd-window');
const titlebar = document.getElementById('cmd-titlebar');
const cmdBody  = document.getElementById('cmd-body');
const cmdTitle = document.getElementById('cmd-title');
let   cmdTyping= null;

let isDragging = false;
let dragOffX   = 0;
let dragOffY   = 0;

// Mouse Drag
titlebar.addEventListener('mousedown', e => {
  if (e.target.closest('.dot')) return; // ignore window buttons
  isDragging = true;
  const rect = cmdWin.getBoundingClientRect();
  dragOffX = e.clientX - rect.left;
  dragOffY = e.clientY - rect.top;
  cmdWin.style.transition = 'none';
});

document.addEventListener('mousemove', e => {
  if (!isDragging) return;
  let left = e.clientX - dragOffX;
  let top  = e.clientY - dragOffY;
  
  const maxLeft = window.innerWidth - cmdWin.offsetWidth - 10;
  const maxTop  = window.innerHeight - 50;
  left = Math.max(10, Math.min(left, maxLeft));
  top  = Math.max(10, Math.min(top, maxTop));

  cmdWin.style.left  = left + 'px';
  cmdWin.style.top   = top + 'px';
  cmdWin.style.right = 'auto';
});

document.addEventListener('mouseup', () => {
  if (isDragging) {
    isDragging = false;
    cmdWin.style.transition = '';
  }
});

// Touch Drag
titlebar.addEventListener('touchstart', e => {
  if (e.target.closest('.dot')) return;
  const touch = e.touches[0];
  isDragging = true;
  const rect = cmdWin.getBoundingClientRect();
  dragOffX = touch.clientX - rect.left;
  dragOffY = touch.clientY - rect.top;
  cmdWin.style.transition = 'none';
}, { passive: true });

document.addEventListener('touchmove', e => {
  if (!isDragging) return;
  const touch = e.touches[0];
  let left = touch.clientX - dragOffX;
  let top  = touch.clientY - dragOffY;
  const maxLeft = window.innerWidth - cmdWin.offsetWidth - 10;
  const maxTop  = window.innerHeight - 50;
  left = Math.max(10, Math.min(left, maxLeft));
  top  = Math.max(10, Math.min(top, maxTop));
  cmdWin.style.left  = left + 'px';
  cmdWin.style.top   = top + 'px';
  cmdWin.style.right = 'auto';
}, { passive: true });

document.addEventListener('touchend', () => {
  if (isDragging) {
    isDragging = false;
    cmdWin.style.transition = '';
  }
});

function toggleMinimizeCmd() {
  cmdWin.classList.toggle('minimized');
}

let isMaximized = false;
let preMaxStyle = null;

function toggleMaximizeCmd() {
  if (!isMaximized) {
    preMaxStyle = {
      left:   cmdWin.style.left,
      top:    cmdWin.style.top,
      width:  cmdWin.style.width,
      height: cmdWin.style.height,
      right:  cmdWin.style.right
    };
    cmdWin.classList.add('maximized');
    isMaximized = true;
  } else {
    cmdWin.classList.remove('maximized');
    if (preMaxStyle) {
      cmdWin.style.left   = preMaxStyle.left;
      cmdWin.style.top    = preMaxStyle.top;
      cmdWin.style.width  = preMaxStyle.width;
      cmdWin.style.height = preMaxStyle.height;
      cmdWin.style.right  = preMaxStyle.right;
    }
    isMaximized = false;
  }
}

function resetCmdPos() {
  cmdWin.classList.remove('minimized', 'maximized');
  isMaximized = false;
  cmdWin.style.top    = '95px';
  cmdWin.style.right  = '40px';
  cmdWin.style.left   = 'auto';
  cmdWin.style.width  = 'min(660px, 94vw)';
  cmdWin.style.height = '440px';
}

// Corner Resize Dragging
const resizeGrip = document.getElementById('win-resize-handle');
let isResizing = false;
let startW = 0, startH = 0, startResizeX = 0, startResizeY = 0;

if (resizeGrip) {
  resizeGrip.addEventListener('mousedown', e => {
    e.stopPropagation();
    isResizing = true;
    startResizeX = e.clientX;
    startResizeY = e.clientY;
    const rect = cmdWin.getBoundingClientRect();
    startW = rect.width;
    startH = rect.height;
    document.body.style.userSelect = 'none';
  });
}

document.addEventListener('mousemove', e => {
  if (!isResizing) return;
  const newW = Math.max(340, Math.min(window.innerWidth * 0.96, startW + (e.clientX - startResizeX)));
  const newH = Math.max(220, Math.min(window.innerHeight * 0.90, startH + (e.clientY - startResizeY)));
  cmdWin.style.width  = newW + 'px';
  cmdWin.style.height = newH + 'px';
});

document.addEventListener('mouseup', () => {
  if (isResizing) {
    isResizing = false;
    document.body.style.userSelect = '';
  }
});

/* ══════════════════════════════════════════════════════
   INTERACTIVE COMMAND LINE INPUT & 'monyong' REDIRECT
 ══════════════════════════════════════════════════════ */
const cmdInput = document.getElementById('cmd-user-input');

cmdWin.addEventListener('click', e => {
  if (!window.getSelection().toString() && cmdInput) {
    cmdInput.focus();
  }
});

if (cmdInput) {
  cmdInput.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
      execUserInput();
    }
  });
}

function execUserInput() {
  if (!cmdInput) return;
  const raw = cmdInput.value.trim();
  if (!raw) return;
  const cmd = raw.toLowerCase();
  cmdInput.value = '';

  // Append user input to terminal output
  const cmdRow = document.createElement('div');
  cmdRow.className = 'cmd-line';
  cmdRow.innerHTML = `<span style="color:var(--green)">user@linux:~$</span> <span style="color:#ffffff">${escapeHtml(raw)}</span>`;
  cmdBody.appendChild(cmdRow);

  // Easter egg: 'monyong' redirects to video page
  if (cmd === 'monyong') {
    const resp = document.createElement('div');
    resp.className = 'cmd-warn';
    resp.innerHTML = `[!] ACCESS GRANTED: EXECUTING PROTOCOL MONYONG...<br><span class="cmd-hl">&gt; Decrypting classified transmission... Launching secret playback.</span>`;
    cmdBody.appendChild(resp);
    cmdBody.scrollTop = cmdBody.scrollHeight;

    triggerVhsGlitch(1200);
    setTimeout(() => {
      window.location.href = '/monyong';
    }, 600);
    return;
  }

  // Easter egg: '1 seed' redirects to 1seed page
  if (cmd === '1 seed' || cmd === '1seed' || cmd === '1-seed') {
    const resp = document.createElement('div');
    resp.className = 'cmd-hl';
    resp.innerHTML = `[!] PROTOCOL 1 SEED RECOGNIZED...<br><span style="color:var(--cyan)">&gt; Synchronizing seed archive transmission... Launching playback.</span>`;
    cmdBody.appendChild(resp);
    cmdBody.scrollTop = cmdBody.scrollHeight;

    triggerVhsGlitch(1200);
    setTimeout(() => {
      window.location.href = '/1seed';
    }, 600);
    return;
  }

  // Language switch command via terminal
  if (cmd === 'lang id' || cmd === 'bahasa' || cmd === 'bahasa indonesia' || cmd === 'indonesia') {
    setLanguage('id');
    const resp = document.createElement('div');
    resp.className = 'cmd-line';
    resp.innerHTML = `[OK] Bahasa antarmuka berhasil diubah ke <strong>Bahasa Indonesia</strong>.`;
    cmdBody.appendChild(resp);
    cmdBody.scrollTop = cmdBody.scrollHeight;
    return;
  }
  if (cmd === 'lang en' || cmd === 'english' || cmd === 'lang english') {
    setLanguage('en');
    const resp = document.createElement('div');
    resp.className = 'cmd-line';
    resp.innerHTML = `[OK] Interface language successfully switched to <strong>English</strong>.`;
    cmdBody.appendChild(resp);
    cmdBody.scrollTop = cmdBody.scrollHeight;
    return;
  }

  // Handle 'who am i' or 'whoami'
  if (cmd === 'who am i' || cmd === 'whoami') {
    const resp = document.createElement('div');
    resp.className = 'cmd-out';
    if (currentLang === 'id') {
      resp.innerHTML = `
        <span class="cmd-hl">UID=0(root) GID=0(root) groups=0(root),27(sudo),1000(linux-enthusiast)</span><br>
        <span style="color:var(--text)">Pengguna:</span> <span class="cmd-warn">Penjelajah Handal &amp; Pelopor Sumber Terbuka</span><br>
        <span style="color:var(--text)">Sesi:</span> <span class="cmd-line">/dev/pts/0 (shell interaktif)</span><br>
        <span style="color:var(--text)">Hak Akses:</span> <span class="cmd-hl">Akses Root Ring-0 Lengkap Diberikan &#10004;</span>
      `;
    } else {
      resp.innerHTML = `
        <span class="cmd-hl">UID=0(root) GID=0(root) groups=0(root),27(sudo),1000(linux-enthusiast)</span><br>
        <span style="color:var(--text)">User:</span> <span class="cmd-warn">Curious Explorer &amp; Open Source Pioneer</span><br>
        <span style="color:var(--text)">Session:</span> <span class="cmd-line">/dev/pts/0 (interactive shell)</span><br>
        <span style="color:var(--text)">Privileges:</span> <span class="cmd-hl">Full Ring-0 Root Access Granted &#10004;</span>
      `;
    }
    cmdBody.appendChild(resp);
    cmdBody.scrollTop = cmdBody.scrollHeight;
    return;
  }

  // Help command
  if (cmd === 'help') {
    const resp = document.createElement('div');
    resp.className = 'cmd-out';
    if (currentLang === 'id') {
      resp.innerHTML = `
        <span class="cmd-hl">Perintah Terminal Interaktif &amp; Tanya Jawab:</span><br>
        &nbsp;&nbsp;<span class="cmd-warn">lang id / lang en</span>&nbsp;- Ganti bahasa (Indonesia / English)<br>
        &nbsp;&nbsp;<span class="cmd-warn">who am i</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Tampilkan identitas user &amp; hak akses root<br>
        &nbsp;&nbsp;<span class="cmd-hl">ask / tanya &lt;topik&gt;</span>- Ajukan pertanyaan seputar Linux<br>
        &nbsp;&nbsp;<span class="cmd-warn">monyong</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- [RAHASIA] Video rahasia terenkripsi<br>
        &nbsp;&nbsp;<span class="cmd-warn">1 seed</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- [RAHASIA] Video transmisi benih<br>
        &nbsp;&nbsp;<span class="cmd-line">what / definisi</span>&nbsp;&nbsp;&nbsp;- Apa itu Linux (kernel &amp; filosofi)<br>
        &nbsp;&nbsp;<span class="cmd-line">creator / pencipta</span>- Profil Linus Torvalds &amp; pembuatan Git<br>
        &nbsp;&nbsp;<span class="cmd-line">distros / distro</span>&nbsp;&nbsp;&nbsp;- Ringkasan distribusi Linux<br>
        &nbsp;&nbsp;<span class="cmd-line">uses / kegunaan</span>&nbsp;&nbsp;&nbsp;- Penerapan nyata (cloud, AI, antariksa)<br>
        &nbsp;&nbsp;<span class="cmd-line">clear</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Bersihkan layar terminal<br>
        &nbsp;&nbsp;<span class="cmd-line">date</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Waktu sistem saat ini<br>
        &nbsp;&nbsp;<span class="cmd-line">exit</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Tutup jendela terminal<br>
        <span style="color:var(--text-dim)">Tips: Coba ketik "tanya apa itu kernel", "distro terbaik", atau "lang en"!</span>
      `;
    } else {
      resp.innerHTML = `
        <span class="cmd-hl">Interactive Terminal Commands &amp; Q&amp;A:</span><br>
        &nbsp;&nbsp;<span class="cmd-warn">lang en / lang id</span>&nbsp;- Switch language (English / Bahasa Indonesia)<br>
        &nbsp;&nbsp;<span class="cmd-warn">who am i</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Show your user identity &amp; root privileges<br>
        &nbsp;&nbsp;<span class="cmd-hl">ask &lt;topic&gt;</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Ask anything about Linux (or type question directly)<br>
        &nbsp;&nbsp;<span class="cmd-warn">monyong</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- [SECRET] Classified video playback<br>
        &nbsp;&nbsp;<span class="cmd-warn">1 seed</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- [SECRET] Classified seed transmission<br>
        &nbsp;&nbsp;<span class="cmd-line">what</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- What is Linux (definition &amp; core)<br>
        &nbsp;&nbsp;<span class="cmd-line">creator</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Linus Torvalds profile &amp; Git creation<br>
        &nbsp;&nbsp;<span class="cmd-line">distros</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Linux distributions overview<br>
        &nbsp;&nbsp;<span class="cmd-line">uses</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Real-world use cases (space, AI, cloud)<br>
        &nbsp;&nbsp;<span class="cmd-line">clear</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Clear screen buffer<br>
        &nbsp;&nbsp;<span class="cmd-line">date</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- System timestamp<br>
        &nbsp;&nbsp;<span class="cmd-line">exit</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Close terminal window<br>
        <span style="color:var(--text-dim)">Tip: Try typing "ask best distro", "what is kernel", or "who created linux"!</span>
      `;
    }
    cmdBody.appendChild(resp);
  } else if (cmd === 'clear') {
    cmdBody.innerHTML = '';
  } else if (['what', 'creator', 'distros', 'uses', 'hero', 'definisi', 'pencipta', 'distro', 'kegunaan', 'beranda'].includes(cmd)) {
    triggerVhsGlitch(650);
    const keyMap = {
      'definisi': 'what',
      'pencipta': 'creator',
      'distro': 'distros',
      'kegunaan': 'uses',
      'beranda': 'hero',
    };
    const targetKey = keyMap[cmd] || cmd;
    const target = document.getElementById(targetKey);
    if (target) target.scrollIntoView({ behavior: 'smooth' });
    openCmd(targetKey);
    return;
  } else if (cmd === 'date') {
    const resp = document.createElement('div');
    resp.className = 'cmd-out';
    resp.textContent = new Date().toUTCString();
    cmdBody.appendChild(resp);
  } else if (cmd === 'exit') {
    closeCmd();
    return;
  } else {
    // Check intelligent Linux topic knowledge base
    const answer = getLinuxTopicAnswer(cmd);
    if (answer) {
      const resp = document.createElement('div');
      resp.className = 'cmd-out';
      resp.innerHTML = answer;
      cmdBody.appendChild(resp);
    } else {
      const resp = document.createElement('div');
      resp.className = 'cmd-err';
      resp.innerHTML = `bash: <strong>${escapeHtml(raw)}</strong>: command not recognized.<br><span style="color:var(--text-dim)">Type <code>help</code> for commands or ask a Linux question (e.g. <code>ask what is kernel</code>, <code>best distro</code>, <code>who created linux</code>).</span>`;
      cmdBody.appendChild(resp);
    }
  }

  cmdBody.scrollTop = cmdBody.scrollHeight;
}

/* ══════════════════════════════════════════════════════
   INTELLIGENT LINUX TOPIC KNOWLEDGE BASE (EN & ID)
 ══════════════════════════════════════════════════════ */
function getLinuxTopicAnswer(q) {
  const s = q.replace(/^(ask|tanya)\s+/i, '').trim().toLowerCase();
  const isId = currentLang === 'id';

  // Kernel topic
  if (s.includes('kernel') || s.includes('inti')) {
    return isId ? `
      <span class="cmd-hl">&#9658; Penjelasan Kernel Linux:</span><br>
      <strong>Kernel</strong> adalah program inti yang berada di pusat sistem operasi. Kernel menjadi jembatan langsung antara aplikasi software dan hardware (CPU, RAM, penyimpanan, kartu jaringan).<br>
      &bull; Mengelola alokasi memori &amp; penjadwalan proses<br>
      &bull; Menangani driver perangkat keras &amp; interupsi sistem<br>
      &bull; Menjaga keamanan dan isolasi hak akses antara user-space &amp; kernel-space
    ` : `
      <span class="cmd-hl">&#9658; Linux Kernel Explanation:</span><br>
      The <strong>kernel</strong> is the core program at the center of the OS. It sits directly between software applications and the hardware (CPU, RAM, storage, network cards).<br>
      &bull; Manages memory allocation &amp; process scheduling<br>
      &bull; Handles device drivers and hardware interruptions<br>
      &bull; Enforces security permissions between user space &amp; kernel space
    `;
  }

  // Linus / Creator
  if (s.includes('linus') || s.includes('creator') || s.includes('who made') || s.includes('who created') || s.includes('pencipta') || s.includes('pembuat') || s.includes('siapa yang buat')) {
    return isId ? `
      <span class="cmd-hl">&#9658; Siapa Pencipta Linux?</span><br>
      Linux diciptakan oleh <strong>Linus Benedict Torvalds</strong>, mahasiswa ilmu komputer di Universitas Helsinki, Finlandia, pada Agustus 1991.<br>
      Awalnya hanya proyek hobi pribadi berbasis MINIX. Saat ini Linus tetap memimpin pengembangan kernel Linux dan pada 2005 juga menciptakan <strong>Git</strong>!
    ` : `
      <span class="cmd-hl">&#9658; Who Created Linux?</span><br>
      Linux was created by <strong>Linus Benedict Torvalds</strong>, a Finnish computer science student in Helsinki, in August 1991.<br>
      He started it as a personal hobby project based on MINIX. Today, Linus still oversees the Linux kernel and also invented <strong>Git</strong> in 2005.
    `;
  }

  // Best Distro / Recommendations
  if (s.includes('best distro') || s.includes('which distro') || s.includes('beginner') || s.includes('recommend') || s.includes('distro terbaik') || s.includes('pemula') || s.includes('rekomendasi')) {
    return isId ? `
      <span class="cmd-hl">&#9658; Rekomendasi Distro Linux:</span><br>
      &bull; <strong>Pemula / Harian:</strong> <span class="cmd-line">Linux Mint</span> (tampilan ramah mirip Windows) atau <span class="cmd-line">Ubuntu</span> (komunitas luas &amp; dokumentasi lengkap)<br>
      &bull; <strong>Developer:</strong> <span class="cmd-line">Fedora</span> (paket software mutakhir &amp; desktop GNOME bersih)<br>
      &bull; <strong>Power User:</strong> <span class="cmd-line">Arch Linux</span> (minimalis DIY, rolling-release)<br>
      &bull; <strong>Keamanan Siber:</strong> <span class="cmd-line">Kali Linux</span> (600+ tools pentest siap pakai)<br>
      &bull; <strong>Server Produksi:</strong> <span class="cmd-line">Debian</span> atau <span class="cmd-line">Rocky Linux</span> (stabilitas tiada tanding)
    ` : `
      <span class="cmd-hl">&#9658; Which Distro Should You Use?</span><br>
      &bull; <strong>Beginners / Daily Use:</strong> <span class="cmd-line">Linux Mint</span> (familiar Windows-like UI) or <span class="cmd-line">Ubuntu</span> (huge community &amp; docs)<br>
      &bull; <strong>Developers:</strong> <span class="cmd-line">Fedora</span> (latest stable packages &amp; clean GNOME)<br>
      &bull; <strong>Power Users:</strong> <span class="cmd-line">Arch Linux</span> (minimal DIY, rolling release)<br>
      &bull; <strong>Security / Pentesting:</strong> <span class="cmd-line">Kali Linux</span> (600+ preloaded tools)<br>
      &bull; <strong>Servers:</strong> <span class="cmd-line">Debian</span> or <span class="cmd-line">Rocky Linux</span> (unmatched stability)
    `;
  }

  // Ubuntu
  if (s.includes('ubuntu')) {
    return isId ? `
      <span class="cmd-hl">&#9658; Ubuntu:</span><br>
      Diciptakan oleh Canonical pada 2004 berbasis Debian. Mempopulerkan Linux pada komputer desktop dengan antarmuka yang ramah dan rilis LTS setiap 2 tahun. Sangat populer untuk server cloud dan workstation AI.
    ` : `
      <span class="cmd-hl">&#9658; Ubuntu:</span><br>
      Created by Canonical in 2004, based on Debian. It popularized Linux on desktops with a user-friendly interface and regular LTS (Long Term Support) releases every 2 years. Very popular for cloud servers and AI workstations.
    `;
  }

  // Arch
  if (s.includes('arch')) {
    return isId ? `
      <span class="cmd-hl">&#9658; Arch Linux:</span><br>
      Distribusi minimalis rolling-release yang mengusung prinsip KISS ("Keep It Simple, Stupid"). Pengguna memasang sistem secara manual dari terminal untuk kendali penuh. Terkenal dengan <em>Arch Wiki</em> dan meme <em>"I use Arch btw"</em>.
    ` : `
      <span class="cmd-hl">&#9658; Arch Linux:</span><br>
      A minimalist, rolling-release distribution built on the KISS principle ("Keep It Simple, Stupid"). Users install everything from the terminal, gaining deep system control. Famous for the <em>Arch Wiki</em> and the meme <em>"I use Arch btw"</em>.
    `;
  }

  // Kali
  if (s.includes('kali') || s.includes('hack') || s.includes('security') || s.includes('pentest') || s.includes('keamanan')) {
    return isId ? `
      <span class="cmd-hl">&#9658; Kali Linux:</span><br>
      Dikembangkan oleh Offensive Security, Kali adalah standar emas sistem operasi untuk pengujian penetrasi (pentesting), forensik digital, dan ethical hacking dengan lebih dari 600 alat keamanan khusus.
    ` : `
      <span class="cmd-hl">&#9658; Kali Linux:</span><br>
      Developed by Offensive Security, Kali is the gold standard OS for penetration testing, digital forensics, and ethical hacking, packed with over 600 specialized security tools.
    `;
  }

  // Debian
  if (s.includes('debian')) {
    return isId ? `
      <span class="cmd-hl">&#9658; Debian:</span><br>
      Salah satu distribusi tertua dan paling dihormati (didirikan 1993). Dijuluki "The Universal Operating System", Debian adalah induk dari Ubuntu, Mint, dan Kali yang terkenal dengan stabilitasnya yang legendaris.
    ` : `
      <span class="cmd-hl">&#9658; Debian:</span><br>
      One of the earliest and most respected distributions (founded 1993). Nicknamed "The Universal Operating System", it is the mother distro to Ubuntu, Mint, and Kali, famous for legendary stability.
    `;
  }

  // Distros in general
  if (s.includes('distro') || s.includes('distribution') || s.includes('distribusi')) {
    return isId ? `
      <span class="cmd-hl">&#9658; Apa itu Distribusi Linux (Distro)?</span><br>
      Karena Linux hanyalah sebuah kernel, pengembang menggabungkannya dengan alat GNU, manajer paket (apt, pacman, dnf), lingkungan desktop (GNOME, KDE), dan aplikasi siap pakai menjadi sebuah sistem operasi lengkap bernama <strong>distro</strong>. Ada lebih dari 600 distro aktif di dunia!
    ` : `
      <span class="cmd-hl">&#9658; What is a Linux Distribution (Distro)?</span><br>
      Because Linux is just a kernel, developers combine it with system tools (GNU), package managers (apt, pacman, dnf), desktop environments (GNOME, KDE), and software into a complete OS called a <strong>distro</strong>. There are over 600 active distros worldwide!
    `;
  }

  // Supercomputers & HPC
  if (s.includes('supercomputer') || s.includes('hpc') || s.includes('superkomputer')) {
    return isId ? `
      <span class="cmd-hl">&#9658; Linux di Superkomputer:</span><br>
      <strong>100%</strong> dari 500 superkomputer tercepat di dunia (TOP500) menggunakan Linux (seperti Frontier, Aurora, dan Fugaku) karena skalabilitas, performa mentah, dan kebebasan modifikasinya tak tertandingi.
    ` : `
      <span class="cmd-hl">&#9658; Linux on Supercomputers:</span><br>
      <strong>100%</strong> of the world's TOP500 fastest supercomputers run Linux (such as Frontier, Aurora, and Fugaku). Nothing else comes close in scalability, customizability, and raw performance.
    `;
  }

  // Android
  if (s.includes('android') || s.includes('mobile') || s.includes('phone') || s.includes('ponsel') || s.includes('hp')) {
    return isId ? `
      <span class="cmd-hl">&#9658; Android &amp; Linux:</span><br>
      Ya, Google Android dibangun langsung di atas kernel Linux yang dimodifikasi. Artinya lebih dari <strong>3.6 miliar perangkat aktif</strong> di seluruh dunia mengantongi Linux setiap hari.
    ` : `
      <span class="cmd-hl">&#9658; Android &amp; Linux:</span><br>
      Yes, Google's Android is built directly on top of a modified Linux kernel. That means over <strong>3.6 billion active mobile devices</strong> are carrying Linux in people's pockets every day.
    `;
  }

  // Space / NASA / SpaceX
  if (s.includes('space') || s.includes('nasa') || s.includes('mars') || s.includes('spacex') || s.includes('antariksa') || s.includes('luar angkasa')) {
    return isId ? `
      <span class="cmd-hl">&#9658; Linux di Luar Angkasa:</span><br>
      SpaceX menggunakan Linux pada roket Falcon 9 dan kapsul Dragon. NASA mengandalkan Linux di Stasiun Luar Angkasa Internasional (ISS), rover Mars Curiosity/Perseverance, hingga helikopter Ingenuity di Mars!
    ` : `
      <span class="cmd-hl">&#9658; Linux in Outer Space:</span><br>
      SpaceX uses Linux to run Falcon 9 rockets and Dragon capsules. NASA uses Linux on the International Space Station (ISS), the Curiosity/Perseverance Mars rovers, and the Ingenuity Mars helicopter!
    `;
  }

  // Open Source / Free / GPL
  if (s.includes('open source') || s.includes('gpl') || s.includes('free') || s.includes('license') || s.includes('sumber terbuka') || s.includes('lisensi')) {
    return isId ? `
      <span class="cmd-hl">&#9658; Mengapa Open Source &amp; GPL?</span><br>
      Linux dirilis di bawah lisensi <strong>GNU General Public License (GPL v2)</strong>. Siapapun berhak melihat, mengedit, mengembangkan, dan membagikan kode sumbernya. Kolaborasi terbuka ini membuat Linux berkembang jauh lebih pesat dari perusahaan software manapun.
    ` : `
      <span class="cmd-hl">&#9658; Why Open Source &amp; GPL?</span><br>
      Linux is licensed under the <strong>GNU General Public License (GPL v2)</strong>. Anyone has the freedom to view, edit, improve, and share the source code. This open collaboration made Linux evolve faster than any proprietary software company ever could.
    `;
  }

  // Linux vs Windows
  if (s.includes('windows') || s.includes('difference') || s.includes('vs')) {
    return isId ? `
      <span class="cmd-hl">&#9658; Linux vs Windows:</span><br>
      &bull; <strong>Kendali Penuh:</strong> Linux memberi kebebasan sistem &amp; privasi 100% tanpa telemetri paksa.<br>
      &bull; <strong>Efisiensi:</strong> Sangat ringan; sanggup menghidupkan laptop lawas berusia 15 tahun.<br>
      &bull; <strong>Keamanan:</strong> Izin file granular dan kode terbuka membuatnya sangat tangguh.<br>
      &bull; <strong>Server &amp; Cloud:</strong> 96%+ web berjalan di Linux; bahkan Windows kini menyertakan WSL (Windows Subsystem for Linux) bagi para developer.
    ` : `
      <span class="cmd-hl">&#9658; Linux vs Windows:</span><br>
      &bull; <strong>Control:</strong> Linux grants 100% system freedom &amp; privacy with zero forced telemetry.<br>
      &bull; <strong>Resource usage:</strong> Linux is ultra lightweight; can revive 15-year-old laptops.<br>
      &bull; <strong>Security:</strong> Granular file permissions and open code make Linux naturally resilient.<br>
      &bull; <strong>Servers/Cloud:</strong> Linux powers 96%+ of the web; Windows even added WSL (Windows Subsystem for Linux) so developers can use Linux inside Windows.
    `;
  }

  // Tux penguin
  if (s.includes('tux') || s.includes('penguin') || s.includes('mascot') || s.includes('maskot')) {
    return isId ? `
      <span class="cmd-hl">&#9658; Pinguin Tux:</span><br>
      Tux adalah maskot resmi Linux yang dipilih oleh Linus Torvalds pada tahun 1996 setelah ia digigit pinguin kecil di kebun binatang Perth, Australia. Nama "TUX" berasal dari <strong>(T)orvalds (U)ni(X)</strong>!
    ` : `
      <span class="cmd-hl">&#9658; Tux the Penguin:</span><br>
      Tux is the official mascot of Linux, chosen by Linus Torvalds in 1996 after he was playfully bitten by a little penguin at a zoo in Perth, Australia. The name "TUX" stands for <strong>(T)orvalds (U)ni(X)</strong>!
    `;
  }

  // What is Linux general
  if (s.includes('what is linux') || s.includes('about linux') || s.includes('why linux') || s.includes('apa itu linux') || s.includes('tentang linux')) {
    return isId ? `
      <span class="cmd-hl">&#9658; Apa itu Linux?</span><br>
      Linux adalah kernel sistem operasi open-source mirip UNIX yang pertama kali dirilis tahun 1991. Dipadukan dengan paket utilitas GNU, Linux membentuk OS lengkap yang menggerakkan segalanya, mulai dari ponsel pintar (Android) dan server web hingga superkomputer dunia.
    ` : `
      <span class="cmd-hl">&#9658; What is Linux?</span><br>
      Linux is a free, open-source UNIX-like operating system kernel first published in 1991. Paired with GNU system tools, it forms complete operating systems powering everything from smartphones (Android) and web servers (Apache/Nginx) to supercomputers and NASA space missions.
    `;
  }

  // What is Linux used for
  if (s.includes('used for') || s.includes('use case') || s.includes('uses') || s.includes('kegunaan') || s.includes('fungsi') || s.includes('manfaat')) {
    return isId ? `
      <span class="cmd-hl">&#9658; Untuk Apa Saja Linux Digunakan?</span><br>
      &bull; <strong>Server Web &amp; Cloud:</strong> 96% dari 1 juta web teratas &amp; cloud AWS/GCP/Azure<br>
      &bull; <strong>Ponsel Pintar:</strong> Menggerakkan Android pada 3.6+ miliar perangkat<br>
      &bull; <strong>Superkomputer:</strong> Seluruh 500 dari 500 sistem TOP500 tercepat di dunia<br>
      &bull; <strong>Keamanan Siber:</strong> Kali Linux untuk pengujian penetrasi dan audit jaringan<br>
      &bull; <strong>IoT &amp; Hardware:</strong> Router, smart TV, mobil listrik Tesla, dan peralatan medis
    ` : `
      <span class="cmd-hl">&#9658; What is Linux Used For?</span><br>
      &bull; <strong>Web &amp; Cloud:</strong> 96% of top web servers &amp; AWS/GCP/Azure clouds<br>
      &bull; <strong>Mobile:</strong> Powers Android on 3.6+ billion devices<br>
      &bull; <strong>Supercomputers:</strong> 500 out of 500 TOP500 systems<br>
      &bull; <strong>Cybersecurity:</strong> Kali Linux penetration testing and network defense<br>
      &bull; <strong>IoT &amp; Hardware:</strong> Routers, smart TVs, Tesla cars, and medical equipment
    `;
  }

  return null;
}

function escapeHtml(str) {
  return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
}

function openCmd(sectionKey) {
  activeSectionKey = sectionKey;
  const summaries = currentLang === 'id' ? CMD_SUMMARIES_ID : CMD_SUMMARIES;
  const data = summaries[sectionKey] || CMD_SUMMARIES[sectionKey];
  if (!data) return;

  cmdTitle.textContent = data.title;
  cmdBody.innerHTML    = '';
  cmdWin.classList.remove('minimized');
  cmdWin.classList.add('show');
  if (cmdInput) setTimeout(() => cmdInput.focus(), 300);

  if (cmdTyping) { clearTimeout(cmdTyping); cmdTyping = null; }

  const cursorEl = document.createElement('span');
  cursorEl.id = 'cmd-cursor';
  cmdBody.appendChild(cursorEl);

  let lineIdx = 0;
  let charIdx = 0;
  let currentEl = null;

  function typeNext() {
    if (lineIdx >= data.lines.length) {
      cursorEl.remove();
      const done = document.createElement('div');
      done.innerHTML = '<span class="cmd-line">user@linux:~$</span> <span id="cmd-cursor"></span>';
      cmdBody.appendChild(done);
      return;
    }

    const line = data.lines[lineIdx];

    if (charIdx === 0) {
      currentEl = document.createElement('div');
      currentEl.className = {
        cmd:  'cmd-line',
        out:  'cmd-out',
        hl:   'cmd-hl',
        warn: 'cmd-warn',
        err:  'cmd-err',
      }[line.type] || 'cmd-out';
      if (line.type === 'cmd') {
        const pfx = document.createElement('span');
        pfx.className = 'cmd-line';
        pfx.textContent = 'user@linux:~$ ';
        currentEl.appendChild(pfx);
      }
      cmdBody.insertBefore(currentEl, cursorEl);
    }

    if (charIdx < line.text.length) {
      const ch = document.createTextNode(line.text[charIdx]);
      currentEl.appendChild(ch);
      charIdx++;
      cmdBody.insertBefore(cursorEl, null);
      cmdBody.scrollTop = cmdBody.scrollHeight;
      const delay = line.type === 'cmd'
        ? 24 + Math.random() * 26
        : 10 + Math.random() * 8;
      cmdTyping = setTimeout(typeNext, delay);
    } else {
      lineIdx++;
      charIdx = 0;
      const pause = line.text === '' ? 50 : (line.type === 'cmd' ? 180 : 45);
      cmdTyping = setTimeout(typeNext, pause);
    }
  }

  cmdTyping = setTimeout(typeNext, 250);
}

function closeCmd() {
  cmdWin.classList.remove('show');
  if (cmdTyping) { clearTimeout(cmdTyping); cmdTyping = null; }
}

// Close on ESC
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeCmd(); });

// Attach VHS glitch & CMD popup to ALL nav links and CTA buttons
document.querySelectorAll('[data-section]').forEach(el => {
  el.addEventListener('click', function(e) {
    const sec = this.getAttribute('data-section');
    triggerVhsGlitch(650);
    if (sec) openCmd(sec);
  });
});

// Initialize bilingual language on page boot
setLanguage(currentLang);
</script>
</body>
</html>
