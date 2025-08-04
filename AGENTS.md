
## Agent Guidelines - Symfony PHP

This project follows a Domain-Driven Design (DDD) approach with Hexagonal Architecture and Clean Architecture principles. Key technologies include PHP 8.3+, Symfony 7.3, API Platform, Doctrine ORM, and a CQRS pattern implementation. The project utilizes event sourcing for asynchronous processing and a hexagonal architecture for clear separation of concerns. The API-first approach with ADR pattern for controllers is also a core aspect.

### Key Architectural Patterns:
- Domain-Driven Design (DDD)
- Hexagonal Architecture
- Clean Architecture
- CQRS (Command Query Responsibility Segregation)
- Event Sourcing
- API-First with ADR (Action-Domain-Responder)

### Development Methodology:
- Test-Driven Development (TDD) is strictly followed.
- Tidy First approach for refactoring.

### Docker Environment:
- Docker Compose is used for development environment setup.

### Commit Discipline:
- Semantic commits are enforced.
- Commits are categorized as Structural, Behavioral, or Fix.
- Small, frequent commits are encouraged.

### Code Quality Standards:
- PSR-12 coding standard.
- Strict types and type hints are used.
- Readonly properties and constructor property promotion are preferred.
- Emphasis on clear intent, minimal state, and simple solutions.

### Domain-Driven Design Patterns:
- Custom repositories with the specification pattern.
- Value objects for complex data structures.
- Domain events for cross-context communication.

### API Features:
- OpenAPI documentation auto-generated.
- JWT authentication with refresh tokens.
- CORS support.

### Test Types:
- PHPSpec for domain logic.
- PHPUnit for application services.
- Behat for acceptance tests.

### Command and Query Bus Abstractions:
- Implements abstract `CommandBus` and `QueryBus` interfaces for CQRS pattern.
- Concrete implementations (`MessengerCommandBus`, `MessengerQueryBus`) leverage Symfony Messenger for asynchronous processing.
- Configuration in `messenger.yaml` and `services.yaml` ensures proper routing and dependency injection.
