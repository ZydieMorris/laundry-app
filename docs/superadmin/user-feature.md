
````md
# Feature Specification: User Management (Super Admin)

## Overview

Implement the **User Management** module for the **Super Admin**.

The User Management module allows the Super Admin to centrally manage all user accounts within the Laundry Management System. Every user account is assigned a role, and depending on the role, may also belong to a specific branch.

This module consists of two sections:

- Users
- Roles

Before implementation, analyze the existing project structure, reuse established patterns, and follow the project's architecture and coding conventions.

---

# Objective

Allow the Super Admin to manage all system users and roles from a centralized interface.

The Super Admin should be able to:

- View all users
- Search users
- Create users
- Edit user information
- Activate or deactivate users
- Manage system roles

Permanent deletion of users is **not recommended**. Users should instead be deactivated to preserve historical records and maintain data integrity.

---

# Module Structure

```text
User Management
│
├── Users
└── Roles
```

---

# Users

## User Listing

Display all users in a modern data table.

Each row should display:

- Full Name
- Email Address
- Contact Number
- Assigned Role
- Assigned Branch
- Status
- Created Date
- Actions

---

## Search

Allow searching by:

- Full Name
- Email Address
- Contact Number

---

## Filters

Optional for MVP:

- Role
- Branch
- Status

---

## Create User

The Super Admin should be able to create new user accounts.

### User Information

- Full Name
- Email Address
- Contact Number
- Password
- Confirm Password
- Assigned Role
- Assigned Branch
- Status (Active by default)

---

## Branch Assignment

Branch assignment depends on the selected role.

### Super Admin

- Does not belong to any branch.
- Branch field should be hidden or disabled.

### Branch Admin

- Must belong to exactly one branch.

### Staff

- Must belong to exactly one branch.

### Customer

- Customer management will be implemented in a future phase.
- Customer accounts should not be created from this module for now.

The system should automatically validate whether a branch is required based on the selected role.

---

## Validation

Implement validation using Laravel Form Requests.

Examples:

- Name is required.
- Email must be unique.
- Password is required during creation.
- Branch is required for Branch Admin and Staff.
- Role is required.

---

## Edit User

Allow the Super Admin to update:

- Name
- Email
- Contact Number
- Assigned Role
- Assigned Branch
- Status

The edit form should reuse the same form component used during user creation.

---

## User Status

Instead of deleting users, allow the Super Admin to:

- Activate User
- Deactivate User

Changing a user's status should require confirmation.

Inactive users should no longer be able to authenticate.

---

# Roles

Implement a dedicated Roles page using **Spatie Laravel Permission**.

The Roles page allows the Super Admin to manage system roles.

---

## Role Listing

Display:

- Role Name
- Number of Users
- Created Date
- Actions

---

## Create Role

Allow the Super Admin to create new roles.

Initially, only the role name is required.

Permission management will be implemented in a future phase.

---

## Edit Role

Allow updating the role name.

---

## Delete Role

Allow deletion of custom roles.

### System Roles

The following roles are considered **system roles** and cannot be deleted:

- Super Admin
- Branch Admin
- Staff
- Customer

The system should prevent deletion of these roles.

---

# User Interface

Design the module using a modern SaaS dashboard style.

The interface should be clean, responsive, and easy to use.

## Design Guidelines

- Use Shadcn Vue components.
- Follow modern 2026 UI/UX practices.
- Use dialogs or slide-over panels for Create/Edit forms.
- Use badges for user status and assigned role.
- Display toast notifications for successful actions.
- Include loading, empty, and error states.
- Maintain consistent spacing and visual hierarchy.

Avoid creating traditional CRUD pages.

---

# Components

Create reusable components whenever possible.

Suggested components:

- UserTable
- UserForm
- CreateUserDialog
- EditUserDialog
- UserStatusBadge
- RoleTable
- RoleForm
- CreateRoleDialog
- EditRoleDialog

Avoid duplicating form logic.

---

# Backend Requirements

Implement using Laravel best practices.

Create:

- Resource Controller
- Form Requests
- Resource Routes
- Validation
- Authorization

Use Spatie Laravel Permission for role management.

Keep controllers thin.

Move business logic into dedicated service classes if complexity increases.

---

# Frontend Requirements

Use the project's existing technology stack.

- Vue 3
- Inertia.js
- TypeScript
- Tailwind CSS
- Shadcn Vue

Reuse existing layouts and components whenever possible.

---

# User Experience

The module should include:

## Loading State

Display skeleton loaders while data is loading.

---

## Empty State

Display meaningful empty states with a primary action.

Example:

"No users found."

"No roles found."

---

## Error State

Display clear error messages for failed operations.

---

## Success Feedback

Display toast notifications after:

- User created
- User updated
- User activated
- User deactivated
- Role created
- Role updated
- Role deleted

---

# Authorization

Only users with the **Super Admin** role may access this module.

Branch Admins, Staff, and Customers must not have access.

Unauthorized access should return a **403 Forbidden** response or follow the project's authorization strategy.

---

# Before Implementation

Before writing code:

1. Analyze the existing project structure.
2. Identify reusable patterns and components.
3. Explain the implementation plan.
4. List all files that will be created or modified.
5. Explain architectural decisions if multiple valid approaches exist.
6. Follow the project's engineering principles and development workflow.

---

# Definition of Done

The feature is complete when:

- Super Admin can view all users.
- Super Admin can search users.
- Super Admin can create users.
- Super Admin can edit users.
- Super Admin can activate or deactivate users.
- Super Admin can view all roles.
- Super Admin can create roles.
- Super Admin can edit roles.
- Super Admin can delete custom roles.
- System roles cannot be deleted.
- Branch assignment is enforced based on the selected role.
- Validation is implemented.
- The interface is responsive and follows the project's modern SaaS design system.
- Components are reusable and maintainable.
- The implementation follows the project's architecture and coding standards.
````
