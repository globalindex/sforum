<?php
declare(strict_types=1);

require '../bootstrap.php';

$active_page = 'register';

if (request_is('post')) {

    $name = request('name');
    $email = request('email');
    $password = request('password');
    $password_confirmation = request('password_confirmation');

    if ($name === '') {
        $errors['name'] = 'Name cannot be empty.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please provide a valid email address.';
    }

    if ($email === '') {
        $errors['email'] = 'Email cannot be empty.';
    }

    if ($password !== $password_confirmation) {
        $errors['password'] = 'The passwords do not match.';
    }

    if (mb_strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters long.';
    }

    if ($password === '') {
        $errors['password'] = 'Password cannot be empty.';
    }

    if (!$errors) {
        $user = db_raw_first(
            "SELECT * FROM `users` WHERE `email` = " . db_prepare($email)
        );

        if ($user) {
            $errors['email'] = 'This email already exists in our database.';
        }
    }

    if (!$errors) {
        db_insert('users', [
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);

        redirect(BASE_URL.'auth/login.php');
    }
}

include PATH.'parts/head.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="mt-10 sm:mt-0 py-12 px-4">
  <div class="md:grid md:grid-cols-3 md:gap-6">
    <div class="md:col-span-1">
      <div class="px-4 sm:px-0">
        <h3 class="text-lg font-medium leading-6 text-gray-900">Personal Information</h3>
        <p class="mt-1 text-sm text-gray-600">
        Already have an account? Then go to the <a class="text-purple-600" href="<?= BASE_URL.'auth/login.php' ?>">login page</a>.
        </p>
      </div>
    </div>
    <div class="mt-5 md:mt-0 md:col-span-2">
      <form action="<?= BASE_URL.'auth/register.php' ?>" method="POST">
        <div class="shadow overflow-hidden sm:rounded-md">
          <div class="px-4 py-5 bg-white sm:p-6">
            <div class="grid grid-cols-6 gap-6">
              <div class="col-span-6 sm:col-span-3">
                <?= error_for($errors, 'name') ?>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" id="name" autocomplete="given-name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
              </div>

              <div class="col-span-6 sm:col-span-3">
                <?= error_for($errors, 'email') ?>
                <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                <input type="text" name="email" id="email" autocomplete="email" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
              </div>

              <div class="col-span-6 sm:col-span-3">
                <?= error_for($errors, 'password') ?>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
              </div>

              <div class="col-span-6 sm:col-span-3">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Password Confirmation</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
              </div>
            </div>
          </div>
          <div class="px-4 py-3 bg-indigo-50 text-right sm:px-6">
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Register
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
