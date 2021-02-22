<?php

declare(strict_types=1);

require_once "bootstrap.php";

$active_page    = "post";
$page_title     = "FORUM :: Beitrag ändern";
$title          = request('title');
$content        = request('content');
$delete         = (int) query('delete');
$post_id        = (request_is("post")) ? request('post_id') : query('post_id');

$sql = "SELECT * FROM `posts` WHERE `id` = ".$post_id;
$post = db_raw_first($sql);

if (!auth_user() || ($post['user_id'] != auth_id())) 
{
  redirect(BASE_URL.'index.php');
}

if (request_is("post")) {
  if ($title === "") 
  {
    $errors['title'] = "Geben Sie bitte Beitag Titel ein.";
  }
  if ($content === "") 
  {
    $errors['content'] = "Geben Sie bitte Inhalt ein.";
  }
  if (!$errors) 
  {
    db_update('posts', (int) $post_id, [
        'title'     => $title,
        'content'   => $content
    ]);
    
    redirect(BASE_URL.'post_details.php?post_id='.$post_id);
  }
}

if ($delete === 1) 
{
  $sql = db_raw_first("SELECT id FROM `comments` WHERE `post_id` = ". (int) $post_id);

  if ($sql != "")
  {
    $errors['message'] = "Sie können diesen Beitrag nicht löschen. Es existieren Kommentage dazu.";
  } else {
    db_delete('posts', (int) $post_id);
    redirect(BASE_URL.'index.php');
  }
}

db_disconnect();

include PATH.'parts/head.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="mt-10 sm:mt-0 py-12 px-4">
  <div class="md:grid md:grid-cols-3 md:gap-6">
    <div class="md:col-span-1">
      <div class="px-4 sm:px-0">
        <h3 class="text-lg font-medium leading-6 text-gray-900">Beitrag ändern</h3>
        <?= error_for($errors, 'message') ?>
      </div>
    </div>
    <div class="mt-5 md:mt-0 md:col-span-2">
      <form action="<?= BASE_URL.'post_edit.php' ?>" method="POST">
        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
        <div class="shadow overflow-hidden sm:rounded-md">
          <div class="px-4 py-5 bg-white sm:p-6">
            <div class="grid grid-cols-6 gap-6">
              <div class="col-span-12 sm:col-span-6">
                <label for="title" class="block text-sm font-medium text-gray-700">Beitrag Titel</label>
                <input type="text" name="title" id="title" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="<?= (isset($post['title'])) ? e($post['title']) : "" ?>">
                <?= error_for($errors, 'title') ?>
              </div>
              <div class="col-span-12 sm:col-span-6">
                <label for="content" class="block text-sm font-medium text-gray-700">Beitrag Content</label>
                <div class="mt-1">
                    <textarea id="content" name="content" rows="3" class="h-28 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md"><?= (isset($post['content'])) ? e($post['content']) : "" ?></textarea>
                </div>
                <?= error_for($errors, 'content') ?>
              </div>
            </div>
          </div>
          <div class="px-4 py-3 bg-indigo-50 flex justify-end items-center text-right sm:px-6">
            <svg class="-ml-1 mr-2 h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <a class="text-sm text-gray-400 hover:text-indigo-700 mr-5" href="<?= BASE_URL.'post_edit.php?post_id='.$post_id.'&delete=1' ?>">Beitrag löschen</a>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              Beitrag ändern
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
</div>

<?php
include PATH.'parts/foot.php';