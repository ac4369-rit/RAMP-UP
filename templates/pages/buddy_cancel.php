<h1><?= e(t('buddy.cancelTitle')) ?></h1>
<p><?= e(t('buddy.cancelBody', ['name' => $call['buddy_name'], 'day' => weekday_name((int) $call['weekday'])])) ?></p>
<form method="post" action="/buddy/<?= (int) $call['id'] ?>/cancel">
  <?= csrf_field() ?>
  <div class="actions">
    <button type="submit" class="danger"><?= e(t('buddy.cancelConfirm')) ?></button>
    <a class="button" href="/buddy"><?= e(t('buddy.cancelKeep')) ?></a>
  </div>
</form>
