Agent Guidelines - Symfony PHP

## Mandatory Instructions

📋 **IMPORTANT**: Refer to [docs/agent/INSTRUCTIONS.md](docs/agent/INSTRUCTIONS.md) for specific agent instructions, including:
- **Git Workflow**: Semantic commits, versioning, branch management
- **Pull Request Management**: Standards for creating and reviewing PRs
- **Mandatory Quality Checks**: QA tools to run before any commit

These instructions are **MANDATORY** and supplement the guidelines below.

## Docker Environment
⚠️ **IMPORTANT**: This project uses Docker Compose for development. Always execute commands inside the appropriate Docker containers.

### Docker Commands
- `docker compose up -d` - Start all services in detached mode
- `docker compose down` - Stop all services
- `docker compose exec app bash` - Access the application container shell
- `docker compose exec app php bin/console [command]` - Run Symfony console commands
- `docker compose exec app composer [command]` - Run Composer commands
- `docker compose logs app` - View application logs
- `docker compose ps` - Check service status

## Project Architecture

This is a **Domain-Driven Design (DDD)** Symfony application with the following characteristics:

### Technology Stack
- **PHP**: 8.3+
- **Symfony**: 7.3.*
- **API Platform**: 4.0+ (REST API with OpenAPI)
- **Doctrine ORM**: 2.20+ with PostgreSQL
- **Messenger**: AMQP-based async processing
- **Authentication**: JWT + 2FA Email
- **File Storage**: Flysystem with S3 support
- **Frontend**: Symfony UX with Twig Components

### Architecture Patterns
- **CQRS**: Separate Command/Query buses with dedicated handlers
- **Event Sourcing**: Domain events with async processing
- **Hexagonal Architecture**: Clean separation of concerns
- **API-First**: REST API with frontend consuming it
- **ADR Pattern**: Action-Domain-Responder for controllers

## Development Methodology

### Test-Driven Development (TDD)
Follow Kent Beck's TDD methodology strictly:

#### TDD Cycle: Red → Green → Refactor
1. **Red**: Write the simplest failing test first
2. **Green**: Implement minimum code needed to make tests pass
3. **Refactor**: Improve code structure while keeping tests green

#### TDD Best Practices
- Use meaningful test names describing behavior (e.g., `shouldCreateAccountWithValidData`)
- Write just enough code to make the test pass - no more
- Run all tests after each change (except long-running tests)
- Only refactor when tests are passing
- Make test failures clear and informative

#### Example TDD Workflow
```php
// 1. RED: Write failing test
public function it_should_create_account_with_valid_email(): void
{
    $command = new CreateAccountCommand('test@example.com');
    $account = $this->handler->handle($command);
    
    expect($account->getEmail())->toBe('test@example.com');
}

// 2. GREEN: Minimal implementation
public function handle(CreateAccountCommand $command): Account
{
    return new Account($command->email);
}

// 3. REFACTOR: Improve structure (if needed)
```

### Tidy First Approach
Separate all changes into two distinct types:

#### 1. Structural Changes (Make First)
- Renaming variables, methods, classes
- Extracting methods or classes
- Moving code between files
- Reorganizing imports
- **Never change behavior**

#### 2. Behavioral Changes (Make Second)
- Adding new functionality
- Modifying existing behavior
- Fixing bugs
- **Never mix with structural changes**

#### Validation Process
- Run tests before structural changes
- Run tests after structural changes
- Ensure no behavior changed during structural modifications

## Build/Test Commands
**Note**: Execute these commands inside the Docker container (`docker compose exec app [command]`) or prefix with `docker compose exec app`

### Dependencies & Setup
- `composer install` - Install PHP dependencies
- `composer dump-autoload` - Regenerate autoloader
- `composer check-platform-reqs` - Check platform requirements
- `composer audit` - Check for security vulnerabilities

### Database Operations
- `php bin/console doctrine:database:create` - Create database
- `php bin/console doctrine:migrations:migrate` - Run database migrations
- `php bin/console doctrine:schema:validate` - Validate database schema
- `php bin/console doctrine:fixtures:load` - Load test fixtures

### Application Management
- `php bin/console cache:clear` - Clear application cache
- `php bin/console app:install` - Run application installer

### JWT & Security
- `php bin/console lexik:jwt:generate-keypair` - Generate JWT keypair
- `php bin/console security:check` - Security vulnerability check

### Testing
- `vendor/bin/phpunit` - Run unit tests
- `vendor/bin/phpspec run` - Run specification tests

### QA Tools (via Docker)
- `docker compose exec app vendor/bin/ecs check` - Code style validation (PSR-12)
- `docker compose exec app vendor/bin/ecs check --fix` - Fix code style issues
- `docker compose exec app vendor/bin/phpstan analyse src` - Static analysis
- `docker compose exec app vendor/bin/rector process --dry-run` - Code modernization analysis
- `docker compose exec app vendor/bin/phparkitect check` - Architecture validation
- `docker compose exec app vendor/bin/twig-cs-fixer lint` - Twig template linting
- `docker compose exec app php bin/console lint:twig templates` - Validate Twig syntax
- `docker compose exec app php bin/console lint:yaml config` - Validate YAML syntax

