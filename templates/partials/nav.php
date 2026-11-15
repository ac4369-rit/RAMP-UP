<?php
if (\App\Auth::user() === null) {
    return;
}

$tabs = [
    'pinned'     => '/home',
    'understand' => '/decisions',
    'search'     => '/search',
    'todo'       => '/tasks',
];

// Small line icons for the bottom tab bar (decorative, the text label is what is read out).
$icons = [
    'pinned'     => '<path d="M12 17v5"/><path d="M9 10.8a2 2 0 0 1-1.1 1.8l-1.8.9A2 2 0 0 0 5 15.2V16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-.8a2 2 0 0 0-1.1-1.8l-1.8-.9a2 2 0 0 1-1.1-1.8V7a1 1 0 0 1 1-1 2 2 0 0 0 0-4H8a2 2 0 0 0 0 4 1 1 0 0 1 1 1z"/>',
    'understand' => '<path d="M2 4h6a4 4 0 0 1 4 4v13a3 3 0 0 0-3-3H2z"/><path d="M22 4h-6a4 4 0 0 0-4 4v13a3 3 0 0 1 3-3h7z"/>',
    'search'     => '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>',
    'todo'       => '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>',
];
?>
<?php require __DIR__ . '/subnav.php'; ?>

<nav class="tabbar" aria-label="<?= e(t('nav.label')) ?>">
  <ul>
    <?php foreach ($tabs as $key => $href): ?>
      <li>
        <a href="<?= e($href) ?>" <?= $tab === $key ? 'aria-current="page"' : '' ?>>
          <svg class="tabbar__icon" viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"
               fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?= $icons[$key] ?></svg>
          <span class="tabbar__label"><?= e(t('nav.' . $key)) ?></span>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>
