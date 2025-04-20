# PHP Forum System

A simple and secure forum system built with PHP, MySQL, and Bootstrap. This forum system includes user authentication, topic management, and an admin panel.

## Features

- User registration and authentication
- Topic creation and management
- Reply system
- Category management
- Admin panel
- Responsive design using Bootstrap 5
- Security features (SQL injection prevention, XSS protection)

## Installation

1. Configure your web server (Apache/Nginx) with PHP and MySQL
2. Create a database and import the `database.sql` file
3. Update database credentials in `config.php`
4. Access the forum through your web browser

## Default Admin Account

- Username: admin
- Password: admin
- Email: admin@example.com

## File Structure

### Configuration Files
- `config.php` - Database connection configuration
- `database.sql` - Database structure and initial data

### Core Files
- `index.php` - Homepage showing all categories and recent topics
- `login.php` - User login functionality
- `register.php` - New user registration
- `logout.php` - User logout functionality

### Topic Management
- `category.php` - Shows all topics in a category
- `topic.php` - Shows a single topic and its replies
- `create_topic.php` - Create new topics
- `edit_topic.php` - Edit existing topics
- `delete_topic.php` - Delete topics

### Reply Management
- `edit_reply.php` - Edit existing replies
- `delete_reply.php` - Delete replies

### Admin Features
- `admin.php` - Admin dashboard with statistics and management tools
- `edit_category.php` - Edit category details
- `delete_category.php` - Delete categories and their contents

### Include Files
- `includes/header.php` - Common header with navigation
- `includes/footer.php` - Common footer with scripts

### Assets
- `css/style.css` - Custom styles for the forum

## Database Structure

### Users Table
- Stores user information
- Handles admin privileges
- Manages user authentication

### Categories Table
- Stores forum categories
- Manages category organization

### Topics Table
- Stores forum topics/posts
- Links to categories and users
- Manages topic content

### Replies Table
- Stores replies to topics
- Links to topics and users
- Manages reply content

## Security Features

1. Password Hashing
   - Uses PHP's password_hash() for secure password storage
   - Implements secure password verification

2. SQL Injection Prevention
   - Uses prepared statements
   - Implements parameter binding

3. XSS Protection
   - HTML escaping using htmlspecialchars()
   - Input validation and sanitization

4. Access Control
   - Session-based authentication
   - Role-based permissions
   - Secure admin privileges

## User Roles

### Regular Users Can:
- Create new topics
- Post replies
- Edit their own topics and replies
- Delete their own content

### Administrators Can:
- Manage all topics and replies
- Create/edit/delete categories
- Access admin dashboard
- View forum statistics
- Manage all user content

## File Descriptions

### Main Pages
- `index.php`: The main landing page displaying all categories and recent topics in each category
- `category.php`: Shows all topics within a specific category
- `topic.php`: Displays a single topic and all its replies

### Authentication
- `login.php`: Handles user login with username/password
- `register.php`: New user registration form and processing
- `logout.php`: Handles user logout and session destruction

### Content Management
- `create_topic.php`: Form and processing for creating new topics
- `edit_topic.php`: Allows users to edit their topics (or any topic for admins)
- `delete_topic.php`: Handles topic deletion with permission checking
- `edit_reply.php`: Allows users to edit their replies
- `delete_reply.php`: Handles reply deletion with permission checking

### Admin Features
- `admin.php`: Admin dashboard with statistics and management tools
- `edit_category.php`: Category management interface
- `delete_category.php`: Handles category deletion and cleanup

### Support Files
- `config.php`: Database connection and configuration settings
- `includes/header.php`: Common header with navigation and user status
- `includes/footer.php`: Common footer with JavaScript includes
- `css/style.css`: Custom CSS styles for forum appearance

## Usage Tips

1. Always run the database.sql script first to set up the database structure
2. Make sure to set proper permissions on the server
3. Keep the admin credentials secure
4. Regularly backup the database
5. Monitor the forum for inappropriate content

## Contributing

Feel free to fork this project and submit improvements through pull requests.

## License

This project is open-source and available for personal and commercial use. 