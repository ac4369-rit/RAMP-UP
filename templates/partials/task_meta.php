<?php
/**
 * Status, priority and deadline of one task, used on several Part 3 screens.
 * Expects $t: a task row (status, priority, due_date; optional days_left / at_risk).
 */
?>
<span class="badge badge--<?= e($t['status']) ?>"><?= e(t('tasks.status.' . $t['status'])) ?></span>
<span class="meta"><?= e(t('tasks.priority.' . $t['priority'])) ?></span>
<?php if (!empty($t['due_date'])): ?>
  <span class="meta">· <?= e(t('tasks.due', ['date' => format_date($t['due_date'])])) ?>
    <?php if (isset($t['days_left']) && $t['status'] !== 'done'): ?>
      (<?= e($t['days_left'] < 0
            ? t('tasks.overdue', ['count' => -$t['days_left']])
            : t('tasks.daysLeft', ['count' => $t['days_left']])) ?>)
    <?php endif; ?>
  </span>
<?php endif; ?>
<?php if (!empty($t['at_risk'])): ?>
  <span class="badge badge--risk">⚠ <?= e(t('tasks.atRisk')) ?></span>
<?php endif; ?>
