<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$a = 34;
$isEnrolled = true;
$current_lesson_id = 0;

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "sign_language";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get course ID from URL parameter
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

// if ($course_id <= 0) {
//     die("Invalid course ID");
// }

// Fetch course information
$course_stmt = $conn->prepare("SELECT title, description FROM courses WHERE id = ?");
if (!$course_stmt) {
    die("Prepare failed: " . $conn->error);
}
// $course_stmt->bind_param("i", $course_id);
$course_stmt->bind_param("i", $a);
if (!$course_stmt->execute()) {
    die("Execute failed: " . $course_stmt->error);
}
$course_result = $course_stmt->get_result();
$course = $course_result->fetch_assoc();
$course_stmt->close();

if (!$course) {
    die("Course not found");
}

// Fetch all lessons for this course
$lessons_stmt = $conn->prepare("SELECT id, title, description, created_at FROM lesson WHERE course_id = ? ORDER BY id ASC");
if (!$lessons_stmt) {
    die("Prepare failed: " . $conn->error);
}
// $lessons_stmt->bind_param("i", $course_id);
$lessons_stmt->bind_param("i", $a);

if (!$lessons_stmt->execute()) {
    die("Execute failed: " . $lessons_stmt->error);
}
$lessons_result = $lessons_stmt->get_result();
$lessons = $lessons_result->fetch_all(MYSQLI_ASSOC);
$lessons_stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['title'] ?? 'Course View'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column -->
            <div class="space-y-8">

                <!-- Conditional Content -->
                <div id="courseContent">
                    <?php if ($isEnrolled): ?>
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="aspect-video aspect-h-9">
                            <video controls class="w-full h-full">
                                <source src="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?stream=video&id=' . $lesson_id); ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <div class="p-4">
                            <h2 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($lesson_title ?? ''); ?></h2>
                            <p class="text-sm text-gray-600 mb-4"><?php echo htmlspecialchars($lesson_description ?? ''); ?></p>
                            <div class="flex justify-between items-center">
                                <!-- <span class="text-sm text-gray-500"><?php echo $lesson_created_at ? date('M d, Y', strtotime($lesson_created_at)) : ''; ?></span> -->
                                <div class="flex space-x-2">
                                    <?php 
                                    // Find next lesson
                                    $next_lesson_id = null;
                                    $found_current = false;
                                    foreach ($lessons as $lesson) {
                                        if ($found_current) {
                                            $next_lesson_id = $lesson['id'];
                                            break;
                                        }
                                        if ($lesson['id'] == $current_lesson_id) {
                                            $found_current = true;
                                        }
                                    }
                                    
                                    if ($next_lesson_id): 
                                    ?>
                                    <a href="?course_id=<?php echo $course_id; ?>&lesson_id=<?php echo $next_lesson_id; ?>" 
                                       class="bg-gray-200 text-gray-800 px-3 py-1 rounded-md text-sm hover:bg-gray-300">
                                        <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                        </svg>
                                        Next Lesson
                                    </a>
                                    <?php endif; ?>
                                    <button class="bg-[#4A90E2] text-white px-3 py-1 rounded-md text-sm hover:bg-[#357abd]">
                                        Complete Lesson
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-[#4A90E2] rounded-full flex items-center justify-center text-white">
                                    JD
                                </div>
                                <div>
                                    <h3 class="font-medium">John Doe</h3>
                                    <p class="text-sm text-gray-500">Instructor</p>
                                </div>
                            </div>
                            <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm">Bestseller</span>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-6">
                            <div>
                                <p class="text-gray-500 text-sm">Duration:</p>
                                <p class="font-medium">12 weeks</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Lessons:</p>
                                <p class="font-medium"><?php echo count($lessons); ?> videos</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Level:</p>
                                <p class="font-medium">Intermediate</p>
                            </div>
                        </div>

                        <button class="w-full bg-[#4A90E2] text-white py-2 rounded-md hover:bg-[#357abd]">
                            Enroll Now
                        </button>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Comments Section -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold mb-4">Comments</h3>
                    <?php
                    // You could fetch comments here
                    ?>
                    <div class="border-b pb-4">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                            <div>
                                <p class="font-medium">User123</p>
                                <p class="text-gray-600 mb-2">Great course! Really informative.</p>
                                <p class="text-sm text-gray-500">2 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Right Column -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b">
                    <h1 class="text-3xl font-bold mb-2"><?php echo htmlspecialchars($course['title'] ?? ''); ?></h1>
                    <p class="text-gray-600 mb-6"><?php echo htmlspecialchars($course['description'] ?? ''); ?></p>

                    <!-- Progress Bar -->
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold">Course Videos</h2>
                        <div class="flex items-center">
                            <span class="text-green-500 mr-2">75% complete</span>
                            <div class="w-32 h-2 bg-gray-200 rounded-full">
                                <div class="w-3/4 h-full bg-green-500 rounded-full"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Video Lessons -->
                    <div class="space-y-4">
                        <?php foreach ($lessons as $lesson): 
                            $isCurrentLesson = ($lesson['id'] == $current_lesson_id);
                            $lessonStatus = $isCurrentLesson ? 'In Progress' : 'Not started';
                            $statusClass = $isCurrentLesson ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800';
                            
                            // You could fetch completion status from progress table
                        ?>
                        <div class="flex items-start space-x-4 p-4 <?php echo $isCurrentLesson ? 'bg-blue-50' : 'bg-gray-50'; ?> rounded-lg">
                            <div class="w-32 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium"><?php echo htmlspecialchars($lesson['title'] ?? ''); ?></h3>
                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($lesson['description'] ?? ''); ?></p>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-sm text-gray-500">
                                        <?php if ($isCurrentLesson): ?>
                                        <a href="?course_id=<?php echo $course_id; ?>&lesson_id=<?php echo $lesson['id']; ?>" class="text-blue-500 hover:underline">
                                            Currently viewing
                                        </a>
                                        <?php else: ?>
                                        <a href="?course_id=<?php echo $course_id; ?>&lesson_id=<?php echo $lesson['id']; ?>" class="hover:underline">
                                            View lesson
                                        </a>
                                        <?php endif; ?>
                                    </span>
                                    <span class="<?php echo $statusClass; ?> text-xs px-2 py-1 rounded"><?php echo $lessonStatus; ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Button -->
    <div class="fixed bottom-8 right-8">
        <button class="bg-[#4A90E2] text-white p-4 rounded-full shadow-lg hover:bg-[#357abd]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
            </svg>
        </button>
    </div>
</body>
</html>