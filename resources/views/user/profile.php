<section id="profile" class="bg-gray-50 dark:bg-gray-900">
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
		<div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
			<div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <h2 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                    <?= lang_get('profile.title') ?> : <?= ucfirst($data['user']['username']) ?>
                </h2>
                <form 
                    action="<?= routes_go_to_route('profile.edit') ?>"
                    method="post"
                    class="space-y-4 md:space-y-6"
                >
                    <div>
                        <label
                            for="username"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Nom d'utilisateur
                        </label>
                        <input
                            type="text"
                            name="username"
                            class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="<?= $data['user']['username'] ?>"
                            disabled
                        >
                    </div>
                    <div>
                        <label
                            for="email"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Adresse mail
                        </label>
                        <input
                            type="email"
                            name="email"
                            class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="<?= $data['user']['email'] ?>"
                            disabled
                        >
                    </div>
                    <div class="flex items-center justify-center">
                        <a
                            href="#"
                            onclick="enableUserEditForm()"
                            id="edit"
                            class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                        >
                            Éditer
                        </a>
                        <a
                            href="#"
                            onclick="disableUserEditForm()"
                            id="cancel"
                            class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                            hidden
                        >
                            Annuler
                        </a>
                        <button
                            type="submit"
                            id="submit"
                            class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                            hidden
                        >
                            Envoyer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>