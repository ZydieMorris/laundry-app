# Project Context

## Project Overview

The **Laundry Shop Management System** is a web-based application designed to streamline and digitize the daily operations of a laundry business. The system enables laundry shop staff to manage customer information, create and monitor laundry orders, process payments, and notify customers when their laundry is ready for pickup.

The primary target users are **laundry shop owners and staff**, while customers benefit through timely notifications and accurate order tracking. The system addresses common business challenges such as manual record-keeping, misplaced order slips, inefficient status tracking, delayed customer communication, and difficulty monitoring daily operations.

---

# Project Type

The application is a **Web-Based Business Management Information System (MIS)** with **Order Tracking** and **Customer Notification** capabilities. It is intended for small to medium-sized laundry shops that require an efficient and centralized platform to manage laundry transactions, monitor order progress, and improve customer service.

---

# Project Objectives

- Digitize the laundry shop's daily operations to reduce reliance on paper-based records.
- Simplify customer and laundry order management through an intuitive interface.
- Provide real-time tracking of laundry orders from drop-off to pickup.
- Improve customer satisfaction by automatically notifying customers when their laundry is ready.
- Maintain accurate payment and transaction records.
- Generate operational reports that assist business owners in monitoring sales and performance.
- Build a scalable and maintainable system using modern software development practices and technologies.

---

# Functional Scope

The system includes the following core modules and features.

## User Management

- User authentication and login
- Role-based access control (Administrator and Staff)
- User profile management

---

## Customer Management

- Register new customers
- Update customer information
- Search customer records
- View customer transaction history

---

## Laundry Service Management

- Create and manage laundry services
- Configure pricing:
  - Per kilogram
  - Per item
  - Fixed price
- Enable or disable available services

---

## Laundry Order Management

- Create new laundry orders
- Assign services and calculate costs
- Generate unique order numbers
- Record expected pickup dates
- Manage order details

---

## Order Status Tracking

Track laundry progress through the following workflow:

```text
Received
    ↓
Sorting
    ↓
Washing
    ↓
Drying
    ↓
Ironing
    ↓
Ready for Pickup
    ↓
Claimed
```

---

## Payment Management

- Record payments
- Support multiple payment methods
- Track payment status:
  - Unpaid
  - Partially Paid
  - Paid
- Maintain payment history

---

## Customer Notifications

- Notify customers when laundry is ready for pickup
- Send notifications through:
  - SMS
  - Email
  - In-app notification
- Maintain notification logs

---

## Dashboard & Reports

- Display today's orders
- Monitor active and completed transactions
- Track daily revenue
- Display operational statistics
- Generate basic sales and transaction reports

---

## Receipt Generation

- Generate printable receipts
- Include:
  - Order number
  - Customer information
  - Laundry services
  - Payment summary
  - Pickup reference

---

# Technology Decisions

## Backend

- Laravel 13
- PHP

## Frontend

- Vue 3
- Inertia.js
- TypeScript
- Tailwind CSS

## Database

- SQLite

## Authentication

- Laravel Fortify

## Validation

- Laravel Form Requests

## Authorization

- Spatie Laravel Permission

## Real-Time Communication

- Laravel Reverb

## User Interface

- Shadcn Vue

## Package Manager

- npm

## Testing

- Pest
