<?php
    $active_page = $active_page ?? '';
?>
<div>
<nav class="bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <img class="h-8 w-8" src="https://tailwindui.com/img/logos/workflow-mark-indigo-500.svg" alt="Workflow">
                </div>
                <div class="md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="<?= BASE_URL."index.php" ?>" class="<?= $active_page === "index" ? "bg-gray-900 text-white " : "text-gray-300 hover:bg-gray-700 hover:text-white " ?>px-3 py-2 rounded-md text-sm font-medium">Home</a>
                        <?php if (auth_user()) : ?>
                        <a href="<?= BASE_URL."profile.php" ?>" class="<?= $active_page === "profile" ? "bg-gray-900 text-white " : "text-gray-300 hover:bg-gray-700 hover:text-white " ?>px-3 py-2 rounded-md text-sm font-medium">Profile</a>
                        <a href="<?= BASE_URL."auth/logout.php" ?>" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Logout</a>
                        <?php else : ?>
                        <a href="<?= BASE_URL."auth/register.php" ?>" class="<?= $active_page === "register" ? "bg-gray-900 text-white " : "text-gray-300 hover:bg-gray-700 hover:text-white " ?>px-3 py-2 rounded-md text-sm font-medium">Register</a>
                        <a href="<?= BASE_URL."auth/login.php" ?>" class="<?= $active_page === "login" ? "bg-gray-900 text-white " : "text-gray-300 hover:bg-gray-700 hover:text-white " ?>px-3 py-2 rounded-md text-sm font-medium">Login</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
</div>
