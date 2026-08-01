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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        /* Navbar Styles */
        .navbar {
            background: rgba(15, 23, 42, 0.95) !important;
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 18px 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-weight: 800;
            color: white !important;
            font-size: 1.6rem;
            letter-spacing: -0.5px;
        }

        .navbar-nav .nav-link {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.8) !important;
            font-size: 0.95rem;
            padding: 8px 16px !important;
            transition: all 0.2s ease;
        }
        .navbar-nav .nav-link:hover {
            color: #ffffff !important;
            transform: translateY(-1px);
        }

        /* Job Feed Container */
        .job-feed-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 45px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }

        .job-card {
            background: #ffffff;
            padding: 30px;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.02);
            margin-bottom: 25px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .job-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05), 0 10px 10px -5px rgba(0,0,0,0.03);
            border-color: #6366f1;
        }

        .job-card h5 {
            color: #0f172a;
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .job-card p {
            font-size: 0.95rem;
            color: #475569;
            margin-bottom: 15px;
        }

        .job-card .budget {
            color: #10b981;
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 15px;
        }

        .btn-apply {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white;
            border: none;
            padding: 10px 24px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px rgba(99, 102, 241, 0.2);
        }
        .btn-apply:hover {
            background: linear-gradient(135deg, #4f46e5, #4338ca);
            color: white;
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
            transform: translateY(-1px);
        }

        /* Modal for Posting Jobs */
        .post-job-btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 30px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
        }
        .post-job-btn:hover {
            background: linear-gradient(135deg, #059669, #047857);
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
            transform: translateY(-1px);
        }

        /* Custom form input styles */
        .modal-body label {
            font-weight: 600;
            margin-top: 10px;
            color: #475569;
            font-size: 0.9rem;
        }

        .modal-body input, .modal-body select, .modal-body textarea {
            width: 100%;
            padding: 10px 14px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .modal-body input:focus, .modal-body select:focus, .modal-body textarea:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            outline: none;
        }

        .modal-body button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            border: none;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(99, 102, 241, 0.2);
            transition: all 0.2s ease;
        }

        .modal-body button:hover {
            background: linear-gradient(135deg, #4f46e5, #4338ca);
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
        }

        .pagination {
            justify-content: center;
            gap: 8px;
            margin-top: 30px;
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
                    <li class="nav-item"><a class="nav-link" href="../frontend/pages/Home Page.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="controllers/profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link active" href="job_feed.php">Jobs</a></li>
                    <li class="nav-item"><a class="nav-link" href="controllers/logout.php">Logout</a></li>
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
