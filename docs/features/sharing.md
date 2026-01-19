# Budget Sharing

## Overview

Budget sharing allows multiple users to access the same budget. This is useful for couples, families, or roommates who want to budget together.

## Key Concepts

- **Owner**: User who created the budget (full control)
- **Member**: Invited user (can view and edit, but not delete budget)
- **Invite**: Token-based invitation sent via email
- **Pending**: Invite sent but not yet accepted

## Code Locations

### Controllers
- `app/Http/Controllers/SharingController.php` - Invite, accept, remove members

### Pages
- `resources/js/Pages/Settings/Sharing/Index.vue` - Manage sharing, invite users
- `resources/js/Pages/Settings/Sharing/PendingInvites.vue` - Accept pending invites

### Models
- `app/Models/Invite.php` - Pending invitations
- `app/Models/Budget.php` - `users` relationship via `budget_users` pivot

## User Workflows

### Invite Someone to Budget
1. Go to Settings → Sharing
2. See current members list
3. Enter email address to invite
4. Tap "Invite"
5. System sends email with invite link
6. Invite appears in "Pending" section

### Accept an Invite
1. Receive invite email with link
2. Click link or go to app
3. If logged in: See invite details, tap "Accept"
4. If not logged in: Create account or sign in, then accept
5. Budget now appears in your budget list

### Decline an Invite
1. See pending invite
2. Tap "Decline"
3. Invite removed, no access granted

### Remove a Member
1. Go to Settings → Sharing
2. Find member in list
3. Tap "Remove"
4. Member loses access immediately
5. Their transactions remain (attributed to them)

### View Pending Invites (Invitee)
1. Go to Settings → Pending Invites
2. See invites waiting for you
3. Accept or decline each

## Data Model

### budget_users Pivot Table
```
budget_id  | user_id | role   | invited_at | accepted_at
-----------|---------|--------|------------|------------
1          | 1       | owner  | null       | null
1          | 2       | member | 2026-01-10 | 2026-01-11
```

### invites Table
```
id | budget_id | email | invited_by | token | accepted_at
---|-----------|-------|------------|-------|------------
1  | 1         | a@b.c | 1          | abc123| null
```

## Roles and Permissions

### Owner
- Full access to all budget features
- Can invite/remove members
- Can delete budget
- Can transfer ownership (future feature)

### Member
- View and edit all budget data
- Add/edit transactions
- Modify categories and accounts
- Cannot delete the budget
- Cannot remove the owner

## Invite Flow

```
Owner invites email
       ↓
System creates Invite record with token
       ↓
Email sent to invitee
       ↓
Invitee clicks link
       ↓
If no account: Create account first
       ↓
Accept invite
       ↓
budget_users record created (role: member)
Invite marked accepted
       ↓
Budget appears in invitee's list
```

## Design Decisions

### Why Token-Based Invites
**Decision**: Invites use unique tokens in URLs

**Reasoning**:
- Works for new and existing users
- Secure (can't guess invite links)
- Email serves as verification
- Token can expire (future enhancement)

### Why Roles Are Simple (Owner/Member)
**Decision**: Only two roles instead of granular permissions

**Reasoning**:
- Simple mental model
- Most users are equals in shared budget
- Owner just has admin privileges
- Can expand later if needed

### Why Transactions Show Creator
**Decision**: Transactions track `created_by` user

**Reasoning**:
- Accountability in shared budgets
- Can see who made each entry
- Useful for discussions/reviews
- Supports potential future audit log

## Real-Time Sync (Future)

Currently, shared budget changes require page refresh to see updates from other users. Future enhancement could add:
- WebSocket updates for real-time sync
- Notifications when others make changes
- Conflict resolution for simultaneous edits

## Related Features

- [Transactions](transactions.md) - Shared transactions show creator
- [Budget View](budget-view.md) - All members see same budget data
