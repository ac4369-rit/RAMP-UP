<p><a href="/tasks/<?= (int) $task['id'] ?>">← <?= e(t('tasks.backToTask')) ?></a></p>
<h1><?= e(t('tasks.blocked.title')) ?></h1>
<p class="meta"><?= e(tc($task['title'])) ?></p>
<p><?= e(t('tasks.blocked.intro')) ?></p>

<?php if ($errors): ?>
  <div class="notice" role="alert" id="form-errors">
    <ul><?php foreach ($errors as $error): ?><li><?= e(t($error, ['max' => $max])) ?></li><?php endforeach; ?></ul>
  </div>
<?php endif; ?>

<form method="post" action="/tasks/<?= (int) $task['id'] ?>/blocked">
  <?= csrf_field() ?>
  <label for="reason"><?= e(t('tasks.blocked.reason')) ?></label>
  <textarea id="reason" name="reason" rows="5" maxlength="<?= $max ?>" required
            <?= $errors ? 'aria-invalid="true" aria-describedby="form-errors"' : '' ?>><?= e($form['reason']) ?></textarea>
  <div class="actions"><button type="submit" class="primary"><?= e(t('tasks.blocked.submit')) ?></button></div>
</form>
