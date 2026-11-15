<h1><?= e(t('hello.deleteTitle')) ?></h1>
<p><?= e(t('hello.deleteBody')) ?></p>
<blockquote><?= nl2br(e(tc($post['body']))) ?></blockquote>
<form method="post" action="/hello/<?= (int) $post['id'] ?>/delete">
  <?= csrf_field() ?>
  <div class="actions">
    <button type="submit" class="danger"><?= e(t('hello.deleteConfirm')) ?></button>
    <a class="button" href="/hello"><?= e(t('hello.deleteCancel')) ?></a>
  </div>
</form>
