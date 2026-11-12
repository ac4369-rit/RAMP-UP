<h1><?= e(t('hello.title')) ?></h1>
<?php require __DIR__ . '/../partials/flash.php'; ?>

<?php if ($errors): ?>
  <div class="notice" role="alert" id="form-errors">
    <ul><?php foreach ($errors as $error): ?><li><?= e(t($error, ['max' => $max])) ?></li><?php endforeach; ?></ul>
  </div>
<?php endif; ?>

<form method="post" action="/hello">
  <?= csrf_field() ?>
  <label for="body"><?= e(t('hello.label')) ?></label>
  <p class="meta" id="hello-hint"><?= e(t('hello.hint', ['max' => $max])) ?></p>
  <textarea id="body" name="body" rows="5" maxlength="<?= $max ?>" required
            aria-describedby="hello-hint<?= $errors ? ' form-errors' : '' ?>"><?= e($body) ?></textarea>
  <div class="actions"><button type="submit" class="primary"><?= e(t('hello.post')) ?></button></div>
</form>

<section aria-labelledby="wall">
  <h2 id="wall"><?= e(t('hello.wall')) ?></h2>
  <?php if ($state === 'error'): ?>
    <?php $retryHref = $_SERVER['REQUEST_URI']; require __DIR__ . '/../partials/states.php'; ?>
  <?php elseif (!$posts): ?>
    <?php $state = 'empty'; $messageKey = 'hello.empty'; require __DIR__ . '/../partials/states.php'; ?>
  <?php else: ?>
    <ol class="list">
      <?php foreach ($posts as $p): ?>
        <li>
          <strong><?= e($p['name']) ?></strong>
          <small class="meta"><?= e(format_datetime($p['created_at'])) ?></small>
          <p><?= nl2br(e($p['body'])) ?></p>
          <?php if ((int) $p['user_id'] === $me): ?>
            <a href="/hello/<?= (int) $p['id'] ?>/delete"><?= e(t('hello.delete')) ?></a>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ol>
  <?php endif; ?>
</section>
