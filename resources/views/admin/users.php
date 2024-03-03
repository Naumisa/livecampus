<?php
$roles = [
	'user',
	'admin'
]
?>

<div class="relative overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
    <div class="flex flex-col px-4 py-3 space-y-3 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 lg:space-x-4">
        <div class="flex items-center flex-1 space-x-4">
            <h5>
                <span class="text-gray-500"><?= lang_get('show-users.users_count') ?> :</span>
                <span class="dark:text-white"><?= count($data['users']) ?></span>
            </h5>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-4 py-3"><?= lang_get('show-users.username') ?></th>
                    <th scope="col" class="px-4 py-3"><?= lang_get('show-users.email') ?></th>
	                <th scope="col" class="px-4 py-3"><?= lang_get('show-users.role') ?></th>
                    <th scope="col" class="px-4 py-3"><?= lang_get('show-users.created_at') ?></th>
                    <th scope="col" class="px-4 py-3"><?= lang_get('show-users.last_updated') ?></th>
                    <th scope="col" class="px-4 py-3"><?= lang_get('show-users.actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['users'] as $user) : ?>
                    <?php if ($user->id === auth_user()->id) continue; ?>
                    <tr class="border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <th scope="row" class="flex items-center px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <?= $user->username ?>
                        </th>
	                    <td class="px-4 py-2">
                            <span class="bg-primary-100 text-primary-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-primary-900 dark:text-primary-300">
                                <?= $user->email ?>
                            </span>
	                    </td>
                        <td class="px-4 py-2">
                            <?= lang_get("show-users.role_" . $roles[$user->role]) ?>
                        </td>
                        <td class="px-4 py-2"><?= $user->created_at ?></td>
                        <td class="px-4 py-2"><?= $user->updated_at ?></td>
                        <td class="px-4 py-2">
	                        ...
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <nav class="flex flex-col items-start justify-between p-4 space-y-3 md:flex-row md:items-center md:space-y-0" aria-label="Table navigation">
        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
            <?= lang_get('show-users.showing') ?>
            <span class="font-semibold text-gray-900 dark:text-white">1-<?= count($data['users'] ?? []) - 1 ?></span>
            <?= lang_get('show-users.showing_of') ?>
            <span class="font-semibold text-gray-900 dark:text-white"><?= count($data['users'] ?? []) ?></span>
        </span>
	    <?php if (isset($pagination)) : ?>
        <ul class="inline-flex items-stretch -space-x-px">
            <li>
                <a href="#" class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                    <span class="sr-only">Previous</span>
                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center justify-center px-3 py-2 text-sm leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">1</a>
            </li>
            <li>
                <a href="#" class="flex items-center justify-center px-3 py-2 text-sm leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">2</a>
            </li>
            <li>
                <a href="#" aria-current="page" class="z-10 flex items-center justify-center px-3 py-2 text-sm leading-tight border text-primary-600 bg-primary-50 border-primary-300 hover:bg-primary-100 hover:text-primary-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white">3</a>
            </li>
            <li>
                <a href="#" class="flex items-center justify-center px-3 py-2 text-sm leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">...</a>
            </li>
            <li>
                <a href="#" class="flex items-center justify-center px-3 py-2 text-sm leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">100</a>
            </li>
            <li>
                <a href="#" class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                    <span class="sr-only">Next</span>
                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            </li>
        </ul>
	    <?php endif; ?>
    </nav>
</div>
