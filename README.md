# Order Approval

A Laravel backend application demonstrating order management, approval workflows, order history, service-layer architecture, and RESTful APIs.

## Overview

This project implements a simple order management workflow where orders can be created, inspected, and approved based on their current status.

The application separates request validation, business logic, persistence, and HTTP responses through dedicated Laravel components.

## Features

- Order creation
- Order details
- Order items
- Order approval workflow
- Order status history
- Service-layer business logic
- Request validation
- RESTful API endpoints
- Laravel Sanctum authentication

## Order Workflow

Orders can move through different statuses.

An order can only be approved when its current status is:

```text
pending_approval
```

When an order is approved, the status transition is recorded in the order history.

## Architecture

The application separates the main responsibilities into:

```text
HTTP Request
     │
     ▼
Controller
     │
     ▼
Form Request
     │
     ▼
Order Service
     │
     ▼
Eloquent Models
     │
     ▼
JSON Response
```

Business operations are handled through the `OrderService`, while request validation is handled separately through Form Requests.

## Domain Model

```text
Order
├── Items
└── History
```

An `Order` has many `OrderItem` records and many `OrderHistory` records.

## API Endpoints

### Create Order

```http
POST /api/orders
```

Creates a new order after validating the request.

### View Order

```http
GET /api/orders/{order}
```

Returns the order including its items and history.

### Approve Order

```http
POST /api/orders/{order}/approve
```

Approves an order when its current status is `pending_approval`.

### View Order History

```http
GET /api/orders/{order}/history
```

Returns the complete status history of the order.

## Installation

Install the dependencies:

```bash
composer install
```

Create the environment file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Run the migrations:

```bash
php artisan migrate
```

## Run the Application

```bash
php artisan serve
```

The application will be available at:

```text
http://127.0.0.1:8000
```

## Testing

Run the test suite with:

```bash
php artisan test
```

## Technologies

- PHP
- Laravel 12
- Laravel Sanctum
- Eloquent ORM
- RESTful APIs
- PHPUnit

## Purpose

This repository demonstrates practical backend engineering concepts including:

- REST API design
- Request validation
- Service-layer architecture
- Eloquent relationships
- Business workflow handling
- State transitions
- Order history tracking
- Authentication
- Automated testing
