<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard - Web Linux Interface' ?></title>
    <link rel="stylesheet" href="<?= base_url('styles/output.css') ?>">
</head>

<body class="bg-gray-100 h-screen flex overflow-hidden">
    <!-- Sidebar -->
    <div id="sidebar" class="absolute -translate-x-full w-full h-full z-50
        md:relative md:translate-x-0 min-w-64
        md:w-2/12 md:flex md:flex-shrink-0 
        transition-transform duration-300 ease-in-out transform">
        <div class="flex flex-col w-full h-full">
            <!-- Sidebar component -->
            <div class="flex flex-col h-0 flex-1 bg-indigo-800">
                <div class="flex items-center justify-between p-4 md:pt-5 md:pb-4">
                    <div class="flex items-center flex-shrink-0">
                        <span class="text-white text-xl font-bold">Web Linux Interface</span>
                    </div>
                    <!-- Close sidebar button - visible only on mobile -->
                    <button id="close-sidebar-btn" class="md:hidden -mr-2 h-10 w-10 inline-flex items-center justify-center rounded-md text-indigo-300 hover:text-white focus:outline-none" onclick="toggleSidebar()">
                        <span class="sr-only">Close sidebar</span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="flex-1 flex flex-col pt-0 overflow-y-auto">
                    <nav class="mt-5 flex-1 px-2 space-y-1">
                        <?php foreach ($navItems ?? [] as $item): ?>
                            <a href="<?= $item['url'] ?>" class="<?= $item['active'] ? 'bg-indigo-900 text-white' : 'text-indigo-100 hover:bg-indigo-700' ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <?= $item['icon'] ?>
                                <?= $item['title'] ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                </div>
                <div class="flex-shrink-0 flex border-t border-indigo-700 p-4">
                    <a href="#" class="flex-shrink-0 group block">
                        <div class="flex items-center">
                            <div>
                                <svg class="h-10 w-10 text-indigo-300" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-white">Admin User</p>
                                <p class="text-xs font-medium text-indigo-200">Logout</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile sidebar backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-20 hidden" onclick="toggleSidebar()"></div>

    <!-- Main content -->
    <div class="flex flex-col flex-1 overflow-hidden w-10/12">
        <div class="md:hidden pl-1 pt-1 sm:pl-3 sm:pt-3">
            <button id="open-sidebar-btn" type="button" class="-ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" onclick="toggleSidebar()">
                <span class="sr-only">Open sidebar</span>
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <script>
        // Sidebar toggle functionality
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            
            sidebar.classList.toggle('-translate-x-full');
            
            // Toggle backdrop visibility
            if (sidebar.classList.contains('-translate-x-full')) {
                backdrop.classList.add('hidden');
            } else {
                backdrop.classList.remove('hidden');
            }
        }
    </script>
</body>

</html>