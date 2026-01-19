# Budget Guy Documentation

Welcome to the Budget Guy documentation. This folder contains comprehensive documentation about the application's architecture, features, and development guidelines.

## Documentation Structure

### Getting Started
- **[INSTRUCTIONS.md](INSTRUCTIONS.md)** - Read this first! Quick start guide for developers
- **[UPDATING-DOCS.md](UPDATING-DOCS.md)** - How to keep documentation current

### Core Documentation
- **[design.md](design.md)** - Technical architecture and design patterns

### Feature Documentation
The `features/` folder contains detailed documentation for each major feature:

- **[budget-view.md](features/budget-view.md)** - Monthly budgeting, category management, move money
- **[plan-projections.md](features/plan-projections.md)** - Planning future months with projections
- **[transactions.md](features/transactions.md)** - Transaction CRUD, splits, transfers
- **[accounts.md](features/accounts.md)** - Bank account management and reconciliation
- **[categories.md](features/categories.md)** - Category groups and category management
- **[recurring.md](features/recurring.md)** - Recurring transaction automation
- **[sharing.md](features/sharing.md)** - Multi-user budget sharing

### Technical Documentation
The `technical/` folder contains implementation details:

- **[database-relationships.md](technical/database-relationships.md)** - Key model relationships and DB schema
- **[design-system.md](technical/design-system.md)** - UI color palette, components, design guidelines
- **[frontend-patterns.md](technical/frontend-patterns.md)** - Vue patterns and component structure

### Original Specifications
The `specs/` folder contains the original build specifications:

- **[budget-app-build-guide.md](specs/budget-app-build-guide.md)** - Original feature spec and wireframe guide

### Development Resources
The `dev/` folder contains interactive design resources:

- **[component-showcase.html](dev/component-showcase.html)** - Live component examples
- **[design-mockups.html](dev/design-mockups.html)** - Design option mockups

## When to Update Documentation

### Update Feature Docs When:
- Adding new features or capabilities
- Changing how existing features work
- Modifying business logic or rules
- Adding new user workflows

### Update Technical Docs When:
- Refactoring database schema
- Adding new services or patterns
- Modifying component APIs

## Documentation Guidelines

### Writing Style
- **Clear and concise** - Get to the point quickly
- **Use examples** - Show code snippets and real scenarios
- **Explain why** - Don't just document what, explain why decisions were made
- **Keep it current** - Update docs when code changes

### What to Include
- **Business context** - Why does this feature exist?
- **Technical overview** - How does it work?
- **Code locations** - Where to find the implementation
- **Trade-offs** - What alternatives were considered?

### What to Avoid
- Restating what code does (code should be self-documenting)
- Overly verbose explanations
- Documenting framework basics (link to Laravel/Vue docs instead)

## For AI Assistants

When working on this codebase:

1. **Start with INSTRUCTIONS.md** - Understand the design system and patterns
2. **Check feature docs** - Understand business logic before making changes
3. **Update as you go** - When making changes, update affected docs
4. **Ask questions** - If docs are unclear or missing, ask the user

The most valuable documentation explains:
- **Business rules** that aren't obvious from code
- **Relationships** between different parts of the system
- **Non-obvious decisions** and why they were made
- **Workflows** that span multiple files/services
