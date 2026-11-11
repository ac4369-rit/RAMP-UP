<?php /** @var ?array $project @var array $milestones @var array $events @var int $daysLeft @var string $state */ ?>
<h1><?= e(t('status.title')) ?></h1>

<?php if ($state === 'error'): ?>
  <?php $retryHref = $_SERVER['REQUEST_URI']; require __DIR__ . '/../partials/states.php'; ?>
<?php elseif ($state === 'empty'): ?>
  <?php $messageKey = 'status.empty'; require __DIR__ . '/../partials/states.php'; ?>
<?php else: ?>
  <section aria-labelledby="sprint">
    <h2 id="sprint"><?= e(t('status.sprint', ['number' => $project['sprint_number']])) ?>: <?= e($project['sprint_name']) ?></h2>
    <p><?= e($project['sprint_goal']) ?></p>
    <p class="meta">
      <?= e(format_date($project['sprint_starts_on'])) ?> – <?= e(format_date($project['sprint_ends_on'])) ?>
      · <?= e(t('status.daysLeft', ['count' => $daysLeft])) ?>
    </p>
  </section>

  <h2><?= e(t('status.path')) ?></h2>
  <div class="timeline">
    <?php foreach (['done' => 'status.achieved', 'current' => 'status.now', 'upcoming' => 'status.next'] as $key => $label): ?>
      <div class="<?= $key === 'current' ? 'current' : '' ?>">
        <h3><?= e(t($label)) ?></h3>
        <?php if (!$milestones[$key]): ?>
          <p class="meta"><?= e(t('status.none')) ?></p>
        <?php else: ?>
          <ul class="plain">
            <?php foreach ($milestones[$key] as $m): ?>
              <li>
                <?= e($m['name']) ?>
                <?php if ($m['due_on']): ?><br><small class="meta"><?= e(t('status.due', ['date' => format_date($m['due_on'])])) ?></small><?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <h2><?= e(t('status.happening')) ?></h2>
  <?php if ($events): $items = $events; require __DIR__ . '/../partials/event_list.php'; ?>
  <?php else: ?><p class="meta"><?= e(t('status.noEvents')) ?></p><?php endif; ?>
<?php endif; ?>

<div class="actions"><a class="button" href="/missed"><?= e(t('status.back')) ?></a></div>
