<?php

return [
    'GET' => [
        '/' => 'DashboardController@index',
        '/dashboard' => 'DashboardController@index',
        '/login' => 'Auth\\AuthController@loginPage',
        '/register' => 'Auth\\AuthController@registerPage',
        '/wallets' => 'WalletController@index',
        '/wallets/create' => 'WalletController@create',
        '/wallets/view/{id}' => 'WalletController@view', // Dynamic route for wallet view
        '/logout' => 'Auth\\AuthController@logout',
        '/transactions' => 'TransactionController@index',
        '/transactions/create' => 'TransactionController@create',
        '/api/categories' => 'TransactionController@getCategoriesByType', // API-like endpoint for categories
        '/api/transactions/get' => 'TransactionController@getTransaction', // Get transaction by ID
        '/budget' => 'BudgetController@index', // Budget management page
        '/goals' => 'GoalsController@index', // Goals management page 
        '/goals/create' => 'GoalsController@create', // Create goal page
        '/goals/view/{id}' => 'GoalsController@view', // View specific goal
        '/goals/edit/{id}' => 'GoalsController@edit', // Edit goal page
        '/categories' => 'CategoryController@index', // Categories management page
        '/subscription' => 'SubscriptionController@index', // Subscription management page
        '/subscription/create' => 'SubscriptionController@create', // Create subscription page
        '/subscription/edit/{id}' => 'SubscriptionController@edit', // Edit subscription page
        '/users/profile' => 'UserController@profile', // User profile page
        '/users/edit' => 'UserController@edit', // Edit user profile page
    ],
    'POST' => [
        '/login' => 'Auth\\AuthController@login',
        '/logout' => 'Auth\\AuthController@logout',
        '/register' => 'Auth\\AuthController@register',
        '/wallets' => 'WalletController@store',
        '/wallets/update' => 'WalletController@update',
        '/transactions/store' => 'TransactionController@store',
        '/transactions/update' => 'TransactionController@update', // Update transaction
        '/transactions/delete' => 'TransactionController@delete', // Delete transaction
        '/api/categories/add' => 'CategoryController@store', // API endpoint to add category
        '/api/categories/update' => 'CategoryController@update', // API endpoint to update category
        '/api/categories/delete' => 'CategoryController@delete', // API endpoint to delete category
        '/api/budgets/add' => 'BudgetController@store', // API endpoint for budget creation
        '/api/budgets/update' => 'BudgetController@update', // API endpoint for budget updates via POST
        '/api/budgets/delete' => 'BudgetController@delete', // API endpoint for budget deletion via POST
        '/goals/store' => 'GoalsController@store', // Create new goal
        '/goals/update' => 'GoalsController@update', // Update goal
        '/goals/delete' => 'GoalsController@delete', // Delete goal
        '/api/goals/delete' => 'GoalsController@delete', // API endpoint to delete goal (alternative route)
        '/subscription/store' => 'SubscriptionController@store', // Create new subscription
        '/subscription/update' => 'SubscriptionController@update', // Update subscription
        '/subscription/delete' => 'SubscriptionController@delete', // Delete subscription
        '/users/update' => 'UserController@update', // Update user profile
        '/users/delete-photo' => 'UserController@deletePhoto', // Delete user profile photo
    ],
    'PUT' => [
        '/wallets/update' => 'WalletController@updatePut',
        '/api/budgets/update' => 'BudgetController@update', // API endpoint for budget updates
    ],
    'DELETE' => [
        '/wallets/delete' => 'WalletController@delete',
        '/api/budgets/delete' => 'BudgetController@delete', // API endpoint for budget deletion
    ],
];
