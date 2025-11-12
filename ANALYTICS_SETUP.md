# Analytics System Setup Guide

## Overview
The Basic Analytics system has been implemented with comprehensive user interaction tracking and visual reports using Chart.js.

## Setup Instructions

### 1. Create the Analytics Database Table
Run the setup script once to create the `user_interactions` table:

```bash
php create_analytics_table.php
```

Or manually create the table using the SQL in `create_analytics_table.php`.

### 2. Files Created/Modified

#### New Files:
- `analytics.php` - Main analytics dashboard page
- `get_analytics_data.php` - API endpoint for fetching analytics data
- `track_interaction.php` - Endpoint for tracking user interactions
- `create_analytics_table.php` - Database table creation script
- `assets/js/analytics.js` - JavaScript for chart rendering

#### Modified Files:
- `dashboard.php` - Added Analytics link to sidebar
- `profile.php` - Added Analytics link to sidebar
- `settings.php` - Added Analytics link to sidebar
- `assets/js/dashboard.js` - Added interaction tracking
- `add_note.php` - Tracks note creation
- `edit_note.php` - Tracks note editing
- `delete_note.php` - Tracks note deletion
- `update_profile.php` - Tracks profile updates
- `update_password.php` - Tracks password changes
- `Login.php` - Tracks login events
- `logout.php` - Tracks logout events

## Features Implemented

### 1. User Interaction Tracking
Tracks all user interactions including:
- Page views
- Note creation, editing, deletion
- Note favoriting/unfavoriting
- Search operations
- Filter applications
- Profile updates
- Password changes
- Login/logout events
- Button clicks

### 2. Analytics Dashboard (`analytics.php`)
Displays comprehensive reports with:

#### Statistics Cards:
- Total Notes
- Total Interactions
- Average Note Length
- Notes Created (Last 7 Days)

#### Charts & Graphs:
1. **Notes Created Over Time** (Line Chart) - Last 30 days
2. **Notes by Day of Week** (Doughnut Chart) - Distribution across weekdays
3. **Notes by Hour of Day** (Bar Chart) - Activity patterns throughout the day
4. **User Interactions Distribution** (Horizontal Bar Chart) - Breakdown of interaction types
5. **Activity Timeline** (Multi-line Chart) - Combined notes and interactions over last 7 days

### 3. Chart.js Integration
- Uses Chart.js 3.9.1 (CDN)
- Responsive charts that adapt to screen size
- Dark mode support
- Smooth animations
- Interactive tooltips

## Usage

1. **Access Analytics**: Click "Analytics" in the sidebar navigation
2. **View Reports**: All charts load automatically with your data
3. **Track Interactions**: All interactions are automatically tracked in the background

## Technical Details

### Database Schema
```sql
CREATE TABLE user_interactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    interaction_type VARCHAR(50) NOT NULL,
    interaction_details TEXT,
    page_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_interaction_type (interaction_type),
    INDEX idx_created_at (created_at)
);
```

### Interaction Types Tracked
- `page_view`
- `note_created`
- `note_edited`
- `note_deleted`
- `note_favorited`
- `note_unfavorited`
- `search_performed`
- `filter_applied`
- `profile_updated`
- `password_changed`
- `login`
- `logout`
- `button_click`
- `form_submitted`

## Notes
- Analytics tracking is non-blocking and won't interrupt user experience
- Charts gracefully handle empty data with helpful messages
- All tracking respects user privacy (only tracks logged-in users)
- Data is user-specific (each user sees only their own analytics)

