# CuanTrack

CuanTrack is a comprehensive personal finance management system built with native PHP. It helps users track their income, expenses, and overall financial health through an intuitive interface.

## Features

- **Transaction Management**: Track income and expenses with detailed categorization
- **Wallet Management**: Manage multiple wallets or accounts with different balances
- **Budget Planning**: Create and manage budgets for different expense categories
- **Savings Goals**: Set financial goals and track progress towards them
- **Subscription Tracking**: Monitor recurring payments and subscriptions
- **Financial Reports**: View spending patterns and financial health metrics
- **Responsive Design**: Accessible on both desktop and mobile devices

## Technology Stack

- **Backend**: Native PHP (No frameworks)
- **Frontend**: HTML, CSS, JavaScript, Bootstrap 5
- **Database**: MySQL
- **Charts**: Chart.js for financial visualizations

## Project Structure

```
CuanTrack/
├── config/            # Configuration files (database connection)
├── controllers/       # Application controllers
│   └── Auth/          # Authentication controllers
├── core/              # Core functionality and middleware
├── models/            # Data models
├── public/            # Public assets
│   └── css/           # Stylesheets
├── routes/            # Routing configuration
├── views/             # View templates
│   ├── budgets/       # Budget management views
│   ├── categories/    # Category management views
│   ├── components/    # Reusable UI components
│   ├── goals/         # Savings goals views
│   ├── layouts/       # Layout templates (header, footer, sidebar)
│   ├── subscriptions/ # Subscription management views
│   ├── transactions/  # Transaction views
│   ├── users/         # User profile views
│   └── wallets/       # Wallet management views
├── .htaccess          # Apache configuration for routing
├── index.php          # Application entry point
└── README.md          # Project documentation
```

## Installation

1. Clone the repository to your web server directory
2. Create a MySQL database named 'cuantrack'
3. Import the database schema from 'cuantrack.sql'
4. Configure database connection in 'config/database.php'
5. Ensure your web server has mod_rewrite enabled
6. Access the application through your web browser

## Setup Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite enabled

## Features Overview

### Wallet Management
Create and manage multiple wallets to organize your finances by accounts, purposes, or currencies.

### Transaction Tracking
Record all your financial transactions with details like amount, category, date, and description.

### Budget Planning
Set monthly budgets for different expense categories and track your spending against them.

### Savings Goals
Create financial goals with target amounts and dates, then track your progress towards achieving them.

### Subscription Management
Keep track of recurring payments with billing cycles, payment dates, and automatic reminders.

### Visual Reports
View graphical representations of your income, expenses, and budget performance over time.

## Usage

1. Register a new account or login
2. Create wallets to organize your finances
3. Set up income and expense categories
4. Start recording your transactions
5. Create budgets to manage your spending
6. Set savings goals to work towards
7. Add subscriptions to track recurring payments
8. View reports to analyze your financial patterns

## License

This project is licensed under the MIT License.
