<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Web Linux Interface</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-indigo-600 py-4">
            <h2 class="text-center text-white text-2xl font-bold">Web Linux Interface</h2>
        </div>

        <div class="px-8 py-6">
            <h3 class="text-xl font-semibold text-gray-700 mb-6">Login to your account</h3>

            <form action="<?= base_url('login') ?>" method="post">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        Email Address
                    </label>
                    <input
                        class="appearance-none border border-gray-300 rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:border-indigo-500 focus:shadow-outline"
                        id="email"
                        type="email"
                        name="email"
                        placeholder="your@email.com"
                        required>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                        Password
                    </label>
                    <input
                        class="appearance-none border border-gray-300 rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:border-indigo-500 focus:shadow-outline"
                        id="password"
                        type="password"
                        name="password"
                        placeholder="••••••••"
                        required>
                </div>

                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <input id="remember" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <button
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out"
                        type="submit">
                        Sign In
                    </button>
                </div>
            </form>

            <div class="text-center text-sm text-gray-500">
                <p>Secure login to manage your Linux servers</p>
            </div>
        </div>

        <div class="text-center my-4 text-sm text-gray-500">
            <p>© 2025 Web Linux Interface. All rights reserved.</p>
        </div>
    </div>


</body>

</html>