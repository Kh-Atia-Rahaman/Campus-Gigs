<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Job</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h1>Post a Job</h1>
        <form action="job_feed.php" method="POST">
            <label for="category">Category</label>
            <select name="category" id="category" required>
                <option value="assignment_research">📚 Assignment & Research Help</option>
                <option value="graphic_design">🎨 Graphic Design & Poster Making</option>
                <option value="photography_videography">📸 Photography & Videography</option>
                <option value="marketing_social_media">📢 Marketing & Social Media</option>
                <option value="event_planning">🎉 Event Planning & Decoration</option>
                <option value="tech_coding">💻 Tech & Coding Support</option>
            </select>

            <label for="budget">Budget</label>
            <input type="number" name="budget" id="budget" required>

            <label for="deadline">Deadline</label>
            <input type="date" name="deadline" id="deadline" required>

            <label for="time">Time</label>
            <input type="time" name="time" id="time" required>

            <label for="description">Description</label>
            <textarea name="description" id="description" required></textarea>

            <button type="submit">Post Job</button>
        </form>
    </div>

</body>
</html>
