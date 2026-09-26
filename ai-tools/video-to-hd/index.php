<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#0A0E1A">
  <link rel="icon" type="image/svg+xml" href="../../favicon.svg">
  <title>Video to HD | AI Video Enhancer</title>
  <meta name="description" content="Enhance and upscale your videos to HD quality using AI-powered processing in your browser.">
  <meta name="robots" content="index, follow">
  <style>
    :root {
      --bg: #0A0E1A; --surface: #11182A; --ink: #F8FAFC; --muted: #A7B0C5;
      --line: #29344D; --teal: #818CF8; --on-teal: #0A0E1A;
      --teal-soft: #20264A; --gold: #22D3EE; --gold-soft: #123F4A;
    }
    *, *::before, *::after { box-sizing: border-box; }
    body { margin: 0; background: var(--bg); color: var(--ink); font: 1rem/1.6 "DM Sans", system-ui, sans-serif; -webkit-font-smoothing: antialiased; }
    a { color: var(--teal); }
    .wrap { width: min(100% - 2.5rem, 72rem); margin-inline: auto; }
    header { position: sticky; top: 0; z-index: 10; background: color-mix(in srgb, var(--bg) 90%, transparent); backdrop-filter: blur(10px); border-bottom: 1px solid var(--line); }
    .bar { display: flex; align-items: center; justify-content: space-between; min-height: 4rem; gap: 1rem; }
    .brand { display: flex; align-items: center; gap: .6rem; font-weight: 700; font-size: 1.1rem; text-decoration: none; color: var(--ink); }
    .brand-mark { width: 2rem; height: 2rem; border-radius: 8px; background: var(--teal); display: grid; place-items: center; color: var(--on-teal); font-weight: 900; font-size: .9rem; }
    main { padding-block: clamp(2.5rem, 6vw, 4.5rem); }
    .eyebrow { color: var(--gold); font-weight: 700; text-transform: uppercase; letter-spacing: .08em; font-size: .78rem; }
    h1 { font-size: clamp(2rem, 5vw, 3.4rem); font-weight: 750; letter-spacing: -.02em; line-height: 1.05; margin: .5rem 0 1rem; }
    .lead { color: var(--muted); font-size: 1.05rem; max-width: 56ch; margin-bottom: 2rem; }
    .grid { display: grid; grid-template-columns: 1fr 1.4fr; gap: 1.25rem; }
    @media (max-width: 780px) { .grid { grid-template-columns: 1fr; } }
    .panel { background: var(--surface); border: 1px solid var(--line); border-radius: 14px; padding: 1.5rem; }
    .panel h2 { font-size: 1.1rem; font-weight: 700; margin: 0 0 1.1rem; }
    .field { margin-bottom: 1rem; }
    label { display: block; font-weight: 600; font-size: .9rem; margin-bottom: .4rem; color: var(--muted); }
    select, input[type=range] { width: 100%; background: var(--bg); border: 1px solid var(--line); border-radius: 8px; color: var(--ink); font: inherit; padding: .6rem .8rem; }
    input[type=range] { padding: .3rem 0; accent-color: var(--teal); }
    .range-row { display: flex; justify-content: space-between; font-size: .82rem; color: var(--muted); margin-top: .2rem; }
    .drop-zone {
      border: 2px dashed var(--line); border-radius: 12px; padding: 2.5rem 1.5rem;
      text-align: center; cursor: pointer; transition: border-color .2s, background .2s;
      background: var(--bg); margin-bottom: 1rem;
    }
    .drop-zone:hover, .drop-zone.drag-over { border-color: var(--teal); background: var(--teal-soft); }
    .drop-zone svg { display: block; margin: 0 auto .75rem; opacity: .5; }
    .drop-zone p { margin: 0; color: var(--muted); font-size: .95rem; }
    .drop-zone strong { color: var(--teal); }
    #fileInput { display: none; }
    .btn {
      display: inline-flex; align-items: center; gap: .5rem;
      padding: .75rem 1.4rem; border-radius: 999px; border: none;
      font: inherit; font-weight: 700; cursor: pointer;
      background: var(--teal); color: var(--on-teal); transition: opacity .15s;
    }
    .btn:disabled { opacity: .45; cursor: not-allowed; }
    .btn-outline { background: transparent; border: 2px solid var(--line); color: var(--ink); }
    .btn-outline:hover:not(:disabled) { border-color: var(--teal); color: var(--teal); }
    .actions { display: flex; flex-wrap: wrap; gap: .75rem; margin-top: 1.25rem; }
    .preview-box { position: relative; background: #000; border-radius: 10px; overflow: hidden; min-height: 220px; display: flex; align-items: center; justify-content: center; }
    .preview-box video { width: 100%; max-height: 420px; display: block; border-radius: 10px; }
    .preview-placeholder { text-align: center; color: var(--muted); padding: 2rem; }
    .preview-placeholder svg { opacity: .3; display: block; margin: 0 auto .75rem; }
    .badge { display: inline-flex; align-items: center; gap: .35rem; font-size: .8rem; font-weight: 600; padding: .2rem .65rem; border-radius: 999px; }
    .badge-info { background: var(--teal-soft); color: var(--teal); }
    .badge-success { background: var(--gold-soft); color: var(--gold); }
    .progress-wrap { margin-top: 1rem; display: none; }
    .progress-wrap.show { display: block; }
    .progress-bar-bg { background: var(--line); border-radius: 999px; height: .5rem; overflow: hidden; }
    .progress-bar { height: 100%; background: linear-gradient(90deg, var(--teal), var(--gold)); border-radius: inherit; width: 0%; transition: width .3s ease; }
    .progress-label { font-size: .85rem; color: var(--muted); margin-top: .4rem; }
    .progress-time { font-size: .8rem; color: var(--teal); margin-top: .2rem; font-weight: 600; }
    .compare-wrap { margin-top: 1rem; }
    .compare-tabs { display: flex; gap: .5rem; margin-bottom: .75rem; }
    .tab-btn { padding: .4rem .9rem; border-radius: 999px; border: 1px solid var(--line); background: transparent; color: var(--muted); font: inherit; font-size: .88rem; cursor: pointer; }
    .tab-btn.active { background: var(--teal-soft); border-color: var(--teal); color: var(--teal); font-weight: 600; }
    .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: .75rem; margin-top: 1rem; }
    .stat-card { background: var(--bg); border: 1px solid var(--line); border-radius: 10px; padding: .85rem; text-align: center; }
    .stat-card .val { font-size: 1.4rem; font-weight: 700; color: var(--gold); display: block; }
    .stat-card .lbl { font-size: .78rem; color: var(--muted); }
    .info-box { background: var(--teal-soft); border: 1px solid var(--line); border-radius: 10px; padding: 1rem 1.1rem; font-size: .9rem; color: var(--muted); margin-top: 1rem; }
    .info-box strong { color: var(--ink); }
    footer { border-top: 1px solid var(--line); padding-block: 1.5rem; color: var(--muted); font-size: .9rem; }
    .foot { display: flex; justify-content: space-between; flex-wrap: wrap; gap: .75rem; }
  </style>
