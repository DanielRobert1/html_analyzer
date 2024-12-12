# HTML Analyzer

This is a test assignment submission by Daniel Robert Aigbe. Please find the design documentation here [here](DOCUMENTATION.md)

## Requirements

- PHP >= 8.2
- Composer
- Laravel Framework >= 11
- MySQL or another supported database
- Node.js and npm (for frontend assets)

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/DanielRobert1/html_analyzer.git
   cd html_analyzer
   ```

2. Install dependencies:

   ```bash
   composer install
   npm install
   ```

3. Copy the `.env.example` file to `.env` and update the environment variables:

   ```bash
   cp .env.example .env
   ```

4. Generate the application key:

   ```bash
   php artisan key:generate
   ```

5. Set up the database:

   - Create a database in your DBMS.
   - Update the `.env` file with your database credentials.

6. Run migrations and seed the database (if applicable):

   ```bash
   php artisan migrate --seed
   ```

7. Build frontend assets:

   ```bash
   npm run dev
   ```

8. Start the development server:

   ```bash
   php artisan serve
   ```

   The application will be available at [http://localhost:8000](http://localhost:8000).

## Features

- Upload html file for analysis

## Testing

Run the test suite:

```bash
php artisan test
```

## Deployment

1. Set up a production server with PHP, Composer, and a database.
2. Clone the repository and install dependencies:

   ```bash
   composer install --optimize-autoloader --no-dev
   npm run build
   ```

3. Configure the environment variables in the `.env` file.
4. Run migrations:

   ```bash
   php artisan migrate --force
   ```
   

## Contributing

Contributions are welcome! Please fork this repository and submit a pull request.

## License

This project is open-source and available under the [MIT License](LICENSE).
