<?php

declare(strict_types=1);

require_once "bootstrap.php";

if (!auth_user()) 
{
    redirect(BASE_URL.'auth/login.php');
}

$active_page    = "post";
$page_title     = "FORUM :: Diskussion starten";
$title          = request('title');
$content        = request('content');

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
        db_insert('posts', [
            'title'         => $title,
            'content'       => $content,
            'user_id'       => auth_id(),
        ]);

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
        <h3 class="text-lg font-medium leading-6 text-gray-900">Beitrag erstellen</h3>
      </div>
    </div>
    <div class="mt-5 md:mt-0 md:col-span-2">
      <form action="<?= BASE_URL.'post_add.php' ?>" method="POST">
        <div class="shadow overflow-hidden sm:rounded-md">
          <div class="px-4 py-5 bg-white sm:p-6">
            <div class="grid grid-cols-6 gap-6">
              <div class="col-span-12 sm:col-span-6">
                <label for="title" class="block text-sm font-medium text-gray-700">Beitrag Titel</label>
                <input type="text" name="title" id="title" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="<?= e($title) ?>">
                <?= error_for($errors, 'title') ?>
              </div>
              <div class="col-span-12 sm:col-span-6">
                <label for="title" class="block text-sm font-medium text-gray-700">Beitrag Content</label>
                <div class="mt-1">
                    <textarea id="content" name="content" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Beitrag Content hier."><?= e($content) ?></textarea>
                </div>
                <?= error_for($errors, 'content') ?>
              </div>
            </div>
          </div>
          <div class="px-4 py-3 bg-indigo-50 text-right sm:px-6">
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Beitrag posten
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

