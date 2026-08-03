<?php
require_once 'db-config.php';
$conn = getDbConnection();
$result = $conn->query("SELECT id, category, intention_text, prayer_count FROM prayers WHERE is_approved = 1 ORDER BY created_at DESC LIMIT 100");

$categoryLabels = [
    'health' => 'Health & Healing',
    'family' => 'Family',
    'peace' => 'Peace & Anxiety',
    'work' => 'Work & Career',
    'grief' => 'Grief & Loss',
    'relationships' => 'Relationships',
    'strength' => 'Strength & Hope',
    'gratitude' => 'Gratitude',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Prayer Wall | Prayers and Healings</title>
<meta name="description" content="Take a quiet moment and pray for someone who needs it.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Karla:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root{--cream:#faf6ee;--cream-deep:#f2e9d8;--umber:#3e2a15;--umber-soft:#6b543a;--gold:#c9963e;--gold-deep:#a97328;--sage:#6b8f7b;--sage-deep:#4f6f5c;--line:rgba(62,42,21,0.14);}
  *{box-sizing:border-box;}
  body{margin:0;background:var(--cream);color:var(--umber);font-family:'Karla', sans-serif;line-height:1.7;}
  h1,h2,.display{font-family:'Fraunces', serif;font-weight:500;color:var(--umber);margin:0;}
  a{color:inherit;}
  .wrap{max-width:820px;margin:0 auto;padding:0 28px;}
  header{position:sticky;top:0;z-index:50;background:rgba(250,246,238,0.92);backdrop-filter:blur(6px);border-bottom:1px solid var(--line);}
  .nav{display:flex;align-items:center;justify-content:space-between;padding:14px 28px;max-width:1100px;margin:0 auto;}
  .brand{display:flex;align-items:center;gap:12px;text-decoration:none;}
  .brand img{height:44px;width:44px;border-radius:50%;}
  .brand span{font-family:'Fraunces',serif;font-size:1.15rem;}
  nav ul{list-style:none;display:flex;gap:30px;margin:0;padding:0;}
  nav ul a{text-decoration:none;font-size:0.95rem;color:var(--umber-soft);}
  .menu-toggle{display:none;background:none;border:none;font-size:1.6rem;color:var(--umber);cursor:pointer;}

  .page-hero{padding:60px 0 20px;text-align:center;}
  .eyebrow{display:inline-block;font-size:0.78rem;letter-spacing:2.5px;text-transform:uppercase;color:var(--sage-deep);margin-bottom:16px;font-weight:600;}
  .page-hero h1{font-size:2.3rem;margin-bottom:14px;}
  .page-hero p{color:var(--umber-soft);}

  .wall{padding:20px 0 80px;display:flex;flex-direction:column;gap:16px;}
  .prayer-card{background:#fff;border:1px solid var(--line);border-radius:16px;padding:24px 26px;display:flex;justify-content:space-between;align-items:flex-start;gap:20px;flex-wrap:wrap;}
  .prayer-card .tag{font-size:0.72rem;letter-spacing:1.5px;text-transform:uppercase;color:var(--gold-deep);font-weight:700;margin-bottom:8px;display:block;}
  .prayer-card p{margin:0;color:var(--umber-soft);max-width:520px;}
  .pray-btn{background:var(--umber);color:var(--cream);border:none;padding:11px 20px;border-radius:100px;font-size:0.9rem;font-weight:600;cursor:pointer;white-space:nowrap;}
  .pray-btn:hover{background:var(--gold-deep);}
  .pray-btn:disabled{opacity:0.6;cursor:default;}
  .count{font-size:0.8rem;color:var(--umber-soft);margin-top:6px;display:block;}
  .empty{text-align:center;color:var(--umber-soft);padding:40px 0;}

  footer{border-top:1px solid var(--line);padding:44px 0;}
  .footer-inner{max-width:1100px;margin:0 auto;padding:0 28px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;}
  footer .fine{font-size:0.82rem;color:var(--umber-soft);}

  @media (max-width:860px){
    nav ul{position:fixed;top:74px;left:0;right:0;background:var(--cream);flex-direction:column;padding:20px 28px;border-bottom:1px solid var(--line);display:none;}
    nav ul.open{display:flex;}
    .menu-toggle{display:block;}
  }
</style>
</head>
<body>

<header>
  <div class="nav">
    <a href="/" class="brand"><img src="/images/home/prayersandhealings-logo.png" alt="Prayers and Healings logo"><span>Prayers and Healings</span></a>
    <button class="menu-toggle" id="menuToggle">&#9776;</button>
    <nav><ul id="navMenu">
      <li><a href="/">Home</a></li>
      <li><a href="/about.html">About</a></li>
      <li><a href="/blog.html">Blog</a></li>
      <li><a href="/prayer-wall.php">Prayer Wall</a></li>
      <li><a href="/contact.html">Contact</a></li>
    </ul></nav>
  </div>
</header>

<div class="wrap">
  <div class="page-hero">
    <span class="eyebrow">Someone Needs Your Prayer</span>
    <h1>The Prayer Wall</h1>
    <p>Take a quiet moment. Somewhere, someone is hoping that they are not alone.</p>
  </div>

  <div class="wall" id="prayerWall">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="prayer-card" data-id="<?php echo (int)$row['id']; ?>">
          <div>
            <span class="tag"><?php echo htmlspecialchars($categoryLabels[$row['category']] ?? ucfirst($row['category'])); ?></span>
            <p><?php echo nl2br(htmlspecialchars($row['intention_text'])); ?></p>
            <span class="count"><span class="count-num"><?php echo (int)$row['prayer_count']; ?></span> prayers</span>
          </div>
          <button class="pray-btn" onclick="prayFor(this, <?php echo (int)$row['id']; ?>)">&#128591; I Prayed</button>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="empty">No prayers on the wall yet. Be the first to <a href="/request-prayer.html">request a prayer</a>.</p>
    <?php endif; ?>
  </div>
</div>

<footer>
  <div class="footer-inner">
    <a href="/" class="brand"><img src="/images/home/prayersandhealings-logo.png" alt="Prayers and Healings logo" style="height:34px;width:34px;"><span>Prayers and Healings</span></a>
    <p class="fine">&copy; <span id="year"></span> Prayers and Healings. Held with care, for every faith and every journey.</p>
  </div>
</footer>

<script>
  document.getElementById('year').textContent = new Date().getFullYear();
  document.getElementById('menuToggle').addEventListener('click', function(){
    document.getElementById('navMenu').classList.toggle('open');
  });

  async function prayFor(btn, id){
    btn.disabled = true;
    try {
      const formData = new FormData();
      formData.append('id', id);
      const res = await fetch('/pray-for.php', { method: 'POST', body: formData });
      const data = await res.json();
      if (data.success) {
        const card = btn.closest('.prayer-card');
        card.querySelector('.count-num').textContent = data.prayer_count;
        btn.textContent = '🙏 Prayed';
      } else {
        btn.disabled = false;
      }
    } catch(err) {
      btn.disabled = false;
    }
  }
</script>

</body>
</html>