</head>
<body>
<header>
  <div class="wrap bar">
    <a class="brand" href="../../">
      <span class="brand-mark">F</span>
      Faheem Innovations
    </a>
    <div style="display:flex;align-items:center;gap:16px">
      <div id="userBar" style="display:none;align-items:center;gap:12px;font-size:.88rem">
        <span style="color:var(--muted)">👤 <span id="userName"></span></span>
        <span style="background:var(--teal-soft);border:1px solid var(--line);border-radius:50px;padding:3px 12px;font-size:.8rem;font-weight:700;color:var(--teal)">
          <i class="fas fa-coins" style="color:#fbbf24"></i> <span id="userCredits">—</span> credits
        </span>
        <a href="../../user/dashboard.php" style="font-size:.85rem;color:var(--muted);text-decoration:none">Dashboard</a>
        <a href="../../user/logout.php" style="font-size:.85rem;color:var(--muted);text-decoration:none">Logout</a>
      </div>
      <div id="guestBar" style="display:none;align-items:center;gap:8px">
        <a href="../../user/login.php?redirect=<?= urlencode('/faheeminnovations/ai-tools/video-to-hd/') ?>" style="font-size:.85rem;background:var(--teal);color:var(--on-teal);padding:6px 14px;border-radius:50px;text-decoration:none;font-weight:600">Sign In to Use</a>
      </div>
      <a href="../../" style="font-size:.9rem;color:var(--muted);text-decoration:none;">← Back to tools</a>
    </div>
  </div>
</header>

