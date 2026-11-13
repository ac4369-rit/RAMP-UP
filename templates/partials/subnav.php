<?php
$sections = [
    'pinned'     => [['/home', 'sub.home'], ['/project', 'sub.project'], ['/missed', 'sub.updates'], ['/status', 'sub.status']],
    'understand' => [['/team', 'sub.team'], ['/rules', 'sub.rules'], ['/decisions', 'sub.decisions'], ['/glossary', 'sub.glossary'], ['/resources', 'sub.resources']],
    'search'     => [['/search', 'sub.search'], ['/ask', 'sub.ask']],
    'todo'       => [['/tasks', 'sub.tasks'], ['/tasks/starter', 'sub.starter'], ['/profile', 'sub.profile'], ['/access', 'sub.access'], ['/buddy', 'sub.buddy'], ['/hello', 'sub.hello'], ['/notifications', 'sub.notifications'], ['/check', 'sub.check']],
];
$items = $sections[$tab] ?? [];
$path  = rtrim((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/') ?: '/';

// Only ONE link is marked as current: the longest one that matches the URL
// (so /tasks/starter marks "Starter tasks", not also "My tasks").
$current = '';
foreach ($items as [$href]) {
    if (($path === $href || str_starts_with($path, $href . '/')) && strlen($href) > strlen($current)) {
        $current = $href;
    }
}
?>
<?php if ($items): ?>
<nav class="subnav" aria-label="<?= e(t('nav.sections')) ?>">
  <ul>
    <?php foreach ($items as [$href, $key]):
        $on = $href === $current; ?>
      <li><a href="<?= e($href) ?>" <?= $on ? 'aria-current="page"' : '' ?>><?= e(t($key)) ?></a></li>
    <?php endforeach; ?>
  </ul>
</nav>
<?php endif; ?>
