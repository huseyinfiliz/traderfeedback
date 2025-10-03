# Trader Feedback for Flarum

![License](https://img.shields.io/badge/license-MIT-blue.svg) 
[![Latest Stable Version](https://img.shields.io/packagist/v/huseyinfiliz/traderfeedback.svg)](https://packagist.org/packages/huseyinfiliz/traderfeedback) 
[![Total Downloads](https://img.shields.io/packagist/dt/huseyinfiliz/traderfeedback.svg)](https://packagist.org/packages/huseyinfiliz/traderfeedback)

A comprehensive trader feedback system for [Flarum](https://flarum.org) forums. This extension allows users to leave feedback for each other after trading, buying, or selling items, creating a trust-based reputation system for your community.

## Features

### Core Functionality
- **Feedback Types**: Positive, Neutral, and Negative feedback options
- **User Roles**: Specify your role as Buyer, Seller, or Trader
- **Discussion Linking**: Optionally link feedback to specific discussions
- **Feedback Management**: View and manage feedback in user profiles
- **Trader Score**: Automatic calculation of reputation score (0-100%)
- **Profile Integration**: Statistics visible on user profiles and cards

### Moderation & Safety
- **Admin Moderation**: Optional approval workflow for all feedback
- **Reporting System**: Users can report inappropriate feedback
- **Soft Delete**: Rejected feedback is preserved for audit purposes
- **Permission System**: Granular control over who can give/moderate feedback
- **Spam Protection**: One feedback per discussion rule (configurable)
- **Self-Feedback Prevention**: Users cannot give feedback to themselves

### Notifications
- **New Feedback**: Notify users when they receive feedback
- **Approval/Rejection**: Notify feedback authors about moderation decisions
- **User Preferences**: Users can enable/disable notification types

### Security
- **XSS Protection**: HTML tags stripped from user input
- **SQL Injection Protection**: Parameterized queries and type casting
- **Input Validation**: Comprehensive Laravel validation rules
- **Permission Checks**: All actions require proper authorization

## Installation

### Requirements
- Flarum 1.2.0 or higher
- PHP 7.4 or higher

### Via Composer

```bash
composer require huseyinfiliz/traderfeedback
```

### Enable the Extension

```bash
php flarum migrate
php flarum cache:clear
```

Then enable the extension in your Flarum admin panel.

## Configuration

### Admin Settings

Navigate to **Admin Panel → Extensions → Trader Feedback** to configure:

#### General Settings
- **Allow Negative Feedback**: Enable/disable negative feedback
- **Require Approval**: Require admin approval for all feedback
- **Show Badge in Posts**: Display trader score badge in forum posts

#### Discussion Settings
- **Require Discussion Link**: Force users to link feedback to a discussion
- **One Per Discussion**: Limit one feedback per user pair per discussion

#### Comment Settings
- **Minimum Length**: Minimum characters for feedback comments (default: 10)
- **Maximum Length**: Maximum characters for feedback comments (default: 1000)

### Permissions

Set permissions for the following actions in **Admin Panel → Permissions**:

- **Give Feedback**: Allow users to submit feedback (default: all users)
- **Report Feedback**: Allow users to report inappropriate feedback (default: all users)
- **Moderate Feedback**: Approve/reject feedback and reports (default: moderators and admins)
- **Delete Feedback**: Permanently remove feedback (default: admins only)

## Usage

### Giving Feedback

1. Navigate to a user's profile
2. Click the user controls dropdown (⋮)
3. Select **"Give Feedback"**
4. Fill out the form:
   - Choose feedback type (Positive/Neutral/Negative)
   - Select your role (Buyer/Seller/Trader)
   - Optionally link a discussion
   - Write a comment describing the trade experience
5. Submit the feedback

### Viewing Feedback

- **User Profile**: Click the "Trader Feedback" tab on any user's profile
- **User Card**: Hover over a username to see quick statistics
- **Post Badge**: Trader scores appear next to usernames in posts (if enabled)

### Reporting Feedback

If you see inappropriate feedback:
1. Click the **"Report"** button on the feedback item
2. Provide a reason for the report
3. Submit the report

Moderators will review and take appropriate action.

### For Moderators

#### Pending Approvals
Navigate to **Admin Panel → Trader Feedback → Pending Approvals** to:
- View all pending feedback submissions
- Approve legitimate feedback
- Reject spam or inappropriate feedback

#### Reports
Navigate to **Admin Panel → Trader Feedback → Reports** to:
- Review reported feedback
- Approve reports and remove feedback
- Reject reports (keep feedback)
- Dismiss reports without action

## API Endpoints

### Public Endpoints

#### Get User Feedbacks
```
GET /api/trader/feedback?filter[user]={userId}&filter[type]={type}
```

#### Get User Statistics
```
GET /api/trader/stats/{userId}
```

### Authenticated Endpoints

#### Submit Feedback
```
POST /api/trader/feedback
```
Body:
```json
{
  "data": {
    "type": "feedbacks",
    "attributes": {
      "to_user_id": 123,
      "type": "positive",
      "role": "buyer",
      "comment": "Great trader, fast shipping!",
      "discussion_id": 456
    }
  }
}
```

#### Report Feedback
```
POST /api/trader/feedback/{id}/report
```

### Moderator Endpoints

#### Approve Feedback
```
POST /api/trader/feedback/{id}/approve
```

#### Reject Feedback
```
POST /api/trader/feedback/{id}/reject
```

#### List Pending Feedbacks
```
GET /api/trader/feedback/pending
```

#### List Reports
```
GET /api/trader/reports
```

## Database Schema

### Tables

#### `tfb_feedbacks`
- Core feedback storage with soft delete support
- Indexed for efficient queries

#### `tfb_stats`
- Cached user statistics (positive/neutral/negative counts, score)
- Automatically updated when feedback is created/modified

#### `tfb_reports`
- Feedback reports with moderation status
- Tracks reporter and resolver information

## Performance

### Caching
- User statistics are cached for 1 hour
- Cache automatically invalidated when feedback changes
- Reduces database load for frequently viewed profiles

### Database Optimization
- Composite indexes for common queries
- Eager loading for relationships
- Soft delete queries optimized with indexes

## Troubleshooting

### Notifications Not Appearing
1. Clear cache: `php flarum cache:clear`
2. Rebuild assets: `php flarum assets:publish`
3. Check notification preferences in user settings

### Statistics Not Updating
1. Manually trigger recalculation via admin panel
2. Check if feedback is approved (only approved feedback counts)
3. Verify soft-deleted feedback is excluded

### Permission Issues
1. Review permission settings in admin panel
2. Check user group assignments
3. Ensure users have required permissions for their actions

## Translation

The extension includes English translations. To add more languages:

1. Create a locale file: `locale/{lang_code}.yml`
2. Copy structure from `locale/en.yml`
3. Translate all strings
4. Submit a pull request to contribute translations

## Development

### Building Assets

```bash
npm install
npm run dev  # Development with watch
npm run build  # Production build
```

### Running Tests

```bash
composer test
```

### Code Style

```bash
composer analyse:phpstan
```

## Support

- **Issues**: [GitHub Issues](https://github.com/huseyinfiliz/flarum-trader-feedback/issues)
- **Discussions**: [Flarum Community](https://discuss.flarum.org)

## Contributing

Contributions are welcome! Please:
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

This extension is licensed under the [MIT License](LICENSE).

## Credits

Developed by [Huseyin Filiz](https://github.com/huseyinfiliz)

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history and updates.