<?php
$config = require __DIR__ . '/config.php';
$baseUrl = $config['app']['base_url'];
?>
<nav class="top-nav">
    <div class="nav-brand">
        <span class="nav-icon">&#128218;</span>
        <span class="nav-title"><?= htmlspecialchars($config['app']['title']) ?></span>
    </div>
    <div class="nav-links">
        <a href="<?= $baseUrl ?>/" class="nav-link active">Bücher</a>
        <a href="/linkmanager/" class="nav-link">Links</a>
        <a href="/befehlsmanager/" class="nav-link">Befehle</a>
        <a href="/host2host/" class="nav-link">Host2Host</a>
    </div>
</nav>