### Messenger (Async Processing)
- `php bin/console messenger:consume` - Consume messages from all transports
- `php bin/console messenger:consume sending_mail` - Consume email queue
- `php bin/console messenger:consume updating_yellow_box` - Consume YellowBox updates
- `php bin/console messenger:stats` - Show queue statistics
- `php bin/console messenger:failed:show` - Show failed messages

## Commit Discipline

### Commit Rules
Only commit when:
1. **ALL tests are passing**
2. **ALL compiler/linter warnings resolved**
3. **Change represents single logical unit**
4. **Commit message clearly states structural vs behavioral**

### Commit Types
- **Structural**: `refactor: extract method for user validation`
- **Behavioral**: `feat: add email validation to user registration`
- **Fix**: `fix: handle null email in user creation`

### Commit Frequency
- Use small, frequent commits
- Commit structural changes separately from behavioral changes
- Never mix different types of changes in same commit

## Code Style & Conventions

### PHP Standards
- Follow **PSR-12** coding standard
- Use **strict types** declaration (`declare(strict_types=1);`) in all PHP files
- Use **type hints** for all method parameters and return types
- Use **readonly properties** when appropriate (PHP 8.1+)
- Prefer **constructor property promotion** (PHP 8.0+)
- Use **match expressions** over switch when suitable (PHP 8.0+)

### Naming Conventions
- **PascalCase** for classes, interfaces, traits, and enums
- **camelCase** for methods, variables, and properties
- **snake_case** for configuration keys and database columns
- **SCREAMING_SNAKE_CASE** for constants

### Code Quality Standards
- **Eliminate duplication ruthlessly**
- **Express intent clearly** through naming and structure
- **Make dependencies explicit**
- **Keep methods small** and focused on single responsibility
- **Minimize state and side effects**
- **Use simplest solution** that could possibly work

### Documentation & Error Handling
- Add **PHPDoc blocks** for all public methods and complex logic
- Handle exceptions explicitly with **try/catch blocks**
- Use **custom exception classes** for domain-specific errors

### DDD & Architecture
- Use **dependency injection** instead of static calls
- Follow **single responsibility principle** for services
- Implement **value objects** for complex data structures
- Use **domain events** for cross-context communication
- Keep **controllers thin** - delegate to application services

## Domain-Driven Design Patterns

### CQRS Implementation
```php
// Command (Write operations)
$this->commandBus->dispatch(new CreateAccountCommand($data));

// Query (Read operations) 
$account = $this->queryBus->ask(new FindAccountQuery($id));
```

### Event Handling
```php
// Domain events are automatically dispatched
$account->edit($newData); // Triggers AccountWasEdited event
$this->eventBus->dispatch($account->getEvents());
```

### Repository Pattern
- Use **custom repositories** for complex queries
- Implement **specification pattern** for business rules
- Separate **read/write models** when appropriate

## Refactoring Guidelines

### When to Refactor
- **Only when tests are passing** (Green phase)
- After implementing new functionality
- When code duplication is identified
- When clarity can be improved

### Refactoring Process
1. **Make one refactoring change at a time**
2. **Run tests after each refactoring step**
3. **Use established refactoring patterns** with proper names
4. **Prioritize removing duplication** and improving clarity

### Common Refactoring Patterns
- **Extract Method**: Break down long methods
- **Extract Class**: Separate responsibilities
- **Rename**: Improve clarity of intent
- **Move Method**: Better organize responsibilities

### PostgreSQL Features
- **JSONB** support with custom functions
- **Custom DQL functions**: `JSONB_EXTRACT_TEXT`, `DAY`, `YEAR`, `MONTH`, `CAST`
- **UUID** primary keys with `Types::GUID`
- **Custom data types** for domain modeling

### Migrations & Fixtures
- Use **Doctrine migrations** for database changes
- **Foundry** for test data generation with domain-specific factories
- **Fixtures** organized by bounded context

### API Features
- **OpenAPI documentation** auto-generated
- **JWT authentication** with refresh tokens via Lexik bundle
- **CORS** support for frontend integration
- **Multiple formats**: JSON, JSON:API, JSON-LD, multipart
- **Custom operations** with dedicated controllers

### CQRS Buses
- **command.bus**: Write operations with logging middleware
- **query.bus**: Read operations with logging middleware
- **event.bus**: Domain events with `allow_no_handlers`

### Test Types
- **PHPSpec**: Specification/behavior tests for domain logic
- **PHPUnit**: Unit tests for application services
- **Behat**: Acceptance tests organized by bounded context

### Test Environment
- **Separate test database** with `_test` suffix
- **Synchronous message processing** with `sync://` transports
- **VCR mocking** for external API calls
- **Foundry factories** for consistent test data


### Language Requirements
- **ALL documentation in `docs/` directory MUST be written in English**
- This includes:
    - Architecture documentation
    - API documentation
    - Developer guides
    - Technical specifications
    - README files within docs/
- **Exception**: User-facing documentation in French is acceptable only in specific user guide directories
- **Rationale**: English ensures accessibility for international developers and maintains consistency with codebase comments and technical standards
