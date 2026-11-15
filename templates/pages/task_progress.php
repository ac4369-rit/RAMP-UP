<p><a href="/tasks/<?= (int) $task['id'] ?>">← <?= e(t('tasks.backToTask')) ?></a></p>
<h1><?= e(t('tasks.progress.title')) ?></h1>
<p class="meta"><?= e(tc($task['title'])) ?></p>
<p><?= e(t('tasks.progress.intro')) ?></p>

<?php if ($errors): ?>
  <div class="notice" role="alert" id="form-errors">
    <ul><?php foreach ($errors as $error): ?><li><?= e(t($error, ['max' => $max])) ?></li><?php endforeach; ?></ul>
  </div>
<?php endif; ?>

<form method="post" action="/tasks/<?= (int) $task['id'] ?>/progress">
  <?= csrf_field() ?>
  <label for="status"><?= e(t('tasks.progress.status')) ?></label>
  <select id="status" name="status">
    <?php foreach ($statuses as $s): ?>
      <option value="<?= e($s) ?>" <?= $s === $form['status'] ? 'selected' : '' ?>><?= e(t('tasks.status.' . $s)) ?></option>
    <?php endforeach; ?>
  </select>

  <label for="note"><?= e(t('tasks.progress.note')) ?></label>
  <textarea id="note" name="note" rows="5" maxlength="<?= $max ?>"
            placeholder="<?= e(t('tasks.progress.placeholder')) ?>"
            <?= $errors ? 'aria-invalid="true" aria-describedby="form-errors"' : '' ?>><?= e($form['note']) ?></textarea>

  <div class="actions"><button type="submit" class="primary"><?= e(t('tasks.progress.save')) ?></button></div>
</form>

<p class="meta">
  <?= e(t('tasks.progress.finished')) ?>
  <a href="/tasks/<?= (int) $task['id'] ?>/review"><?= e(t('tasks.detail.review')) ?></a>
</p>
