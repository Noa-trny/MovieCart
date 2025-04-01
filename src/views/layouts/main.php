<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'MovieCart' ?> - <?= SITE_NAME ?></title>
    
    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9fafb;
            color: #1f2937;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto">
            <nav class="flex justify-between items-center py-4">
                <!-- Logo -->
                <a href="<?= SITE_URL ?>" class="text-2xl font-bold text-blue-600">
                    <i class="fas fa-film mr-2"></i> MovieCart
                </a>
                
                <!-- Search Form -->
                <div class="hidden md:block w-1/3">
                    <form action="<?= SITE_URL ?>/search.php" method="GET">
                        <div class="relative">
                            <input 
                                type="text" 
                                name="q" 
                                placeholder="Search movies..." 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                            <button type="submit" class="absolute right-2 top-2 text-gray-400 hover:text-blue-500">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Navigation -->
                <div class="flex items-center space-x-4">
                    <a href="<?= SITE_URL ?>" class="text-gray-600 hover:text-blue-600">Home</a>
                    
                    <div class="relative group">
                        <button class="text-gray-600 hover:text-blue-600 flex items-center">
                            Categories <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div class="absolute z-10 hidden bg-white shadow-lg rounded-lg p-2 mt-1 space-y-1 w-48 group-hover:block">
                            <a href="<?= SITE_URL ?>/categories/action.php" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 rounded">Action</a>
                            <a href="<?= SITE_URL ?>/categories/drama.php" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 rounded">Drama</a>
                            <a href="<?= SITE_URL ?>/categories/comedy.php" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 rounded">Comedy</a>
                            <a href="<?= SITE_URL ?>/categories/sci-fi.php" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 rounded">Sci-Fi</a>
                            <a href="<?= SITE_URL ?>/categories/horror.php" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 rounded">Horror</a>
                        </div>
                    </div>
                    
                    <?php if (isLoggedIn()): ?>
                        <a href="<?= SITE_URL ?>/cart.php" class="text-gray-600 hover:text-blue-600 relative">
                            <i class="fas fa-shopping-cart"></i>
                            <?php if (isset($_SESSION[CART_SESSION_KEY]) && count($_SESSION[CART_SESSION_KEY]) > 0): ?>
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                    <?= count($_SESSION[CART_SESSION_KEY]) ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <div class="relative group">
                            <button class="text-gray-600 hover:text-blue-600 flex items-center">
                                <i class="fas fa-user mr-1"></i> Account <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            <div class="absolute z-10 right-0 hidden bg-white shadow-lg rounded-lg p-2 mt-1 space-y-1 w-48 group-hover:block">
                                <a href="<?= SITE_URL ?>/profile.php" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 rounded">
                                    <i class="fas fa-user-circle mr-2"></i> Profile
                                </a>
                                <a href="<?= SITE_URL ?>/logout.php" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 rounded">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?= SITE_URL ?>/login.php" class="text-gray-600 hover:text-blue-600">Login</a>
                        <a href="<?= SITE_URL ?>/register.php" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                            Register
                        </a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>
    
    <!-- Mobile Search (visible on mobile only) -->
    <div class="block md:hidden bg-gray-100 py-2">
        <div class="container mx-auto">
            <form action="<?= SITE_URL ?>/search.php" method="GET">
                <div class="relative">
                    <input 
                        type="text" 
                        name="q" 
                        placeholder="Search movies..." 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    <button type="submit" class="absolute right-2 top-2 text-gray-400 hover:text-blue-500">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="container mx-auto mt-4">
            <div class="rounded-lg p-4 <?= $_SESSION['flash_type'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                <?= $_SESSION['flash_message'] ?>
            </div>
        </div>
        <?php 
        // Clear flash message after displaying
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        ?>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main class="container mx-auto py-8">
        <?= $content ?>
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- About Section -->
                <div>
                    <h3 class="text-xl font-semibold mb-4">About <?= SITE_NAME ?></h3>
                    <p class="text-gray-300">Your ultimate destination for movies and entertainment. Browse our collection of films from various genres and directors.</p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-xl font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="<?= SITE_URL ?>" class="text-gray-300 hover:text-white">Home</a></li>
                        <li><a href="<?= SITE_URL ?>/categories/action.php" class="text-gray-300 hover:text-white">Action Movies</a></li>
                        <li><a href="<?= SITE_URL ?>/categories/drama.php" class="text-gray-300 hover:text-white">Drama Movies</a></li>
                        <li><a href="<?= SITE_URL ?>/profile.php" class="text-gray-300 hover:text-white">My Account</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h3 class="text-xl font-semibold mb-4">Contact Us</h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-2"></i>
                            <span>65 Pl. Rihour, 59800 Lille</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-2"></i>
                            <span>+33 03 74 09 19 85</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2"></i>
                            <span>contact@moviecart.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-6 flex flex-col md:flex-row justify-between items-center">
                <p>© <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html> 