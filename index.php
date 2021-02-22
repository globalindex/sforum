<?php

declare(strict_types=1);

require_once "bootstrap.php";

$active_page    = "index";

$sql = <<<SQL
SELECT 
`posts`.*, 
`users`.`name`, 
`comments`.`created_at` AS `comment_created_at`, 
COUNT(`comments`.`post_id`) AS COUNT
FROM `posts` 
LEFT JOIN `users` ON `posts`.`user_id` = `users`.`id` 
LEFT JOIN `comments` ON `posts`.`id` = `comments`.`post_id` 
GROUP BY `comments`.`post_id`, `posts`.`id` 
ORDER BY `comments`.`created_at` DESC, `posts`.`updated_at` DESC
SQL;

$posts = db_raw_select($sql);

db_disconnect();

include PATH.'parts/head.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between">
      <h1 class="text-3xl font-bold leading-tight text-gray-900 mb-5 mt-5">Forum Übersicht</h1>
      <?php if (auth_user()): ?>
      <div class="px-4 py-5 text-right">
          <a href="<?= BASE_URL ?>post_add.php">
          <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
              </svg>
              Beitrag erstellen
          </button>
          </a>
      </div>
      <?php endif; ?>
    </div>
    <div class="flex flex-col">
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
      <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Thema / Topic
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Autor
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Kommentare
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Erstellt am:
              </th>
              <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">Edit</span>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
          <?php if ($posts): ?>
            <?php foreach ($posts as $post): ?>
            <tr>
              <td class="px-6 py-4">
                <div class="flex">
                  <div class="flex-shrink-0 h-10 w-10 pt-1">
                    <div class="text-xs text-indigo-400">
                        ID: <?= $post['id'] ?>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-2xl font-semibold font-sans text-gray-900 sm:text-xl"><a class="hover:text-indigo-400" href="<?= BASE_URL ?>post_details.php?post_id=<?= $post['id'] ?>"><?= e($post['title']) ?></a></div>
                    <div class="text-sm text-gray-500"><?= mb_substr(strip_tags($post['content'], "<p><b>"), 0, 250) ?><?= (mb_strlen(strip_tags($post['content'], "<p><b>")) > 250) ? " ..." : "" ?></div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-500">
                  <?= e($post['name']) ?>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                    <?= ($post['COUNT']) ? $post['COUNT'] : "0" ?>
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <?= date("d.m.Y", strtotime($post['created_at'])) ?> um <?= date("H:i", strtotime($post['created_at'])) ?>
                <div class="text-xs text-indigo-400">
                  <?php if ($post['comment_created_at']): ?>
                  Zuletzt kommentiert: <?= date("d.m.Y", strtotime($post['comment_created_at'])) ?> um <?= date("H:i", strtotime($post['comment_created_at'])) ?>
                  <?php endif; ?>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <?php if (auth_user('id') === $post['user_id']): ?>
                <a href="<?= BASE_URL.'post_edit.php?post_id='.$post['id'] ?>" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr><td colspan="5">Keine Beiträge im Forum vorhanden.</td><tr>
            <?php endif; ?>
          </tbody>
        </table>
        <?php if (auth_user()): ?>
        <div class="border-t border-gray-200">
          <div class="px-4 py-3 bg-indigo-50 text-right">
              <a href="<?= BASE_URL ?>post_add.php">
              <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                  </svg>
                  Beitrag erstellen
              </button>
              </a>
          </div>
        </div>
        <?php endif; ?>

        </div>
        </div>
        </div>
        </div>

    </div>
</div>

<?php

include PATH.'parts/foot.php';
