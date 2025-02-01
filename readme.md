# Event Management System

A simple Event Management System built with <strong>Raw PHP</strong> and <strong>MySQL</strong>. It allows user registration, event creation, attendance registration, and provides a JSON API for programmatic access to event details.

---

<h2>🚀 Features</h2>
<ul>
  <li>User Registration &amp; Login</li>
  <li>Role-Based Access (Admin/User)</li>
  <li>Event Creation, Update, and Deletion</li>
  <li>Event Registration for Users</li>
  <li>CSV Download of Attendee Lists (Admin Only)</li>
  <li>JSON API to Fetch Event Details</li>
</ul>

<h2>📦 Project Setup Instructions</h2>
<ol>
  <li>Clone the repository:
    <pre><code>git clone https://github.com/rjzim01/Event-Management</code></pre>
  </li>
  <li>Import the <code>event_management.sql</code> file into your MySQL server.</li>
  <li>Update the <code>db.php</code> file with your database credentials:
    <pre><code>define('DB_HOST', 'localhost');
define('DB_NAME', 'event_management');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');</code></pre>
  </li>
  <li>Start your local server (e.g., XAMPP, WAMP, or MAMP) and navigate to the project folder in your browser.</li>
  <li>Register a new user or log in with an existing account.</li>
</ol>

<h2>🔑 API Endpoint</h2>
<ul>
  <li><strong>GET /api/event.php?event_id=1</strong> - Fetch event details in JSON format.</li>
</ul>

<h2>👤 Admin Access</h2>
<ul>
  <li>Only users with the <code>authenticated</code> can create, update, or delete events.</li>
  <li>Admins can download attendee lists in CSV format for each event.</li>
</ul>

<h2>💡 Technologies Used</h2>
<ul>
  <li>PHP (Raw)</li>
  <li>MySQL</li>
  <li>HTML5 &amp; CSS3 &amp; Bootstrap</li>
  <li>JavaScript (Validation &amp; API Calls)</li>
</ul>

