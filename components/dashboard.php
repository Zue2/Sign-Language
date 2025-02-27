<html>

<head>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
    <link
        rel="stylesheet"
        as="style"
        onload="this.rel='stylesheet'"
        href="https://fonts.googleapis.com/css2?display=swap&amp;family=Lexend%3Awght%40400%3B500%3B700%3B900&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900" />

    <title>Galileo Design</title>
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#4A90E2',
                        'primary-dark': '#2A69A4',
                        'secondary': '#7ED321',
                        'accent': '#F5A623',
                        'success': '#10B981',
                        'warning': '#F1C40F',
                        'error': '#E74C3C',
                        'background': '#f8fafb',
                        'surface': '#FFFFFF',
                        'text': '#333333',
                        'text-light': '#7F8C8D',
                    }
                }
            }
        }
    </script>
</head>

<body>
    <div class="relative max-w-7xl mx-auto flex size-full h-50vh flex-col bg-background group/design-root overflow-x-hidden" style='font-family: Lexend, "Noto Sans", sans-serif;'>
        <div class="layout-container flex h-full grow flex-col">
            <div class="gap-1 mx-6 flex flex-1 justify-center py-0">
                <div class="layout-content-container flex sticky top-5 z-50 flex-col w-60 h-96 ">
                    <?php include 'instructor/dashboard/sidebar.php'; ?>
                </div>
                <div class="layout-content-container flex flex-col max-h-[100vh] h-70vh flex-1">

                    <?php
                    $tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';

                    switch ($tab) {
                        case 'dashboard':
                            require "components/dashboard.php";
                            break;
                        case 'profile':
                            require 'components/profile.php';
                            break;
                        case 'creator':
                            require 'components/creator-profile.php';
                            break;
                        case 'statistics':
                            require 'instructor/dashboard/stats.php';
                            break;
                        case 'students':
                            require 'instructor/dashboard/students.php';
                            break;
                        default:
                            require "instructor/dashboard/course.php";
                            break;
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>