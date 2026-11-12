<?php $flash = flash_take(); ?>
<?php if ($flash !== null): ?>
  <div class="notice" role="status"><?= e(t($flash['key'], $flash['params'])) ?></div>
<?php endif; ?>
