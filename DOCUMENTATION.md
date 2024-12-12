# Design Document

## Overview
The Accessibility Checker is a full-stack application designed to analyze HTML documents for WCAG compliance, focusing on accessibility issues such as missing alt attributes, skipped heading levels, unlabeled form elements, and tabable action elements.

## Architecture
The system consists of the following components:

### Backend
- Built with Laravel, it provides a RESTful API for processing HTML files and evaluating accessibility rules.

### Frontend
- Developed in React with TypeScript, it offers an intuitive interface for uploading files and visualizing accessibility results. This is tied together with the backend using Inertia.js, providing a full-stack experience.

### Deployment
- Hosted on Laravel Forge on a Linux Ubuntu 22.04 instance with PHP 8.2 and Laravel 11.

## Backend Design
### Key Features
- Accepts file uploads via an API endpoint.
- Parses HTML documents using a modular `HtmlParser` utility.
- Evaluates accessibility rules through self-contained Rule classes implementing a common `RuleInterface`.

### Core Modules
- **HtmlParserInterface**: Abstracts the HTML parsing functionality for flexibility.
- **RuleInterface**: Defines the structure for evaluating accessibility rules.

### Rules Implemented
- **AltAttributeRule**: Checks for missing `alt` attributes in `<img>` tags.
- **HeadingsRule**: Ensures proper heading structure and no skipped levels.
- **FormLabelRule**: Verifies all form inputs have associated labels.
- **TabNavigationRule**: Ensures all actionable elements can be navigated using the Tab key.

### Scoring Logic
#### Compliance Score
- Issues are categorized by severity:
  - **High** (e.g., Missing `tabindex`): -10 points per issue.
  - **Medium** (e.g., Missing `alt`): -5 points per issue.
  - **Low** (e.g., Minor label issues): -3 points per issue.
- Total score = `100 - ∑(IssueSeverity * Count)`.

### API Endpoints
- **POST /api/accessibility-analyze**: Accepts HTML files for analysis.

## Frontend Design
### Key Features
- File upload interface.
- Displays compliance score and issue breakdown.
- Highlights issues visually within the HTML preview.

### React.js Components
- **FileUploader**: Handles file selection and API interaction.
- **AccessibilityChart**: Visualizes compliance scores.
- **ResultsDisplay**: Displays scores and issues breakdown.

## Future Considerations
- Support for additional WCAG rules.
- Integration with cloud storage for larger HTML files.
- User authentication for personalized reports.
- Broader unit, feature, and integration testing for better code coverage and utility.

## Testing
- **Unit Tests**: Test individual rules (e.g., `AltAttributeRule`).
- **Feature Tests**: Validate end-to-end functionality, including file upload and results retrieval.

## Conclusion
This design emphasizes modularity and extensibility, ensuring the system remains maintainable while adhering to WCAG standards.

## Hosted Demo
You can test the live application at the following URL:
[https://html-analyzer.danielrobertaigbe.com/](https://html-analyzer.danielrobertaigbe.com/)
