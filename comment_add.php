<?php

declare(strict_types=1);

require_once "bootstrap.php";

if (!auth_user()) 
{
    redirect(BASE_URL.'auth/login.php');
}

$active_page    = "post";
$page_title     = "FORUM :: Kommentar schreiben";
$comment        = request('comment');
$post_id        = (request_is("post")) ? request('post_id') : query('post_id');

if (request_is("post")) {
    if ($comment === "") 
    {
        $errors['comment'] = "Geben Sie bitte Kommentar-Inhalt ein.";
    }
    if (!$errors) 
    {
        db_insert('comments', [
            'comment'       => $comment,
            'user_id'       => auth_id(),
            'post_id'       => $post_id
        ]);

        redirect(BASE_URL.'post_details.php?post_id='. $post_id);
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
        <h3 class="text-lg font-medium leading-6 text-gray-900">Beitrag kommentieren</h3>
      </div>
    </div>
    <div class="mt-5 md:mt-0 md:col-span-2">
      <form action="<?= BASE_URL.'comment_add.php' ?>" method="POST">
        <input type="hidden" name="post_id" value="<?= $post_id ?>">
        <div class="shadow overflow-hidden sm:rounded-md">
          <div class="px-4 py-5 bg-white sm:p-6">
            <div class="grid grid-cols-6 gap-6">
              <div class="col-span-12 sm:col-span-6">
                <label for="comment" class="block text-sm font-medium text-gray-700">Kommentar</label>
                <div class="mt-1">
                    <textarea id="comment" name="comment" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Kommentar Content hier."></textarea>
                </div>
                <?= error_for($errors, 'comment') ?>
              </div>
            </div>
          </div>
          <div class="px-4 py-3 bg-indigo-50 text-right sm:px-6">
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Kommentar posten
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

