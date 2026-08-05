# Feature Specification: Branch Management (Super Admin)

## Overview

Implement the **Branch Management** module for the **Super Admin**.

This is the first core module of the Laundry Management System. Every branch represents a physical laundry shop and serves as the foundation of the system. Future entities such as Branch Admins, Staff, Customers, Laundry Orders, Reports, and Inventory will belong to a specific branch.

Before implementing the feature, analyze the existing project structure, reuse established patterns, and follow the project's architecture and coding conventions.

---

# Objective

Allow the Super Admin to centrally manage all laundry branches.

The Super Admin should be able to:

- View all branches
- Search branches
- Create a branch
- Update branch information
- Activate or deactivate a branch

Permanent deletion is **not** allowed to preserve historical data and maintain data integrity.

---

# Functional Requirements

## View Branches

Display all branches in a modern data table.

Each row should display:

- Branch Name
- Branch Code
- Contact Number
- Email Address
- Status
- Created Date
- Actions

---

## Search

Allow searching by:

- Branch Name
- Branch Code
- Email Address

Searching should update results efficiently without requiring a full page reload.

---

## Pagination

Implement pagination for the branch list.

---

## Create Branch

The Super Admin should be able to create a new branch.

### Branch Information

- Branch Name *(required)*
- Branch Code *(optional or auto-generated)*
- Contact Number
- Email Address
- Complete Address
- Status *(Active by default)*

### Validation

- Branch Name must be unique.
- Branch Code must be unique if provided.
- Validate all required fields using Laravel Form Requests.

After successful creation:

- Display a success toast notification.
- Refresh the branch list.
- Close the dialog automatically.

---

## Edit Branch

Allow the Super Admin to update branch information.

The edit form should reuse the same form component used during branch creation.

---

## Branch Status

Instead of deleting a branch, allow the Super Admin to:

- Activate Branch
- Deactivate Branch

Changing a branch's status should require user confirmation.

Inactive branches should remain in the system for historical records.

---

# User Interface

Design the page using a **modern SaaS dashboard** style.

The interface should feel clean, professional, and easy to use.

### Design Principles

- Use Shadcn Vue components.
- Follow modern 2026 UI design practices.
- Prioritize spacing and visual hierarchy.
- Keep layouts clean and uncluttered.
- Use cards with subtle shadows.
- Use rounded corners consistently.
- Use badges for branch status.
- Use dialogs or slide-over panels for Create/Edit actions.
- Use toast notifications for successful operations.
- Ensure the layout is responsive across desktop and tablet devices.

Avoid creating a traditional CRUD interface. The goal is to create a polished user experience similar to modern SaaS products.

---

# Components

Create reusable components whenever appropriate.

Suggested components:

- BranchTable
- BranchForm
- CreateBranchDialog
- EditBranchDialog
- BranchStatusBadge

Avoid duplicating form logic.

---

# Backend Requirements

Implement the following using Laravel best practices:

- Migration
- Model
- Resource Controller
- Form Requests
- Resource Routes
- Validation
- Authorization

Keep controllers thin and move business logic into dedicated service classes if complexity increases.

---

# Frontend Requirements

Use the project's existing technology stack:

- Vue 3
- Inertia.js
- TypeScript
- Tailwind CSS
- Shadcn Vue

Reuse existing layouts, components, and design patterns whenever possible.

---

# User Experience

The page should include:

## Loading State

Display skeleton loaders while data is loading.

## Empty State

Show a meaningful empty state when no branches exist, along with a primary action to create the first branch.

## Error State

Display clear error messages for failed requests.

## Success Feedback

Display toast notifications after successful operations.

---

# Before Implementation

Before writing code:

1. Analyze the existing project structure.
2. Identify similar implementations that can be reused.
3. Explain the implementation plan.
4. List the files that will be created or modified.
5. Explain any architectural decisions if multiple valid approaches exist.
6. Wait for approval before introducing new patterns or dependencies.

---

# Definition of Done

This feature is considered complete when:

- Super Admin can view all branches.
- Super Admin can search branches.
- Super Admin can create a branch.
- Super Admin can edit branch information.
- Super Admin can activate or deactivate a branch.
- Validation is fully implemented.
- The interface is responsive.
- The design follows a modern SaaS dashboard aesthetic.
- Components are reusable and maintainable.
- The implementation follows the project's engineering principles and development workflow.
