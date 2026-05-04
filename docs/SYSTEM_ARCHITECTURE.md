# System Architecture Documentation

## Overview

The Mould Tracking System is a comprehensive Laravel-based web application designed to track injection moulds throughout their lifecycle in manufacturing environments. The system provides real-time monitoring, preventive maintenance scheduling, and detailed reporting capabilities.

## Architecture Principles

- **MVC Pattern**: Laravel's Model-View-Controller architecture with Livewire components for reactive UI
- **Domain-Driven Design**: Organized around mould lifecycle management domain
- **Event-Driven**: Uses Laravel's event system for maintenance scheduling and notifications
- **Permission-Based Access**: Role-based authorization with granular permissions

## Core Components

### Models

#### Mould (`App\Models\Mould`)
- **Purpose**: Represents physical moulds with specifications and maintenance tracking
- **Key Fields**:
  - `code`: Unique mould identifier (string, unique)
  - `cavities`: Number of cavities (integer)
  - `status`: Current status (enum: AVAILABLE, IN_SETUP, IN_RUN, IN_MAINTENANCE, IN_TRANSIT)
  - PM intervals: `pm_interval_shot`, `pm_interval_days`
  - Counters: `total_shots`, `last_pm_at_shot`, `last_pm_at_ts`
- **Relationships**:
  - Has many `ProductionRun`
  - Has many `MaintenanceEvent`
  - Has many `LocationHistory`

#### ProductionRun (`App\Models\ProductionRun`)
- **Purpose**: Tracks individual production runs with quality metrics
- **Key Fields**:
  - `start_ts`, `end_ts`: Run timing (timestamp)
  - `cavities_snapshot`: Frozen cavity count at run start
  - Production metrics: `shot_total`, `ok_part`, `ng_part`
  - Quality data: Defects linked via `RunDefect`
- **Validation**: `ok_part + ng_part = shot_total × cavities_snapshot`

#### MaintenanceEvent (`App\Models\MaintenanceEvent`)
- **Purpose**: Records all maintenance activities (PM/CM)
- **Types**: PM (Preventive), CM (Corrective)
- **Key Fields**: `type`, `start_ts`, `end_ts`, `downtime_min`, `description`, `parts_used`, `cost`

### Livewire Components

The application uses Livewire 3 for reactive, server-side rendered components organized by feature:

- **Dashboard**: Analytics and alerts (`Dashboard/Summary.php`)
- **Moulds**: CRUD operations for mould management
- **Production**: Run management and closing
- **Maintenance**: Event logging and scheduling
- **Reports**: Various analytics and exports
- **Admin**: User and permission management

### Database Schema

#### Core Tables
- `moulds`: Mould master data with UUID primary keys
- `production_runs`: Run history with quality metrics
- `maintenance_events`: Maintenance log with work orders
- `run_defects`: Quality defect tracking per run
- `machines`: Production equipment registry
- `plants`, `zones`: Location hierarchy
- `location_histories`: Mould movement tracking

#### Key Relationships
```
moulds (1) ──── (many) production_runs
moulds (1) ──── (many) maintenance_events
production_runs (1) ──── (many) run_defects
moulds (many) ──── (1) machines (for active runs)
```

### Business Logic

#### Production Run Workflow
1. **Start**: Create run with mould + machine, snapshot cavities
2. **Monitor**: Track as active (`end_ts IS NULL`)
3. **Close**: Record shots, OK/NG parts, defects
4. **Validate**: Ensure production math consistency
5. **Update**: Reset mould status to AVAILABLE

#### Preventive Maintenance
- **Scheduling**: Based on shot count or time intervals
- **Alerts**: Dashboard notifications when due/overdue
- **Tracking**: Update next due dates on completion

#### Quality Tracking
- **Defects**: Categorized NG parts per run
- **Validation**: Sum of defect quantities = total NG parts
- **Reporting**: NG rates by mould, time period

### Security & Permissions

#### Roles
- **Admin**: Full system access + user management
- **Production**: Run management + basic reporting
- **Maintenance**: PM/CM events + equipment tracking
- **QA**: Quality reporting + defect analysis
- **Viewer**: Read-only access to all data

#### Key Permissions
- `view_maintenance_section`: Access to maintenance features
- `close_production_runs`: Ability to finalize runs
- `manage_moulds`: CRUD operations on mould master data

### API Endpoints

#### Health Check
- `GET /health`: System status monitoring
- Returns: database, application, version, environment, disk_space

#### Livewire Routes
- `/dashboard`: Main analytics dashboard
- `/moulds`: Mould listing and management
- `/runs/active`: Active production monitoring
- `/maintenance/events`: Maintenance log
- `/reports/*`: Various reporting interfaces

### Data Flow

#### Production Data Flow
```
Operator Input → Livewire Component → Validation → Database → Status Updates → Dashboard Refresh
```

#### Maintenance Scheduling
```
Mould Counters → Background Jobs → Due Date Calculations → Notification Queue → Dashboard Alerts
```

### Performance Considerations

#### Database Optimization
- UUID primary keys with proper indexing
- Composite indexes on frequently queried fields
- Active run queries optimized (`end_ts IS NULL`)
- Location-based filtering with zone/plant hierarchy

#### Caching Strategy
- Database-driven cache for sessions, views
- Queue-based background processing for reports
- Excel export generation in background

#### Monitoring
- Health endpoint for uptime monitoring
- Activity logging for audit trails
- Error logging with Laravel's exception handler

### Deployment Architecture

#### Development
- SQLite database for local development
- Vite dev server with hot reload
- Concurrent PHP server + queue worker + logs

#### Production
- MySQL/PostgreSQL for production database
- Queue workers for background processing
- SSL/TLS encryption
- Automated backups

### Testing Strategy

#### Test Coverage
- Feature tests for critical workflows
- Unit tests for business logic
- Browser tests for UI interactions (planned)

#### Key Test Areas
- Production run lifecycle validation
- Permission-based access control
- Data integrity constraints
- Excel import/export functionality

### Future Enhancements

#### Planned Features
- Mobile app integration via QR scanning
- OEE (Overall Equipment Effectiveness) calculations
- Advanced analytics with machine learning
- API integrations with ERP systems
- Real-time notifications via WebSockets

#### Scalability Considerations
- Database sharding by plant/location
- Redis caching for high-traffic deployments
- Microservices architecture for large-scale operations

---

**Document Version**: 1.0
**Last Updated**: May 2026