<div class="relative overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
    <div class="flex flex-col px-4 py-3 space-y-3 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 lg:space-x-4">
        <div class="flex items-center flex-1 space-x-4">
        <?php if(count($data['files'])): ?>
            <h5>
                <span class="text-gray-500"><?= lang_get('dashboard.files_count') ?> :</span>
                <span class="dark:text-white"><?= count($data['files']) ?></span>
            </h5>
        <?php else: ?>
            <h5 class="dark:text-white">Vous n'avez aucun fichier pour le moment.</h5>
        <?php endif; ?> 
        </div>
        <div class="flex flex-col flex-shrink-0 space-y-3 md:flex-row md:items-center lg:justify-end md:space-y-0 md:space-x-3">
            <button data-modal-target="upload-modal" data-modal-toggle="upload-modal" type="button" class="flex items-center justify-center flex-shrink-0 px-3 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg focus:outline-none hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path clip-rule="evenodd" fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                </svg>
                <?= lang_get('dashboard.file_upload') ?>
            </button>
        </div>
    </div>
    <?php if(count($data['files'])): ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-4 py-3"><?= lang_get('file_info.name') ?></th>
                    <th scope="col" class="px-4 py-3"><?= lang_get('file_info.type') ?></th>
                    <th scope="col" class="px-4 py-3"><?= lang_get('file_info.size') ?></th>
                    <th scope="col" class="px-4 py-3"><?= lang_get('file_info.downloaded_count') ?></th>
                    <th scope="col" class="px-4 py-3"><?= lang_get('file_info.owner') ?></th>
                    <th scope="col" class="px-4 py-3"><?= lang_get('file_info.last_updated') ?></th>
                    <th scope="col" class="px-4 py-3"><?= lang_get('file_info.actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['files'] as $file) : ?>
                <tr class="border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <th scope="row" class="flex items-center px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        <?= $file['name_origine'] ?>
                    </th>
                    <td class="px-4 py-2">
                        <span class="bg-primary-100 text-primary-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-primary-900 dark:text-primary-300">
                            .<?=
                                // $file['extension']
                                pathinfo($data['user_storage_path'].$file['name_random'], PATHINFO_EXTENSION);
                            ?>
                        </span>
                    </td>
                    <td class="px-4 py-2">
                        <?= filesize($data['user_storage_path'].$file['name_random']) ?> Mo
                    </td>
                    <td class="px-4 py-2"><?= $file['download_count']." ".lang_get('file_info.count') ?></td>
                    <td class="px-4 py-2"><?= auth_user()->email ?></td>
                    <td class="px-4 py-2"><?= $file['updated_at'] ?></td>
                    <td class="px-4 py-2">
                        <div class="flex items-center space-x-2">
                            <a href="<?= routes_go_to_route('file/share') ?>" class="flex items-center pr-2.5 py-0.5 text-base font-bold text-gray-900 rounded-lg bg-gray-50 hover:bg-gray-100 group hover:shadow dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-white">
                                <span class="flex-1 ms-3 whitespace-nowrap">
                                    <?= lang_get('file_info.action_share') ?>
                                </span>
                            </a>
                            <a href="<?= routes_go_to_route('file/delete') ?>" class="flex items-center pr-2.5 py-0.5 text-base font-bold text-gray-900 rounded-lg bg-red-500 hover:bg-red-600 group hover:shadow dark:bg-red-800 dark:hover:bg-red-700 dark:text-white">
                                <span class="flex-1 ms-3 whitespace-nowrap">
                                    <?= lang_get('file_info.action_delete') ?>
                                </span>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <nav class="flex flex-col items-start justify-between p-4 space-y-3 md:flex-row md:items-center md:space-y-0" aria-label="Table navigation">
        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
          <?= lang_get('dashboard.showing') ?>
          <span class="font-semibold text-gray-900 dark:text-white">1-10</span>
          <?= lang_get('dashboard.showing_of') ?>
          <span class="font-semibold text-gray-900 dark:text-white">1000</span>
        </span>
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
    </nav>
    <?php endif; ?>
</div>

<!-- UPLOAD MODAL -->
<div id="upload-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="p-4 md:p-5 text-center">
                <form action="<?= routes_go_to_route('file.upload') ?>" method="POST" enctype="multipart/form-data" class="m-2">
                    <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600 mb-4">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                <span class="font-semibold"><?= lang_get('file_upload.action_description') ?></span>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <?= lang_get('file_upload.upload_types') ?>
                                <br>
                                <?= lang_get('file_upload.upload_max_size') ?>
                            </p>
                        </div>
                        <input id="dropzone-file" type="file" name="dropzone-file" class="hidden" />
                    </label>
                	<button data-modal-hide="upload-modal" type="submit" class="text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
						<?= lang_get('file_upload.send') ?>
            		</button>
                    <button data-modal-hide="upload-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        <?= lang_get('file_upload.cancel') ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>