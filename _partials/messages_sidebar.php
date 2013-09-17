<?php $user = get_user(); ?>
<ul class="nav nav-pills nav-stacked">
  <li <?=_is_active($active, 'messages')?>>
    <a href="<?=root_path('messages.php')?>">
      <span class="badge pull-right"><?=count_messages($user->id)?></span>
      <i class="glyphicon glyphicon-inbox"></i>
    Message Inbox
    </a>
  </li>
  <li <?=_is_active($active, 'sent-messages')?>>
    <a href="<?=root_path('sent_messages.php')?>">
      <i class="glyphicon glyphicon-folder-open"></i>
      Sent Messages
    </a>
  </li>
  <li <?=_is_active($active, 'compose-message')?>>
    <a href="<?=root_path('compose_message.php')?>">
      <i class="glyphicon glyphicon-pencil"></i>
      Compose Message
    </a>
  </li>
  <?php if ($user->checkPermission('accessAdminPanel')): ?>
  <li><br></li>
  <li>
    <a href="<?=root_path('admin/')?>">
      <i class="glyphicon glyphicon-home"></i>
      Admin Panel
    </a>
  </li>
  <?php endif; ?>
</ul>
