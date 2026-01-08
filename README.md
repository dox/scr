SCR Booking System
==================

A secure, modern web application for managing **Senior Common Room (SCR) meal bookings**, guest management, preferences, and administration within an Oxford college environment.

The system has been **rewritten from the ground up** to retain familiar functionality while significantly improving **security, robustness, maintainability, and extensibility**.

✨ Key Features
--------------

### Meal Bookings

*   Book SCR meals by date
	
*   Optional **guest bookings**
	
*   Clear presentation of dietary preferences
	
*   Support for booking cut-offs and service-specific rules
	

### User Preferences

*   Persistent dietary preferences (e.g. vegetarian, pescatarian)
	
*   Wine preferences and exclusions
	
*   Sensible defaults with user override
	

### Wine Management

*   Wine lists with:
	
	*   Personal (“My Lists”) and shared/public lists
		
	*   Counts of wines per list
		
	*   Favourite (heart) indicators
		
*   Integrated into meal bookings and administration
	

### Consent & Compliance

*   Explicit consent handling, including:
	
	*   Terms & Conditions
		
	*   Freedom of Speech obligations
		
*   Designed to support evolving regulatory and policy requirements
	

### Authentication & Security

*   LDAP-backed authentication (via **LdapRecord**)
	
*   Strong input validation and sanitisation throughout
	
*   CSRF protection
	
*   Hardened against common web vulnerabilities
	
*   No direct access to sensitive endpoints (cron jobs, admin tools, etc.)
	

### Administration

*   Manage:
	
	*   Meals
		
	*   Settings
		
	*   Users
		
	*   Wine lists
		
*   Granular logging of system actions
	
*   Designed to be usable by non-technical administrative staff
	

Architecture & Design
------------------------

*   **PHP (Object-Oriented)** backend
	
*   Clear class structure with sensible separation of concerns
	
*   MySQL database with relational integrity
	
*   JavaScript-enhanced UI where appropriate
	
*   Bootstrap-based frontend with:
	
	*   Consistent iconography
		
	*   Accessible markup
		
	*   Responsive layout
		

The system has been refactored to prioritise:

*   Readability
	
*   Testability
	
*   Long-term maintainability
	

Code Quality
---------------

*   Entire codebase reviewed using static analysis tooling
	
*   Numerous legacy issues resolved:
	
	*   Input handling
		
	*   Inconsistent naming
		
	*   Silent failures
		
*   Improved error handling and logging
	

Project Structure (High Level)
---------------------------------

Plain textANTLR4BashCC#CSSCoffeeScriptCMakeDartDjangoDockerEJSErlangGitGoGraphQLGroovyHTMLJavaJavaScriptJSONJSXKotlinLaTeXLessLuaMakefileMarkdownMATLABMarkupObjective-CPerlPHPPowerShell.propertiesProtocol BuffersPythonRRubySass (Sass)Sass (Scss)SchemeSQLShellSwiftSVGTSXTypeScriptWebAssemblyYAMLXML`   /  ├── assets/            # CSS, JS, icons, images  ├── classes/           # Core application classes  ├── includes/          # Bootstrap & shared includes  ├── ldap/              # LDAP abstraction and helpers  ├── pages/             # Application pages / views  ├── cron/              # Scheduled tasks (restricted access)  ├── logs/              # Application logs (non-public)  └── index.php          # Application entry point   `

🚀 Installation & Setup
-----------------------

> ⚠️ This application is designed for use within a **trusted institutional environment**.

### Requirements

*   PHP 8.x
	
*   MySQL / MariaDB
	
*   Apache (with .htaccess support)
	
*   LDAP directory (Active Directory compatible)
	

### Basic Setup

1.  Clone the repository
	
2.  Configure database credentials
	
3.  Configure LDAP connection details
	
4.  Ensure required directories are not web-accessible
	
5.  Import the database schema
	
6.  Configure Apache virtual host as required
	

Security Notes
-----------------

*   Sensitive directories are protected via .htaccess
	
*   Direct folder listing is disabled
	
*   Cron endpoints are not publicly accessible
	
*   User input is validated and escaped at multiple layers
	

This system assumes **defence in depth**, not trust in the UI.

Testing & QA
---------------

The system supports:

*   Manual QA testing
	
*   Stress testing of:
	
	*   Dates
		
	*   Edge-case input
		
	*   Character sets (including emoji and non-Latin characters)
		
*   Consistency checks for UI language and behaviour
	

Ongoing Development
----------------------

Recent work has focused on:

*   Security hardening
	
*   Codebase modernisation
	
*   Feature parity with legacy systems
	
*   Improving clarity for both users and administrators
	

Future improvements may include:

*   Automated tests
	
*   Enhanced reporting
	
*   Improved accessibility
	
*   Further admin tooling
	

Maintainer
-------------

**Andrew Breakspear**IT ManagerUniversity of Oxford College

Licence
----------

This project is currently intended for **internal institutional use**.Licensing terms to be defined if the project is ever released more widely.