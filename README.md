# WDM Custom Header - Auto-Update Setup Guide

## GitHub Auto-Update System

Your WDM Custom Header plugin now includes automatic updates from GitHub repositories, providing a WordPress-style "Update Now" notification system.

### Features

- **Automatic Update Notifications**: Shows "There is a new version available" notices in WordPress admin
- **One-Click Updates**: Click "Update Now" to automatically download and install from GitHub
- **Version Management**: Automatic comparison with GitHub releases
- **Private Repository Support**: Optional GitHub token for private repos
- **Release Notes**: Displays changelog from GitHub releases

### Setup Instructions

1. **Create GitHub Repository**
   - Upload your plugin files to a GitHub repository
   - Create releases using semantic versioning (v1.0.0, v1.1.0, etc.)

2. **Configure Plugin Settings**
   - Go to WordPress Admin → Settings → WDM Header → Auto Updates tab
   - Enable "Automatically check for plugin updates from GitHub"
   - Enter your GitHub username and repository name
   - Optional: Add GitHub Personal Access Token for private repos

3. **Example Configuration**
   ```
   GitHub Username: yourusername
   Repository Name: wdm-custom-header
   GitHub Token: ghp_xxxxxxxxxxxxxxxxxxxx (optional)
   ```

### How It Works

1. Plugin checks GitHub API for new releases every 12 hours
2. Compares current version with latest GitHub release
3. Shows WordPress-style update notification when newer version available
4. Downloads and installs directly from GitHub release ZIP file
5. Caches version information to reduce API calls

### GitHub Release Process

1. **Tag Your Release**
   ```bash
   git tag v1.1.0
   git push origin v1.1.0
   ```

2. **Create GitHub Release**
   - Go to your repository → Releases → Create a new release
   - Select the tag you created
   - Add release notes describing changes
   - Publish the release

3. **Automatic Detection**
   - Plugin will detect the new version within 12 hours
   - Users will see "Update Now" notification
   - One-click update process installs new version

### Private Repository Setup

For private repositories, create a GitHub Personal Access Token:

1. Go to GitHub → Settings → Developer settings → Personal access tokens
2. Generate new token with "repo" permissions
3. Copy the token (starts with `ghp_`)
4. Enter token in plugin settings

### Troubleshooting

- **No Updates Detected**: Check GitHub username and repository name are correct
- **Private Repo Issues**: Verify GitHub token has "repo" permissions
- **Rate Limiting**: Add GitHub token to increase API rate limits
- **Manual Check**: Use "Check for Updates Now" button in plugin settings

### Files Created

- `includes/class-wdm-updater.php` - Core update functionality
- WordPress admin integration for update notifications
- Settings page with GitHub configuration options
- Automatic version checking and caching system

The system is now fully integrated and ready to use once you configure your GitHub repository details in the plugin settings.