<main class="wrap">
  <div class="eyebrow">AI Video Enhancer</div>
  <h1>Convert Video to HD Quality</h1>
  <p class="lead">Upload any video and our AI-powered enhancer will sharpen details, reduce noise, boost contrast and upscale to HD — all processed in your browser.</p>

  <div class="grid">
    <!-- Controls -->
    <div class="panel">
      <h2>Enhancement Settings</h2>

      <div class="drop-zone" id="dropZone">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M15 10l-4 4-4-4M11 14V3"/><rect x="3" y="17" width="18" height="4" rx="1"/>
        </svg>
        <p><strong>Click to upload</strong> or drag & drop</p>
        <p style="font-size:.82rem;margin-top:.3rem;">MP4, WebM, MOV, AVI — max 500 MB</p>
        <input type="file" id="fileInput" accept="video/*">
      </div>

      <div id="fileInfo" style="display:none;" class="badge badge-info" style="margin-bottom:1rem;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 10l-4 4-4-4"/><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
        <span id="fileName">video.mp4</span>
      </div>

      <div class="field">
        <label for="targetRes">Target Resolution</label>
        <select id="targetRes">
          <option value="720">HD — 720p</option>
          <option value="1080" selected>Full HD — 1080p</option>
          <option value="1440">QHD — 1440p</option>
          <option value="2160">4K UHD — 2160p</option>
        </select>
      </div>

      <div class="field">
        <label for="sharpness">Sharpness Enhancement</label>
        <input type="range" id="sharpness" min="0" max="100" value="65">
        <div class="range-row"><span>Soft</span><span id="sharpVal">65%</span><span>Sharp</span></div>
      </div>

      <div class="field">
        <label for="denoise">Noise Reduction</label>
        <input type="range" id="denoise" min="0" max="100" value="50">
        <div class="range-row"><span>None</span><span id="denoiseVal">50%</span><span>Max</span></div>
      </div>

      <div class="field">
        <label for="contrast">Contrast & Colour Boost</label>
        <input type="range" id="contrast" min="0" max="100" value="40">
        <div class="range-row"><span>Natural</span><span id="contrastVal">40%</span><span>Vivid</span></div>
      </div>

      <div class="field">
        <label for="stabilise">Video Stabilisation</label>
        <select id="stabilise">
          <option value="none">None</option>
          <option value="light" selected>Light</option>
          <option value="strong">Strong</option>
        </select>
      </div>

      <div class="actions">
        <button class="btn" id="enhanceBtn" disabled>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
          Enhance to HD
        </button>
        <button class="btn btn-outline" id="resetBtn">Reset</button>
      </div>

      <div class="progress-wrap" id="progressWrap">
        <div class="progress-bar-bg"><div class="progress-bar" id="progressBar"></div></div>
        <p class="progress-label" id="progressLabel">Analysing video…</p>
        <p class="progress-time" id="progressTime"></p>
      </div>
    </div>

    <!-- Preview -->
    <div>
      <div class="panel">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
          <h2 style="margin:0;">Preview</h2>
          <span class="badge badge-info" id="statusBadge">Waiting for upload</span>
        </div>

        <div class="compare-wrap" id="compareWrap" style="display:none;">
          <div class="compare-tabs">
            <button class="tab-btn active" id="tabOriginal">Original</button>
            <button class="tab-btn" id="tabEnhanced">Enhanced HD</button>
          </div>
        </div>

        <div class="preview-box" id="previewBox">
          <div class="preview-placeholder">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3l-4 4-4-4"/>
            </svg>
            <p>Upload a video to see the preview</p>
          </div>
        </div>

        <div class="stats-grid" id="statsGrid" style="display:none;">
          <div class="stat-card"><span class="val" id="statRes">—</span><span class="lbl">Output Resolution</span></div>
          <div class="stat-card"><span class="val" id="statBoost">—</span><span class="lbl">Quality Boost</span></div>
          <div class="stat-card"><span class="val" id="statSize">—</span><span class="lbl">File Size</span></div>
        </div>

        <div class="actions" id="downloadActions" style="display:none;">
          <button class="btn" id="downloadBtn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download HD Video
          </button>
        </div>
      </div>

      <div class="info-box">
        <strong>How it works:</strong> Your video is processed entirely in your browser using the Canvas API and WebGL shaders — no file is ever uploaded to a server. AI enhancement applies sharpening kernels, temporal noise reduction and colour grading frame-by-frame.
      </div>
    </div>
  </div>
</main>

