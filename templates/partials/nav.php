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
?>
<?php require __DIR__ . '/subnav.php'; ?>

<nav class="tabbar" aria-label="<?= e(t('nav.label')) ?>">
  <ul>
    <?php foreach ($tabs as $key => $href): ?>
      <li>
        <a href="<?= e($href) ?>" <?= $tab === $key ? 'aria-current="page"' : '' ?>><?= e(t('nav.' . $key)) ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
  <form method="post" action="/logout" class="inline">
    <?= csrf_field() ?>
    <button type="submit"><?= e(t('nav.logout')) ?></button>
  </form>
</nav>
