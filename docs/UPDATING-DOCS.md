# Updating Documentation

## When to Update Documentation

### Immediate Updates Required

Update documentation **immediately** when:

1. **Adding new features**
   - Create or update relevant feature doc
   - Add to database-relationships.md if new tables
   - Update frontend-patterns.md if new UI patterns

2. **Changing business logic**
   - Update affected feature docs
   - Document why change was made

3. **Modifying database schema**
   - Update database-relationships.md
   - Update affected feature docs

4. **Changing UI components**
   - Update design-system.md if new patterns
   - Update INSTRUCTIONS.md if affects common usage

### Optional Updates

Consider updating when:

1. **Refactoring code** (no behavior change)
   - Only update if pattern changes significantly
   - Update code locations if files moved

2. **Bug fixes**
   - Update if fix reveals gap in documentation
   - Add to "Gotchas" section if edge case

## How to Update Documentation

### Feature Documentation

**Location**: `docs/features/`

**When adding new feature**:
1. Create new file: `docs/features/[feature-name].md`
2. Use existing files as template
3. Include:
   - Overview (what it does, why it exists)
   - Key concepts (business terms)
   - Code locations (controllers, pages, components)
   - User workflows
   - Design decisions (why this approach)
4. Add link to `docs/README.md`

**When modifying existing feature**:
1. Update affected section(s)
2. Note significant changes

### Technical Documentation

**Location**: `docs/technical/`

**When modifying database**:
1. Update `database-relationships.md`
   - Add/modify entity relationships
   - Update schema examples

**When changing frontend patterns**:
1. Update `frontend-patterns.md`
   - Add new pattern examples
   - Update component structure if changed

**When changing design system**:
1. Update `technical/design-system.md`
2. Update `INSTRUCTIONS.md` if affects common usage

## Documentation Templates

### Feature Doc Template

```markdown
# [Feature Name]

## Overview

Brief description of what this feature does and why it exists.

## Key Concepts

- **Term 1**: Definition
- **Term 2**: Definition

## Code Locations

### Controllers
- `app/Http/Controllers/ThingController.php` - Description

### Pages
- `resources/js/Pages/Thing/Index.vue` - Description

### Components
- `resources/js/Components/Domain/ThingCard.vue` - Description

## User Workflows

### Workflow Name
1. Step 1
2. Step 2
3. Step 3

## Design Decisions

### Why We Chose X Over Y
**Decision**: Brief statement

**Reasoning**:
- Reason 1
- Reason 2

## Gotchas / Edge Cases

- Known issue or edge case to be aware of
```

## AI Assistant Guidelines

When I (Claude) make changes to the codebase, I should:

1. **Identify affected docs**
   - Which features changed?
   - What technical aspects changed?

2. **Update immediately**
   - Don't wait for user to ask
   - Update in same session as code changes

3. **Be concise**
   - Focus on what changed
   - Don't rewrite entire doc unless major overhaul

4. **Ask questions**
   - If business logic unclear, ask user
   - If design decision not obvious, ask why

## Quality Checklist

Before finalizing doc updates:

- [ ] Accurate (reflects current code)
- [ ] Clear (easy to understand)
- [ ] Concise (no unnecessary detail)
- [ ] Complete (covers key aspects)
- [ ] Examples (shows real usage)
- [ ] Explains "why" (not just "what")
- [ ] Code locations accurate

## When Docs Diverge from Code

If you notice docs are wrong:
1. Note the discrepancy
2. Tell me (Claude) what's actually correct
3. I'll update immediately

**Example**:
> "The docs say the nav is Budget | Plan | Transactions | Accounts, but it's actually Budget | Transactions | Accounts | Plan."

I'll then update all affected docs.
