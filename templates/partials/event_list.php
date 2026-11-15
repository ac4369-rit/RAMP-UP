<?php /** @var list<array> $items */ ?>
<ul class="list">
  <?php foreach ($items as $ev): ?>
    <li>
      <strong class="tag"><?= e(t('event.kind.' . $ev['kind'])) ?></strong>
      <?= e(tc($ev['title'])) ?>
      <br>
      <small class="meta">
        <?php if (!empty($ev['detail'])): ?><?= e(tc($ev['detail'])) ?> — <?php endif; ?>
        <?= e(t('time.daysAgo', ['count' => days_ago($ev['happened_at'])])) ?>
        <?php if (($ev['source'] ?? '') === 'github'): ?> — <?= e(t('missed.fromGithub')) ?><?php endif; ?>
      </small>
    </li>
  <?php endforeach; ?>
</ul>