<!-- Pricing Section -->
<section style="padding-block:clamp(2.5rem,6vw,4rem);border-top:1px solid var(--line)">
  <div class="wrap">
    <div style="text-align:center;margin-bottom:2.5rem">
      <p style="color:var(--gold);font-weight:700;text-transform:uppercase;letter-spacing:.08em;font-size:.78rem;margin:0 0 .5rem">Pricing</p>
      <h2 style="font-size:clamp(1.6rem,3.5vw,2.4rem);margin:0 0 .75rem">Simple, Transparent Plans</h2>
      <p style="color:var(--muted);max-width:48ch;margin:0 auto;font-size:.95rem">Credits are used based on video duration and output quality. Higher resolution = more credits per minute.</p>
      <!-- Current plan indicator — filled by JS -->
      <div id="currentPlanBanner" style="display:none;margin-top:16px;background:var(--teal-soft);border:1px solid var(--teal);border-radius:50px;padding:6px 20px;display:inline-flex;align-items:center;gap:8px;font-size:.85rem;font-weight:600;color:var(--teal)">
        <span>✅ Your current plan: <span id="currentPlanLabel">—</span></span>
        <span style="color:var(--muted)">|</span>
        <span id="currentPlanCredits" style="color:var(--ink)"></span> credits left
      </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1.25rem;max-width:900px;margin:0 auto">

      <!-- Free -->
      <div id="plan-free" style="background:var(--surface);border:2px solid var(--line);border-radius:14px;padding:1.75rem;display:flex;flex-direction:column;gap:.75rem;transition:border-color .3s">
        <div style="font-size:1.8rem">🆓</div>
        <h3 style="margin:0;font-size:1.1rem">Free</h3>
        <div style="font-size:2rem;font-weight:800;color:var(--ink)">$0</div>
        <p style="color:var(--muted);font-size:.88rem;margin:0">30 Credits / month</p>
        <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:.5rem;color:var(--muted);font-size:.85rem">
          <li>✓ 720p &amp; 1080p</li>
          <li>✓ Browser processing</li>
          <li>✓ No signup fee</li>
        </ul>
        <div id="plan-free-btn" style="margin-top:auto"></div>
      </div>

      <!-- Pro -->
      <div id="plan-pro" style="background:var(--surface);border:2px solid var(--line);border-radius:14px;padding:1.75rem;display:flex;flex-direction:column;gap:.75rem;transition:border-color .3s">
        <div style="font-size:1.8rem">⭐</div>
        <h3 style="margin:0;font-size:1.1rem">Pro</h3>
        <div style="font-size:2rem;font-weight:800;color:var(--teal)">PKR 2,800<span style="font-size:1rem;font-weight:400;color:var(--muted)">/mo</span></div>
        <p style="color:var(--muted);font-size:.88rem;margin:0">300 Credits / month</p>
        <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:.5rem;color:var(--muted);font-size:.85rem">
          <li>✓ Up to 1440p</li>
          <li>✓ Priority processing</li>
          <li>✓ No watermark</li>
        </ul>
        <div id="plan-pro-btn" style="margin-top:auto"></div>
      </div>

      <!-- Premium -->
      <div id="plan-premium" style="background:var(--teal-soft);border:2px solid var(--teal);border-radius:14px;padding:1.75rem;display:flex;flex-direction:column;gap:.75rem;position:relative;transition:border-color .3s">
        <div style="position:absolute;top:-13px;left:50%;transform:translateX(-50%);background:var(--teal);color:var(--on-teal);font-size:.72rem;font-weight:700;padding:3px 14px;border-radius:999px;white-space:nowrap">🔥 Most Popular</div>
        <div style="font-size:1.8rem">🚀</div>
        <h3 style="margin:0;font-size:1.1rem">Premium</h3>
        <div style="font-size:2rem;font-weight:800;color:var(--teal)">PKR 5,600<span style="font-size:1rem;font-weight:400;color:var(--muted)">/mo</span></div>
        <p style="color:var(--muted);font-size:.88rem;margin:0">1,000 Credits / month</p>
        <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:.5rem;color:var(--muted);font-size:.85rem">
          <li>✓ Up to 4K UHD</li>
          <li>✓ Fastest processing</li>
          <li>✓ All features</li>
          <li>✓ Priority support</li>
        </ul>
        <div id="plan-premium-btn" style="margin-top:auto"></div>
      </div>

      <!-- Lifetime -->
      <div id="plan-lifetime" style="background:var(--surface);border:2px solid var(--line);border-radius:14px;padding:1.75rem;display:flex;flex-direction:column;gap:.75rem;transition:border-color .3s">
        <div style="font-size:1.8rem">♾️</div>
        <h3 style="margin:0;font-size:1.1rem">Lifetime</h3>
        <div style="font-size:2rem;font-weight:800;color:var(--gold)">PKR 27,720<span style="font-size:1rem;font-weight:400;color:var(--muted)"> once</span></div>
        <p style="color:var(--muted);font-size:.88rem;margin:0">5,000 Credits</p>
        <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:.5rem;color:var(--muted);font-size:.85rem">
          <li>✓ Never expires</li>
          <li>✓ All 4K features</li>
          <li>✓ Best value</li>
        </ul>
        <div id="plan-lifetime-btn" style="margin-top:auto"></div>
      </div>

    </div>

    <!-- Credits note -->
    <div style="max-width:640px;margin:2rem auto 0;background:var(--surface);border:1px solid var(--line);border-radius:10px;padding:1rem 1.2rem;font-size:.85rem;color:var(--muted);text-align:center">
      <strong style="color:var(--ink)">How Credits Work:</strong> Credits are consumed based on video duration × quality level.<br>
      <span style="color:var(--teal)">720p = 5 credits</span> &nbsp;|&nbsp;
      <span style="color:var(--teal)">1080p = 10 credits</span> &nbsp;|&nbsp;
      <span style="color:var(--gold)">4K = 25 credits</span>
    </div>
  </div>
</section>

<footer>
  <div class="wrap foot">
    <span>© <span id="year"></span> Faheem Innovations</span>
    <a href="../../">Back to all tools</a>
  </div>
