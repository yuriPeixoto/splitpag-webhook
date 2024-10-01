# Splitpag Webhook Integration

## Overview

This project implements a webhook integration with the Splitpag API, providing a robust interface for managing clients, charges, payments, and audits. It's built using PHP with the Slim framework, offering a RESTful API structure.

## Table of Contents

1. [Features](#features)
2. [Requirements](#requirements)
3. [Installation](#installation)
4. [Configuration](#configuration)
5. [Usage](#usage)
6. [API Endpoints](#api-endpoints)
7. [Authentication](#authentication)
8. [Testing](#testing)
9. [Contributing](#contributing)
10. [License](#license)

## Features

- Secure authentication with Splitpag API
- Client management
- Charge creation and management
- Payment processing and status checks
- Audit logging
- Comprehensive error handling and logging

## Requirements

- PHP 8.2 or higher
- Composer
- MySQL (or your preferred database)
- Splitpag API credentials

## Installation

1. Clone the repository:
   ```
   git clone https://github.com/yourusername/splitpag-webhook.git
   ```

2. Navigate to the project directory:
   ```
   cd splitpag-webhook
   ```

3. Install dependencies:
   ```
   composer install
   ```

4. Copy the `.env.example` file to `.env` and update with your configuration:
   ```
   cp .env.example .env
   ```

## Configuration

Update the `.env` file with your Splitpag API credentials and other configuration details:

```
SPLITPAG_API_URL=https://api.splitpag.com
SPLITPAG_EMAIL=your_email@example.com
SPLITPAG_PASSWORD=your_password
```

## Usage

To start the local development server, run:
```
php -S localhost:8080 -t public/
```

The API will be available at `http://localhost:8000`.

## API Endpoints

- **Authentication**
  - `POST /login`: Authenticate and receive a token

- **Clients**
  - `GET /client`: List clients
  - `POST /client`: Create a new client

- **Charges**
  - `GET /charge`: List charges
  - `GET /charge/create`: Get charge creation data
  - `POST /charge/create`: Create a new charge
  - `GET /charge/status/{hash_charge_id}`: Get charge status

- **Payments**
  - `GET /payment`: List payments
  - `GET /payment/checkStatusPayment`: Check payment status

- **Audits**
  - `GET /audit`: List audit logs

For detailed information on request/response formats, please refer to the [API Documentation](https://doc.splitpag.com.br/docs/#/).

## Authentication

All endpoints, except `/login`, require authentication. Include the token received from the login endpoint in the `Authorization` header of subsequent requests:

```
Authorization: Bearer YOUR_TOKEN
```

## Testing

To run the test suite:
```
php vendor/bin/phpunit
```

For more detailed testing information, see [TESTING.md](TESTING.md).

## TODO List

The following items are planned for implementation:

### Exception Handling
- [ ] Create personalized exceptions specific to the project

### Model Development
- [ ] Create models for key entities (e.g., Transaction, Customer, Order) based on the Splitpag API structure

### Error Handling
- [ ] Implement a consistent error handling strategy across all services and controllers
- [ ] Create custom exceptions for different error types (e.g., TransactionException, CustomerNotFoundException)

### Logging
- [ ] Implement comprehensive logging throughout the application, especially for API interactions and error scenarios

### Configuration Management
- [ ] Use environment variables or a configuration file to manage Splitpag API credentials and other sensitive information

### Input Validation
- [ ] Implement robust input validation for all API requests to ensure data integrity and security

### Rate Limiting
- [ ] Implement rate limiting to prevent abuse of the bridge service

### Caching
- [ ] Implement caching where appropriate to improve performance, especially for frequently accessed, relatively static data

### Testing
- [ ] Expand unit tests to cover all services and controllers
- [ ] Implement integration tests to ensure correct functionality with the Splitpag API

### Documentation
- [ ] Create comprehensive documentation for the bridge service, including setup instructions, API endpoints, and usage examples

### Security
- [ ] Ensure all sensitive data (API keys, passwords) are properly encrypted and securely stored
- [ ] Implement HTTPS for all communications

### Webhook Security
- [ ] Implement signature verification for incoming webhooks to ensure they're genuinely from Splitpag

### Error Responses
- [ ] Standardize error responses across the API to make it easier for consumers of the bridge to handle errors

### Dependency Management
- [ ] Use a dependency injection container to manage service dependencies and improve testability

### API Versioning
- [ ] Implement API versioning to allow for future updates without breaking existing integrations

### Performance Monitoring
- [ ] Implement performance monitoring to identify and address bottlenecks in the bridge service

## Contributing

Contributions are welcome! Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.