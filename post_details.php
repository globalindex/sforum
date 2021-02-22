<?php

declare(strict_types=1);

require_once "bootstrap.php";

$active_page    = "post";
$post_id        = (int) query('post_id');
$delete         = (int) query('delete');
$comm_delete    = (int) query('comm_delete');
$page_title     = "FORUM :: Diskussion Details";

$sql = <<<SQL
SELECT `posts`.*, `users`.`name` AS `user` 
FROM `posts` JOIN `users` ON `posts`.`user_id` = `users`.`id` 
WHERE `posts`.`id` = $post_id
SQL;

$post = db_raw_first($sql);

$sql_comments = <<<COMMENT
SELECT `comments`.*, `users`.`name` AS `user` 
FROM `comments` JOIN `users` ON `comments`.`user_id` = `users`.`id` 
WHERE `comments`.`post_id` = $post_id
COMMENT;

$comments = db_raw_select($sql_comments);

if ($delete === 1) 
{
  db_delete('posts', $post_id);
  redirect(BASE_URL.'index.php');
}

if ($comm_delete != "") 
{
  $del_comment = db_raw_first("SELECT `user_id` FROM `comments` WHERE `id` = ". $comm_delete);

  if ($del_comment['user_id'] != auth_id())
  {
    $errors['message'] = "Sie können Kommentare anderer Benutzer nicht löschen.";
  } else {
    db_delete('comments', $comm_delete);
    redirect(BASE_URL.'post_details.php?post_id='.$post_id);
  }
}

db_disconnect();

include PATH.'parts/head.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

<h1 class="text-3xl font-bold leading-tight text-gray-900 mb-5 mt-5">Forum Beitrag lesen und kommentieren</h1>

<div class="bg-white shadow overflow-hidden sm:rounded-lg">
  <div class="px-4 py-5 sm:px-6 flex justify-between">
    <div class="">
    <h3 class="text-2xl leading-6 font-semibold mb-2 text-gray-900">
        <?= e($post['title']) ?>
    </h3>
    <div class="max-w-2xl flex">
      <div class="mr-2">
        <svg class="-ml-1 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
        </svg>
      </div>
      <div class="text-sm text-gray-500">
        <?= e($post['user']) ?>
      </div>
    </div>
    </div>
    <div class="text-right text-xs text-gray-400">
        <div>Erstellt am: <?= date("d.m.Y", strtotime($post['created_at'])) ?> um <?= date("H:i", strtotime($post['created_at'])) ?></div>
        <div>Geändert am: <?= date("d.m.Y", strtotime($post['updated_at'])) ?> um <?= date("H:i", strtotime($post['updated_at'])) ?></div>
    </div>
  </div>
  <div class="border-t border-gray-200">
    <div class="px-4 py-5 sm:px-6 font-normal text-gray-900">
        <?= $post['content'] ?>
    </div>
  </div>
  <?php if (auth_user('id') === $post['user_id']): ?>
    <div class="border-t border-gray-200">
        <div class="px-4 py-3 bg-indigo-50 flex justify-end items-center text-right">
          <svg class="-ml-1 mr-2 h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
          <?php if ($comments): ?>
            <span class="text-sm text-gray-300 mr-5">Beitrag löschen</span>
          <?php else: ?>
            <a class="text-sm text-gray-400 hover:text-indigo-700 mr-5" href="<?= BASE_URL.'post_edit.php?post_id='.$post_id.'&delete=1' ?>">Beitrag löschen</a>
          <?php endif; ?>
          <a href="<?= BASE_URL ?>post_edit.php?post_id=<?= $post['id'] ?>">
            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
              </svg>
              Beitrag bearbeiten
            </button>
          </a>
        </div>
    </div>
    <?php endif; ?>
</div>
</div>


<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-5">
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
  <div class="px-4 py-3 sm:px-6">
    <h4 class="text-xl leading-6 font-semibold text-gray-900">
        Kommentare
    </h4>
    <?= error_for($errors, 'message') ?>
  </div>
  <div class="border-t border-gray-200">
    <?php if ($comments): ?>
        <?php foreach ($comments as $comment): ?>
        <div class="px-5 py-5">
            <div class="flex justify-between mb-3">
                <div class="max-w-2xl flex">
                  <div class="mr-2">
                    <svg class="-ml-1 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <div class="text-sm text-gray-500">
                    <?= $comment['user'] ?>
                  </div>
                </div>
                <div class="flex justify-end items-center text-right">
                  <div class="text-right text-xs text-gray-400">Gepostet am: <?= date("d.m.Y", strtotime($comment['created_at'])) ?> um <?= date("H:i", strtotime($comment['created_at'])) ?></div>
                  <?php if (auth_id() == $comment['user_id']): ?>
                  <a class="ml-5" href="<?= BASE_URL.'post_details.php?post_id='.$post['id'].'&comm_delete='.$comment['id'] ?>">
                    <svg class="-ml-1 h-5 w-5 text-indigo-300 hover:text-indigo-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                  </a>
                  <?php endif; ?>
                </div>
            </div>
            <div class="font-normal text-gray-900"><?= e($comment['comment']) ?></div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="px-5 py-5">
            <p class="font-normal text-gray-900">Keine Kommentare vorhanden.</p>
        </div>
    <?php endif; ?>
    <?php if (auth_user()): ?>
        <div class="border-t border-gray-200">
            <div class="px-4 py-3 bg-indigo-50 text-right">
            <a href="<?= BASE_URL ?>comment_add.php?post_id=<?= $post['id'] ?>">
            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                Kommentar schreiben
            </button>
            </a>
            </div>
        </div>
    <?php endif; ?>
  </div>
</div>

<?php
include PATH.'parts/foot.php';