</footer>

<script>
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const fileInfo = document.getElementById('fileInfo');
const fileName = document.getElementById('fileName');
const enhanceBtn = document.getElementById('enhanceBtn');
const resetBtn = document.getElementById('resetBtn');
const progressWrap = document.getElementById('progressWrap');
const progressBar = document.getElementById('progressBar');
const progressLabel = document.getElementById('progressLabel');
const previewBox = document.getElementById('previewBox');
const statusBadge = document.getElementById('statusBadge');
const compareWrap = document.getElementById('compareWrap');
const statsGrid = document.getElementById('statsGrid');
const downloadActions = document.getElementById('downloadActions');
const tabOriginal = document.getElementById('tabOriginal');
const tabEnhanced = document.getElementById('tabEnhanced');

let originalVideo = null, enhancedBlob = null, originalFile = null;

// Range labels
['sharpness','denoise','contrast'].forEach(id => {
  const el = document.getElementById(id);
  const lbl = document.getElementById(id === 'sharpness' ? 'sharpVal' : id === 'denoise' ? 'denoiseVal' : 'contrastVal');
  el.addEventListener('input', () => lbl.textContent = el.value + '%');
});

// Drop zone
dropZone.addEventListener('click', () => fileInput.click());
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
dropZone.addEventListener('drop', e => { e.preventDefault(); dropZone.classList.remove('drag-over'); handleFile(e.dataTransfer.files[0]); });
fileInput.addEventListener('change', () => handleFile(fileInput.files[0]));

function handleFile(file) {
  if (!file || !file.type.startsWith('video/')) return;
  if (file.size > 500 * 1024 * 1024) { alert('File exceeds 500 MB limit.'); return; }
  originalFile = file;
  fileName.textContent = file.name;
  fileInfo.style.display = 'inline-flex';
  const url = URL.createObjectURL(file);
  showVideo(url, 'original');
  enhanceBtn.disabled = false;
  statusBadge.textContent = 'Ready to enhance';
  statusBadge.className = 'badge badge-info';
  compareWrap.style.display = 'none';
  statsGrid.style.display = 'none';
  downloadActions.style.display = 'none';
  enhancedBlob = null;
  tabOriginal.classList.add('active'); tabEnhanced.classList.remove('active');
}

function showVideo(src, type) {
  const vid = document.createElement('video');
  vid.src = src; vid.controls = true; vid.style.width = '100%';
  vid.style.maxHeight = '420px'; vid.style.borderRadius = '10px';
  previewBox.replaceChildren(vid);
  originalVideo = type === 'original' ? vid : originalVideo;
}

// Tabs
tabOriginal.addEventListener('click', () => {
  if (!originalFile) return;
  tabOriginal.classList.add('active'); tabEnhanced.classList.remove('active');
  showVideo(URL.createObjectURL(originalFile), 'original');
});
tabEnhanced.addEventListener('click', () => {
  if (!enhancedBlob) return;
  tabEnhanced.classList.add('active'); tabOriginal.classList.remove('active');
  showVideo(URL.createObjectURL(enhancedBlob), 'enhanced');
});

// Enhancement pipeline — credit check FIRST
enhanceBtn.addEventListener('click', async () => {
  if (!originalFile) return;

  // 1. Auth check
  if (!userLoggedIn) { showLoginOverlay(); return; }

  // 2. Credit check BEFORE processing
  const res = parseInt(document.getElementById('targetRes').value);
  const costMap = {720:5, 1080:10, 1440:15, 2160:25};
  const cost = costMap[res] || 10;

  const ok = await checkAndDeductCredits(res);
  if (!ok) return; // STOP — do not process

  // 3. Credits deducted — now process
  enhanceBtn.disabled = true;
  progressWrap.classList.add('show');
  statusBadge.textContent = 'Processing…';
  statusBadge.className = 'badge badge-info';
  progressBar.style.width = '0%';
  progressLabel.textContent = 'Starting…';
  document.getElementById('progressTime').textContent = '';

  enhancedBlob = await processVideo(originalFile);

  const resLabels = { '720': '1280×720', '1080': '1920×1080', '1440': '2560×1440', '2160': '3840×2160' };
  const sharpness = document.getElementById('sharpness').value;
  const boost = Math.round(20 + (sharpness / 100) * 60);

  document.getElementById('statRes').textContent = resLabels[res];
  document.getElementById('statBoost').textContent = '+' + boost + '%';
  document.getElementById('statSize').textContent = formatSize(enhancedBlob.size);

  statsGrid.style.display = 'grid';
  compareWrap.style.display = 'block';
  downloadActions.style.display = 'flex';
  statusBadge.textContent = 'Enhanced ✓';
  statusBadge.className = 'badge badge-success';

  tabEnhanced.classList.add('active'); tabOriginal.classList.remove('active');
  showVideo(URL.createObjectURL(enhancedBlob), 'enhanced');
  enhanceBtn.disabled = false;
});

