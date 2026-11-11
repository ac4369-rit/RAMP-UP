<?php /** @var ?array $decision @var array $options @var list<string> $roles @var bool $affectsMe @var string $state */ ?>
<p><a href="/decisions">← <?= e(t('decisions.back')) ?></a></p>

<?php if ($state === 'error'): ?>
  <h1><?= e(t('decisions.detail')) ?></h1>
  <?php $retryHref = $_SERVER['REQUEST_URI']; require __DIR__ . '/../partials/states.php'; ?>
<?php else: ?>
  <h1><?= e($decision['title']) ?></h1>
  <p class="meta">
    <?= e(t('decisions.decidedBy', ['name' => $decision['decided_by']])) ?>
    · <?= e(format_date($decision['decided_at'])) ?>
    · <?= e(t('decisions.status.' . $decision['status'])) ?>
  </p>
  <?php if ($affectsMe): ?><p><strong class="badge badge--role"><?= e(t('decisions.affectsMe')) ?></strong></p><?php endif; ?>
  <p class="meta"><?= e(t('decisions.affects')) ?>:
    <?= e(implode(', ', array_map(fn ($r) => t('role.' . $r), $roles))) ?></p>

  <details open>
    <summary><?= e(t('decisions.context')) ?></summary>
    <p><?= nl2br(e($decision['context'])) ?></p>
  </details>

  <details>
    <summary><?= e(t('decisions.options')) ?></summary>
    <?php if (!$options): ?>
      <p class="meta"><?= e(t('decisions.noOptions')) ?></p>
    <?php else: ?>
      <ul class="plain">
        <?php foreach ($options as $o): ?>
          <li>
            <strong><?= e($o['label']) ?></strong>
            <?php if ((int) $o['chosen']): ?><span class="badge badge--role"><?= e(t('decisions.chosen')) ?></span><?php endif; ?>
            <?php if ($o['description']): ?><br><small class="meta"><?= e($o['description']) ?></small><?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </details>

  <details open>
    <summary><?= e(t('decisions.consequences')) ?></summary>
    <p><?= nl2br(e($decision['consequences'])) ?></p>
  </details>

  <div class="actions">
    <a class="button" href="/ask?decision=<?= (int) $decision['id'] ?>"><?= e(t('decisions.askAbout')) ?></a>
  </div>
<?php endif; ?>
