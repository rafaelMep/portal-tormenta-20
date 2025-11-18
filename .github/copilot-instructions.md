# Portal Tormenta 20 - AI Coding Agent Instructions

This is a Laravel-Inertia.js-React application for managing Tormenta 20 RPG characters and campaigns in Portuguese.

## Architecture Overview

-   **Backend**: Laravel 12 with Inertia.js and PostgreSQL/MySQL
-   **Frontend**: React 18 + TypeScript with TailwindCSS
-   **Build**: Vite with Laravel Breeze for authentication
-   **Domain**: Tormenta 20 RPG character creation with complex race/attribute systems

## Key Domain Models

### Core RPG System

-   **Race**: Base races with variants (Suraggel → Aggelus/Sulfure)
-   **RaceChoiceSet**: Attribute selection system ("+1 to 3 different attributes")
-   **RaceChoiceGroup**: Complex choices (Golem chassis/energy/size)
-   **RaceAttributeMod**: Fixed or conditional attribute modifiers
-   **Skill**: Character skills with ability score dependencies

### Data Flow Pattern

Models use both camelCase (frontend) and snake_case (backend) properties. Frontend normalizes with helper functions like:

```typescript
function getChoiceSets(r: Race): RaceChoiceSet[] {
    return (r.choice_sets ?? r.choiceSets ?? []) as RaceChoiceSet[];
}
```

## Critical Development Workflows

### Development Server

```bash
composer run dev
```

Runs concurrent processes: Laravel serve, queue worker, logs, and Vite dev server.

### Database Management

-   Migrations in `database/migrations/` with descriptive timestamps
-   Use `2025_09_24_000100_create_races_table.php` pattern
-   Race seeders in `database/seeders/` (e.g., `GolemRaceSeeder.php`)

### Frontend Patterns

-   Pages in `resources/js/Pages/` following Laravel route structure
-   Shared types in `resources/js/types/index.d.ts`
-   Layout components in `resources/js/Layouts/`

## Project-Specific Conventions

### TypeScript Patterns

-   Use `Draft` interface for wizard state management
-   Normalize backend data with helper functions
-   Complex forms use step-by-step wizard pattern (see `Characters/Create.tsx`)

### Laravel Patterns

-   Controllers return Inertia renders with eager-loaded relationships
-   Use `Inertia::render('Player/Characters/Create', ['races' => $races])` pattern
-   Route groups: `dashboard.player.*` and `dashboard.master.*`

### Styling Conventions

-   TailwindCSS with custom utility patterns
-   Dark theme with `bg-white/[0.04]` transparency layers
-   Print styles with `.no-print` and `.print-area` classes
-   Component-scoped style blocks for print media

### Data Relationships

Race system is hierarchical:

```php
Race hasMany RaceVariant, RaceChoiceGroup, RaceChoiceSet
RaceChoiceGroup hasMany RaceChoiceOption
RaceAttributeMod belongsTo RaceChoiceOption (conditional mods)
```

## Integration Points

### Inertia.js Data Flow

-   Props passed via `Inertia::render()` in controllers
-   Type-safe with `PageProps<T>` interface
-   Route helpers via Ziggy package: `route('dashboard.player.characters.create')`

### Authentication

-   Laravel Breeze with custom `Landing` and `GuestLayout` components
-   Portuguese translations in `lang/pt_BR/`
-   Routes protected with `auth` and `verified` middleware

### State Management

-   Local state with React hooks
-   Draft persistence in `localStorage` for wizard forms
-   Complex calculations in `useMemo` hooks for performance

## Common Pitfalls

-   Race data structure varies between seeds/migrations - always use normalizer functions
-   Print styles require specific class patterns and media queries
-   Portuguese content requires proper character encoding in migrations
-   Complex choice validation requires understanding min/max choice constraints