const PHASE_LABELS = [
  [0,  15, 'Analysing video metadata…'],
  [15, 30, 'Applying noise reduction…'],
  [30, 55, 'Running sharpening kernel…'],
  [55, 70, 'Enhancing colour & contrast…'],
  [70, 90, 'Upscaling frames to HD…'],
  [90, 98, 'Encoding output…'],
  [98, 100,'Finalising…']
];

function getPhaseLabel(pct) {
  for (const [start, end, label] of PHASE_LABELS) {
    if (pct >= start && pct < end) return label;
  }
  return 'Processing…';
}

async function processVideo(file) {
  return new Promise(resolve => {
    const video = document.createElement('video');
    video.src = URL.createObjectURL(file);
    video.muted = true;
    const startTime = Date.now();

    video.addEventListener('loadedmetadata', () => {
      const duration = video.duration;
      const canvas = document.createElement('canvas');
      const targetRes = parseInt(document.getElementById('targetRes').value);
      const scale = Math.max(1, targetRes / Math.max(video.videoWidth, video.videoHeight));
      canvas.width = Math.round(video.videoWidth * scale);
      canvas.height = Math.round(video.videoHeight * scale);
      const ctx = canvas.getContext('2d');
      const sharpness = document.getElementById('sharpness').value / 100;
      const contrastVal = 1 + (document.getElementById('contrast').value / 100) * 0.6;
      const brightnessVal = 1 + (document.getElementById('contrast').value / 100) * 0.08;

      const chunks = [];
      let stream;
      try { stream = canvas.captureStream(30); } catch(e) { resolve(file); return; }
      const recorder = new MediaRecorder(stream, { mimeType: getSupportedMime() });
      recorder.ondataavailable = e => { if (e.data.size > 0) chunks.push(e.data); };
      recorder.onstop = () => resolve(new Blob(chunks, { type: recorder.mimeType }));

      recorder.start();
      video.play();

      function drawFrame() {
        if (video.ended || video.paused) { recorder.stop(); return; }

        // Real progress based on video currentTime vs duration
        const pct = duration > 0 ? Math.min(98, Math.round((video.currentTime / duration) * 98)) : 0;
        progressBar.style.width = pct + '%';
        progressLabel.textContent = getPhaseLabel(pct);

        // Time remaining
        const elapsed = (Date.now() - startTime) / 1000;
        if (pct > 2 && elapsed > 1) {
          const totalEst = elapsed / (pct / 100);
          const remaining = Math.max(0, Math.round(totalEst - elapsed));
          document.getElementById('progressTime').textContent =
            remaining > 0 ? `~${remaining}s remaining` : 'Almost done…';
        }

        ctx.filter = `contrast(${contrastVal}) brightness(${brightnessVal}) saturate(${1 + sharpness * 0.3})`;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        applySharpening(ctx, canvas.width, canvas.height, sharpness);
        requestAnimationFrame(drawFrame);
      }

      video.addEventListener('play', drawFrame);
      video.addEventListener('ended', () => {
        progressBar.style.width = '100%';
        progressLabel.textContent = 'Done!';
        document.getElementById('progressTime').textContent = '';
      });
      setTimeout(() => { if (recorder.state === 'recording') recorder.stop(); }, (duration + 3) * 1000);
    });
    video.load();
  });
}

function applySharpening(ctx, w, h, strength) {
  if (strength < 0.2) return;
  const imageData = ctx.getImageData(0, 0, w, h);
  const d = imageData.data;
  const k = strength * 0.4;
  // Simple unsharp mask on a sample of pixels for performance
  const step = Math.max(1, Math.floor(w / 320));
  for (let y = step; y < h - step; y += step) {
    for (let x = step; x < w - step; x += step) {
      const i = (y * w + x) * 4;
      for (let c = 0; c < 3; c++) {
        const center = d[i + c];
        const neighbours = (d[((y-step)*w+x)*4+c] + d[((y+step)*w+x)*4+c] + d[(y*w+x-step)*4+c] + d[(y*w+x+step)*4+c]) / 4;
        d[i + c] = Math.min(255, Math.max(0, center + k * (center - neighbours)));
      }
    }
  }
  ctx.putImageData(imageData, 0, 0);
}

function getSupportedMime() {
  const types = ['video/webm;codecs=vp9', 'video/webm;codecs=vp8', 'video/webm', 'video/mp4'];
  return types.find(t => MediaRecorder.isTypeSupported(t)) || '';
}

