<?= $this->extend('layouts/auth_layout') ?>

<?= $this->section('content') ?>
<div class="max-w-md w-full bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="bg-indigo-600 py-4">
        <h2 class="text-center text-white text-2xl font-bold">Web Linux Interface</h2>
    </div>

    <div class="px-8 py-6">
        <h3 class="text-xl font-semibold text-gray-700 mb-6">Login to your account</h3>

        <form action="<?= base_url('auth/login') ?>" method="post">
            <div class="mb-4">
                <label class="main-label" for="email">
                    Email Address
                </label>
                <input
                    class="main-input"
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
                    class="main-input"
                    id="password"
                    type="password"
                    name="password"
                    placeholder="••••••••"
                    required>
            </div>

            <div class="flex justify-center mb-4">
                <a href="<?= base_url('forgot-password') ?>" class="text-sm text-indigo-600 hover:text-indigo-800">Forgot password?</a>
            </div>

            <div class="mb-6">
                <button
                    class="main-button"
                    type="submit">
                    Sign In
                </button>
            </div>
        </form>

        <div class="text-center text-sm text-gray-500">
            <p>Secure login to manage your Linux servers</p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>