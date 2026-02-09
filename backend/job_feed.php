<?php
// Include database connection
include __DIR__ . '/db_connection.php';

// Job posting handling
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Job posting form
    if (isset($_POST['post_job'])) {
        $category = $_POST['category'];
        $budget = $_POST['budget'];
        $deadline = $_POST['deadline'];
        $time = $_POST['time'];
        $description = $_POST['description'];

        // Insert new job into the database
        $stmt = $conn->prepare("INSERT INTO jobs (category, budget, deadline, time, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $category, $budget, $deadline, $time, $description);
        $stmt->execute();
        $stmt->close();
    }
    // Job application form
    elseif (isset($_POST['apply_job'])) {
        $job_id = $_POST['job_id'];
        $user_name = $_POST['user_name'];
        $user_email = $_POST['user_email'];
        $cover_letter = $_POST['cover_letter'];

        // Insert application into the database
        $stmt = $conn->prepare("INSERT INTO job_applications (job_id, user_name, user_email, cover_letter) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $job_id, $user_name, $user_email, $cover_letter);
        $stmt->execute();
        $stmt->close();
    }
}

// Job filter variables
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$budget_filter = isset($_GET['budget']) ? $_GET['budget'] : '';

// Pagination setup
$limit = 10; // Jobs per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Build the SQL query with filters
$sql = "SELECT * FROM jobs WHERE 1";

if ($category_filter) {
    $sql .= " AND category = '$category_filter'";
}
if ($budget_filter) {
    $sql .= " AND budget <= '$budget_filter'";
}

$sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Total jobs for pagination
$total_sql = "SELECT COUNT(*) as total FROM jobs WHERE 1";
if ($category_filter) {
    $total_sql .= " AND category = '$category_filter'";
}
if ($budget_filter) {
    $total_sql .= " AND budget <= '$budget_filter'";
}
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_jobs = $total_row['total'];
$total_pages = ceil($total_jobs / $limit);

$conn->close();
?>

<!-- HTML structure for displaying jobs -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Feed - CampusGigs</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
        }

        /* Navbar Styles */
        .navbar {
            background: linear-gradient(90deg, #003366, #0056b3);
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 600;
            color: white;
            font-size: 1.8rem;
        }

        .navbar-nav .nav-link {
            font-weight: 500;
            color: white !important;
            font-size: 1.1rem;
        }

        .navbar-nav .nav-link:hover {
            color: #f8f9fa !important;
            background-color: transparent;
            border-bottom: 3px solid #f8f9fa;
        }

        /* Job Feed Container */
        .job-feed-container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        .job-card {
            background: #f9f9f9;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .job-card h5 {
            color: #003366;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .job-card p {
            font-size: 1rem;
            color: #555;
            margin-bottom: 15px;
        }

        .job-card .budget {
            color: #27ae60;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .btn-apply {
            background-color: #0056b3;
            color: white;
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 6px;
        }

        /* Modal for Posting Jobs */
        .post-job-btn {
            display: inline-block;
            padding: 12px 20px;
            background-color: #28a745;
            color: white;
            border-radius: 8px;
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 30px;
            transition: background-color 0.3s ease;
        }

        .post-job-btn:hover {
            background-color: #218838;
        }

        /* Custom form input styles */
        .modal-body label {
            font-weight: 600;
            margin-top: 10px;
        }

        .modal-body input, .modal-body select, .modal-body textarea {
            width: 100%;
            padding: 8px 12px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .modal-body button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            border: none;
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
        }

        .modal-body button:hover {
            background-color: #218838;
        }

        .pagination {
            justify-content: center;
        }
    </style>
</head>
<body>

    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#">CampusGigs</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Profile</a></li>
                    <li class="nav-item"><a class="nav-link active" href="#">Jobs</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Messages</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Job Feed Section -->
    <div class="job-feed-container">
        <!-- Post Job Button -->
        <button class="post-job-btn" data-bs-toggle="modal" data-bs-target="#postJobModal">Post a Job</button>

        <!-- Job Filters -->
<div class="row mb-4">
    <div class="col-12 col-md-6">
        <form action="job_feed.php" method="GET">
            <div class="d-flex">
                <select name="category" class="form-select" style="max-width: 200px;">
                    <option value="">All Categories</option>
                    <option value="assignment" <?= ($category_filter == 'assignment') ? 'selected' : '' ?>>Assignment & Research Help 📚</option>
                    <option value="graphic_design" <?= ($category_filter == 'graphic_design') ? 'selected' : '' ?>>Graphic Design & Poster Making 🎨</option>
                    <option value="photography" <?= ($category_filter == 'photography') ? 'selected' : '' ?>>Photography & Videography 📸</option>
                    <option value="marketing" <?= ($category_filter == 'marketing') ? 'selected' : '' ?>>Marketing & Social Media 📢</option>
                    <option value="event_planning" <?= ($category_filter == 'event_planning') ? 'selected' : '' ?>>Event Planning & Decoration 🎉</option>
                    <option value="tech_support" <?= ($category_filter == 'tech_support') ? 'selected' : '' ?>>Tech & Coding Support 💻</option>
                </select>
                <select name="budget" class="form-select ms-2" style="max-width: 150px;">
                    <option value="">All Budgets</option>
                    <option value="1000" <?= ($budget_filter == '1000') ? 'selected' : '' ?>>Up to 1000</option>
                    <option value="3000" <?= ($budget_filter == '3000') ? 'selected' : '' ?>>Up to 3000</option>
                    <option value="5000" <?= ($budget_filter == '5000') ? 'selected' : '' ?>>Up to 5000</option>
                </select>
                <button type="submit" class="btn btn-outline-primary ms-3">Filter</button>
            </div>
        </form>
    </div>
</div>

        <!-- Job Cards -->
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="job-card">
                <h5><?= $row['category'] ?> - Budget: ₹<?= $row['budget'] ?></h5>
                <p><?= $row['description'] ?></p>
                <p>Deadline: <?= $row['deadline'] ?></p>
                <p class="budget">Budget: ₹<?= $row['budget'] ?></p>
                <button class="btn-apply" data-bs-toggle="modal" data-bs-target="#applyJobModal" data-job-id="<?= $row['id'] ?>">Apply Now</button>
            </div>
        <?php } ?>

        <!-- Pagination -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <a href="job_feed.php?page=<?= $i ?>" class="btn btn-light"><?= $i ?></a>
            <?php } ?>
        </div>
    </div>

    <!-- Job Application Modal -->
    <div class="modal fade" id="applyJobModal" tabindex="-1" aria-labelledby="applyJobModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="applyJobModalLabel">Apply for Job</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="job_feed.php" method="POST">
                        <input type="hidden" name="job_id" id="job_id">
                        <label for="user_name">Your Name</label>
                        <input type="text" name="user_name" required>

                        <label for="user_email">Your Email</label>
                        <input type="email" name="user_email" required>

                        <label for="cover_letter">Cover Letter</label>
                        <textarea name="cover_letter" rows="5" required></textarea>

                        <button type="submit" name="apply_job">Apply</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Post Job Modal -->
    <div class="modal fade" id="postJobModal" tabindex="-1" aria-labelledby="postJobModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="postJobModalLabel">Post a New Job</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="job_feed.php" method="POST">
                       <label for="category">Category</label>
				<select name="category" id="category" required>
    					<option value="assignment_research">📚 Assignment & Research Help</option>
    						<option value="graphic_design">🎨 Graphic Design & Poster Making</option>
   						 <option value="photography_videography">📸 Photography & Videography</option>
   						 <option value="marketing_social_media">📢 Marketing & Social Media</option>
   						 <option value="event_planning">🎉 Event Planning & Decoration</option>
   						 <option value="tech_coding_support">💻 Tech & Coding Support</option>
				</select>

                        <label for="budget">Budget</label>
                        <input type="number" name="budget" required>

                        <label for="deadline">Deadline</label>
                        <input type="date" name="deadline" required>

                        <label for="time">Time</label>
                        <input type="text" name="time" required>

                        <label for="description">Description</label>
                        <textarea name="description" rows="5" required></textarea>

                        <button type="submit" name="post_job">Post Job</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script for job application modal to auto-fill the job ID
        const applyButtons = document.querySelectorAll('.btn-apply');
        applyButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const jobId = e.target.getAttribute('data-job-id');
                document.getElementById('job_id').value = jobId;
            });
        });
    </script>
</body>
</html>