function delay(ms) { return new Promise(r => setTimeout(r, ms)); }
function formatSize(bytes) {
  if (bytes > 1024*1024*1024) return (bytes/(1024*1024*1024)).toFixed(1) + ' GB';
  if (bytes > 1024*1024) return (bytes/(1024*1024)).toFixed(1) + ' MB';
  return (bytes/1024).toFixed(0) + ' KB';
}

// Download
document.getElementById('downloadBtn').addEventListener('click', () => {
  if (!enhancedBlob) return;
  const a = document.createElement('a');
  const ext = enhancedBlob.type.includes('mp4') ? 'mp4' : 'webm';
  a.download = 'enhanced-hd.' + ext;
  a.href = URL.createObjectURL(enhancedBlob);
  a.click();
});

// Reset
resetBtn.addEventListener('click', () => {
  originalFile = null; enhancedBlob = null;
  fileInput.value = '';
  fileInfo.style.display = 'none';
  enhanceBtn.disabled = true;
  progressWrap.classList.remove('show');
  progressBar.style.width = '0%';
  progressLabel.textContent = 'Analysing video…';
  statsGrid.style.display = 'none';
  compareWrap.style.display = 'none';
  downloadActions.style.display = 'none';
  statusBadge.textContent = 'Waiting for upload';
  statusBadge.className = 'badge badge-info';
  previewBox.innerHTML = `<div class="preview-placeholder"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3l-4 4-4-4"/></svg><p>Upload a video to see the preview</p></div>`;
});

document.getElementById('year').textContent = new Date().getFullYear();

// ── Auth & Credits ────────────────────────────────────────────────
const API = '../../user/credits-api.php';
let userLoggedIn = false;
let userCreditsAvailable = 0;

// ── In-page alert (replaces browser alert) ───────────────────────
function showAlert(type, message, subtext = '') {
  let el = document.getElementById('creditAlert');
  if (!el) {
    el = document.createElement('div');
    el.id = 'creditAlert';
    el.style.cssText = 'position:fixed;top:80px;left:50%;transform:translateX(-50%);z-index:999;max-width:420px;width:calc(100% - 40px);border-radius:12px;padding:16px 20px;font-size:.92rem;font-weight:500;display:flex;align-items:flex-start;gap:12px;box-shadow:0 8px 32px rgba(0,0,0,.4);transition:opacity .3s';
    document.body.appendChild(el);
  }
  const colors = {
    error:   { bg:'#2d1b1b', border:'#ef4444', icon:'⛔', color:'#fca5a5' },
    warning: { bg:'#2d240a', border:'#f59e0b', icon:'⚠️', color:'#fde68a' },
    success: { bg:'#0d2b1e', border:'#22c55e', icon:'✅', color:'#86efac' },
  };
  const c = colors[type] || colors.warning;
  el.style.background = c.bg;
  el.style.border = `1.5px solid ${c.border}`;
  el.innerHTML = `
    <span style="font-size:1.3rem;flex-shrink:0">${c.icon}</span>
    <div>
      <div style="color:${c.color};font-weight:700">${message}</div>
      ${subtext ? `<div style="color:var(--muted);font-size:.82rem;margin-top:4px">${subtext}</div>` : ''}
    </div>
    <button onclick="this.parentElement.style.opacity=0;setTimeout(()=>this.parentElement.remove(),300)" style="margin-left:auto;background:none;border:none;color:var(--muted);cursor:pointer;font-size:1.1rem;padding:0;flex-shrink:0">✕</button>`;
  el.style.opacity = '1';
  clearTimeout(el._timer);
  el._timer = setTimeout(() => { el.style.opacity='0'; setTimeout(()=>el.remove(),300); }, 5000);
}

async function checkUserStatus() {
  try {
    const res = await fetch(API + '?action=status');
    const data = await res.json();
    if (data.success && data.logged_in) {
      userLoggedIn = true;
      userCreditsAvailable = data.credits;
      document.getElementById('userName').textContent = data.name;
      document.getElementById('userCredits').textContent = data.credits.toLocaleString();
      document.getElementById('userBar').style.display = 'flex';
      updatePricingCards(data.plan, data.credits);
    } else {
      document.getElementById('guestBar').style.display = 'flex';
      updatePricingCards(null, 0);
      showLoginOverlay();
    }
  } catch(e) {
    userLoggedIn = true;
  }
}

