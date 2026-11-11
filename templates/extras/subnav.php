<?php
/** Menú de sección (lo que en el wireframe abre el icono): necesita $tab. */
$sections = [
    'pinned'     => [['/missed', 'sub.updates'], ['/status', 'sub.status']],
    'understand' => [['/decisions', 'sub.decisions'], ['/glossary', 'sub.glossary'], ['/resources', 'sub.resources']],
    'search'     => [['/search', 'sub.search'], ['/ask', 'sub.ask']],
];
$items = $sections[$tab] ?? [];
$path  = rtrim((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/') ?: '/';
?>
<?php if ($items): ?>
<nav class="subnav" aria-label="<?= e(t('nav.sections')) ?>">
  <ul>
    <?php foreach ($items as [$href, $key]):
        $on = $path === $href || str_starts_with($path, $href . '/'); ?>
      <li><a href="<?= e($href) ?>" <?= $on ? 'aria-current="page"' : '' ?>><?= e(t($key)) ?></a></li>
    <?php endforeach; ?>
  </ul>
</nav>
<?php endif; ?>
