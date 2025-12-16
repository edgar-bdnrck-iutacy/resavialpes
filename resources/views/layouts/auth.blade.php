<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>ResAvialpes - @yield('title', 'Connexion')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- On garde le CSS global pour réutiliser tes styles btn/card/input --}}
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

  {{-- Applique le thème dès le chargement (évite flash) --}}
  <script>
    (function () {
      try {
        const key = 'resavialpes-theme';
        const saved = localStorage.getItem(key);
        const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

        const theme = (saved === 'theme-light' || saved === 'theme-dark')
          ? saved
          : (prefersDark ? 'theme-dark' : 'theme-light');

        const html = document.documentElement;
        html.classList.remove('theme-dark', 'theme-light');
        html.classList.add(theme);
        html.style.colorScheme = (theme === 'theme-dark') ? 'dark' : 'light';
      } catch (e) {}
    })();
  </script>

<style>
  :root{
    --auth-accent: #4f46e5; /* indigo */
    --auth-accent-2: #22c55e; /* green */
    --auth-card-bg: rgba(17, 24, 39, .70);
    --auth-card-border: rgba(255,255,255,.08);
    --auth-text: rgba(255,255,255,.92);
    --auth-muted: rgba(255,255,255,.62);
  }

  html.theme-light{
    --auth-card-bg: rgba(255,255,255,.75);
    --auth-card-border: rgba(0,0,0,.08);
    --auth-text: rgba(0,0,0,.88);
    --auth-muted: rgba(0,0,0,.55);
  }

  body{
    margin:0;
  }

  .auth-bg{
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:28px;
    position:relative;
    overflow:hidden;
  }

  /* “glow blobs” */
  .auth-bg::before,
  .auth-bg::after{
    content:"";
    position:absolute;
    width:520px;
    height:520px;
    border-radius:999px;
    filter: blur(60px);
    opacity:.55;
    z-index:0;
    transform: translateZ(0);
    animation: floaty 10s ease-in-out infinite;
  }

  .auth-bg::before{
    left:-160px;
    top:-180px;
    background: radial-gradient(circle at 30% 30%, var(--auth-accent), transparent 60%);
  }
  .auth-bg::after{
    right:-180px;
    bottom:-220px;
    background: radial-gradient(circle at 70% 70%, var(--auth-accent-2), transparent 60%);
    animation-delay: -2.7s;
  }

  @keyframes floaty{
    0%,100% { transform: translate(0,0) scale(1); }
    50% { transform: translate(20px, 16px) scale(1.05); }
  }

  .auth-shell{
    width:100%;
    max-width:520px;
    position:relative;
    z-index:1;
  }

  .auth-brand{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:12px;
    margin-bottom:14px;
    color:var(--auth-text);
  }

  .auth-logo{
    width:46px;
    height:46px;
    border-radius:14px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:900;
    letter-spacing:.5px;
    background: linear-gradient(135deg, rgba(79,70,229,.35), rgba(34,197,94,.25));
    border:1px solid var(--auth-card-border);
    box-shadow: 0 10px 30px rgba(0,0,0,.25);
  }

  .auth-card{
    border-radius:18px;
    border:1px solid var(--auth-card-border);
    background: var(--auth-card-bg);
    backdrop-filter: blur(14px);
    box-shadow: 0 18px 55px rgba(0,0,0,.35);
    overflow:hidden;
  }

  .auth-card-header{
    padding:18px 18px 12px;
    border-bottom:1px solid var(--auth-card-border);
    text-align:center;
    color:var(--auth-text);
  }

  .auth-title{
    font-size:20px;
    font-weight:800;
    margin:0 0 6px 0;
  }

  .auth-subtitle{
    font-size:13px;
    color:var(--auth-muted);
  }

  .auth-card-body{
    padding:16px 18px 18px;
  }

  .auth-field{
    margin-bottom:12px;
  }

  .auth-label{
    display:block;
    font-size:13px;
    margin-bottom:6px;
    color:var(--auth-muted);
  }

  .auth-input-wrap{
    position:relative;
  }

  .auth-input{
    width:100%;
    box-sizing:border-box;
    height:46px;
    padding:0 44px 0 44px;
    border-radius:14px;
    border:1px solid var(--auth-card-border);
    background: rgba(255,255,255,.06);
    color:var(--auth-text);
    outline:none;
  }

  html.theme-light .auth-input{
    background: rgba(0,0,0,.03);
  }

  .auth-input:focus{
    border-color: rgba(79,70,229,.55);
    box-shadow: 0 0 0 4px rgba(79,70,229,.15);
  }

  .auth-icon{
    position:absolute;
    left:14px;
    top:50%;
    transform:translateY(-50%);
    opacity:.7;
  }

  .auth-action{
    position:absolute;
    right:10px;
    top:50%;
    transform:translateY(-50%);
  }

  .auth-btn{
    width:100%;
    height:46px;
    border-radius:14px;
    border:1px solid rgba(255,255,255,.10);
    background: linear-gradient(135deg, rgba(79,70,229,.95), rgba(34,197,94,.65));
    color:white;
    font-weight:800;
    cursor:pointer;
    transition: transform .08s ease, filter .12s ease;
  }

  .auth-btn:hover{ filter: brightness(1.05); }
  .auth-btn:active{ transform: translateY(1px); }

  .auth-row{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    margin: 8px 0 14px;
    color:var(--auth-muted);
    font-size:13px;
  }
</style>
</head>
<body>
    <div class="auth-bg">
      <div class="auth-shell">
        <div class="auth-brand">
          <div class="auth-logo">RA</div>
          <div style="text-align:left;">
            <div style="font-weight:900; line-height:1;">ResAvialpes</div>
            <div class="auth-subtitle">Administration</div>
          </div>
        </div>
  
        @yield('content')
      </div>
    </div>


  <script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