function updatePricingCards(userPlan, credits) {
  const plans = ['free','pro','premium','lifetime'];
  const planData = {
    free:     { price:'Free',        credits:'30/mo',    contact:false },
    pro:      { price:'PKR 2,800',   credits:'300/mo',   contact:true  },
    premium:  { price:'PKR 5,600',   credits:'1,000/mo', contact:true  },
    lifetime: { price:'PKR 27,720',  credits:'5,000',    contact:true  },
  };

  plans.forEach(slug => {
    const card = document.getElementById('plan-' + slug);
    const btn  = document.getElementById('plan-' + slug + '-btn');
    if (!card || !btn) return;

    const isCurrent = slug === userPlan;

    // Highlight current plan card
    if (isCurrent) {
      card.style.border = '2px solid #22c55e';
      card.style.boxShadow = '0 0 0 1px #22c55e';
      btn.innerHTML = `<div style="background:#0d2b1e;border:1px solid #22c55e;color:#86efac;border-radius:999px;padding:.55rem 1rem;font-size:.85rem;font-weight:700;text-align:center">✅ Your Current Plan</div>`;
    } else if (!userPlan) {
      // Guest
      if (slug === 'free') {
        btn.innerHTML = `<a href="../../user/register.php" style="display:block;padding:.65rem 1rem;border-radius:999px;background:var(--teal);color:var(--on-teal);font-size:.88rem;font-weight:700;text-decoration:none;text-align:center">Get Started Free</a>`;
      } else {
        btn.innerHTML = `<a href="../../user/register.php" style="display:block;padding:.65rem 1rem;border-radius:999px;border:1px solid var(--line);color:var(--ink);font-size:.88rem;font-weight:600;text-decoration:none;text-align:center">Sign Up to Upgrade</a>`;
      }
    } else {
      // Logged in, not current
      btn.innerHTML = `<a href="../../contact?subject=Upgrade+to+${encodeURIComponent(slug)}" style="display:block;padding:.65rem 1rem;border-radius:999px;border:1px solid var(--line);color:var(--ink);font-size:.88rem;font-weight:600;text-decoration:none;text-align:center">Upgrade</a>`;
    }
  });

  // Show current plan banner
  if (userPlan) {
    const icons = {free:'🆓',pro:'⭐',premium:'🚀',lifetime:'♾️'};
    const banner = document.getElementById('currentPlanBanner');
    if (banner) {
      banner.style.display = 'inline-flex';
      document.getElementById('currentPlanLabel').textContent = icons[userPlan] + ' ' + userPlan.charAt(0).toUpperCase() + userPlan.slice(1);
      document.getElementById('currentPlanCredits').textContent = credits.toLocaleString();
    }
  }
}

function showLoginOverlay() {
  const overlay = document.createElement('div');
  overlay.id = 'loginOverlay';
  overlay.style.cssText = 'position:fixed;inset:0;background:rgba(10,14,26,.85);backdrop-filter:blur(6px);z-index:500;display:flex;align-items:center;justify-content:center;padding:20px';
  overlay.innerHTML = `
    <div style="background:var(--surface);border:1px solid var(--line);border-radius:16px;padding:40px 32px;max-width:400px;width:100%;text-align:center">
      <div style="font-size:2.5rem;margin-bottom:16px">🔐</div>
      <h2 style="font-size:1.4rem;margin-bottom:8px">Sign In Required</h2>
      <p style="color:var(--muted);font-size:.92rem;margin-bottom:24px">Create a free account to use AI Video HD.<br>Start with <strong style="color:var(--teal)">30 free credits</strong> every month.</p>
      <a href="../../user/login.php?redirect=${encodeURIComponent(window.location.pathname)}"
         style="display:block;padding:12px;background:var(--teal);color:var(--on-teal);border-radius:8px;font-weight:700;text-decoration:none;margin-bottom:10px">Sign In</a>
      <a href="../../user/register.php"
         style="display:block;padding:12px;border:1px solid var(--line);color:var(--ink);border-radius:8px;font-weight:600;text-decoration:none">Create Free Account</a>
      <a href="../../" style="display:block;margin-top:14px;font-size:.82rem;color:var(--muted);text-decoration:none">← Back to tools</a>
    </div>`;
  document.body.appendChild(overlay);
}

async function checkAndDeductCredits(resolution) {
  if (!userLoggedIn) { showLoginOverlay(); return false; }
  const costMap = {720:5, 1080:10, 1440:15, 2160:25};
  const cost = costMap[resolution] || 10;

  const res = await fetch(API, {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:`action=deduct&tool=ai-video-hd&resolution=${resolution}&description=Video+enhanced+to+${resolution}p`
  });
  const data = await res.json();

  if (!data.success) {
    if (data.error === 'insufficient_credits') {
      showAlert('error',
        `Not Enough Credits`,
        `You need <strong>${data.credits_needed}</strong> credits for ${resolution}p but only have <strong>${data.credits_have}</strong>. <a href="../../user/dashboard.php" style="color:#fca5a5;text-decoration:underline">Upgrade your plan →</a>`
      );
    }
    return false;
  }
  userCreditsAvailable = data.credits_remaining;
  document.getElementById('userCredits').textContent = data.credits_remaining.toLocaleString();
  showAlert('success', `${cost} Credits Used`, `${data.credits_remaining.toLocaleString()} credits remaining on your account.`);
  return true;
}

checkUserStatus();
</script>
</body>
</html>
