# Web Linux Interface

A web-based interface for managing and interacting with Ubuntu Linux systems remotely. Built with CodeIgniter PHP framework.

## Overview

Web Linux Interface provides a user-friendly web interface that allows users to manage their Ubuntu Linux systems through a browser. This eliminates the need for direct terminal access while still providing powerful system management capabilities. The application is built using CodeIgniter PHP framework with Composer for dependency management and Tailwind CSS for styling.

## Features

- Remote system management through a web browser
- File management (browse, upload, download, edit)
- Process monitoring and management
- User and permission management
- System resource monitoring
- Terminal emulation
- Task scheduling
- Software installation and updates

## Installation

### Prerequisites

- Ubuntu Linux (18.04 LTS or newer recommended)
- PHP 8.3.7
- Composer (PHP dependency manager)
- CodeIgniter 4.x
- npm (For Tailwind CSS only)
- Modern web browser

### Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/Elie309/WebLinuxInterface.git
   cd WebLinuxInterface
   ```

2. Install PHP dependencies with Composer:
   ```bash
   composer install
   ```

3. Install and build Tailwind CSS:
   ```bash
   npm install
   npm run build-css
   ```

4. Configure CodeIgniter:
   ```bash
   cp env .env
   # Edit .env with your database and other configurations
   ```

5. Start the server:
   ```bash
   php spark serve
   # or use your preferred web server (Apache, Nginx)
   ```

6. Access the interface at `http://localhost:8080` (default CodeIgniter port)

## Usage

1. Log in with your system credentials
2. Navigate through the dashboard to access different management features
3. Use the built-in terminal for direct command execution
4. Monitor system resources and processes in real-time

## Security Considerations

- It's recommended to use HTTPS for secure connections
- Set up proper authentication and access controls
- Limit access to trusted networks
- Regularly update the application and dependencies

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

**OPEN LICENSE**

This project is available for free use with the following conditions:
- Do not sell this software or any derivative works
- Can be used for personal use
- Modification and redistribution is allowed as long as original attribution is maintained

## Author

- **Elie309 (Elie Saade)**

## Support

For issues, feature requests, or questions, please open an issue on the GitHub repository.
