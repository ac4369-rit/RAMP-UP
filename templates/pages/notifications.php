<h1><?= e(t('notify.title')) ?></h1>
<p class="meta"><?= e(t('notify.intro')) ?></p>
<?php require __DIR__ . '/../partials/flash.php'; ?>

<?php if ($state === 'error'): ?>
  <?php $retryHref = $_SERVER['REQUEST_URI']; require __DIR__ . '/../partials/states.php'; ?>
<?php else: ?>
  <form method="post" action="/notifications">
    <?= csrf_field() ?>
    <fieldset>
      <legend><?= e(t('notify.legend')) ?></legend>
      <ul class="plain">
        <li>
          <input id="only_mentions" type="checkbox" name="only_mentions" value="1" <?= (int) $settings['only_mentions'] ? 'checked' : '' ?>>
          <label for="only_mentions"><?= e(t('notify.mentions')) ?></label>
        </li>
        <li>
          <input id="daily_summary" type="checkbox" name="daily_summary" value="1" <?= (int) $settings['daily_summary'] ? 'checked' : '' ?>>
          <label for="daily_summary"><?= e(t('notify.summary', ['time' => format_time($settings['daily_summary_time'])])) ?></label>
        </li>
        <li>
          <label for="daily_summary_time"><?= e(t('notify.summaryTime')) ?></label>
          <input id="daily_summary_time" type="time" name="daily_summary_time" value="<?= e(substr($settings['daily_summary_time'], 0, 5)) ?>">
        </li>
        <li>
          <input id="all_channel_messages" type="checkbox" name="all_channel_messages" value="1" <?= (int) $settings['all_channel_messages'] ? 'checked' : '' ?>>
          <label for="all_channel_messages"><?= e(t('notify.all')) ?></label>
        </li>
      </ul>
    </fieldset>
    <div class="actions"><button type="submit" class="primary"><?= e(t('notify.save')) ?></button></div>
  </form>
<?php endif; ?>
