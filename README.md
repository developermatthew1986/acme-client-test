# AcmeWidget Basket System

A Laravel-based shopping basket system with product management, delivery rules, and special offers.

## About This Project

This project demonstrates a flexible basket/shopping cart system built with Laravel. It includes:

- Product catalog management
- Dynamic delivery charge calculation based on order value
- Special offer system (e.g., Buy One Get Second Half Price)
- Comprehensive basket calculation with breakdowns

## Prerequisites

- PHP 7.4 or higher
- Composer
- MySQL/PostgreSQL or any Laravel-supported database
- Node.js and npm (for frontend assets)

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd acme-client-test
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Set up environment file**
   ```bash
   cp .env.example .env
   ```

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Configure your database**
   
   Edit the `.env` file and set your database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

7. **Run database migrations**
   ```bash
   php artisan migrate
   ```

8. **Seed the database with sample products**
   ```bash
   php artisan db:seed
   ```

## Running the Basket Demo

The project includes a demonstration command that showcases the basket system with various test cases.

### Execute the Demo Command

```bash
php artisan basket:demo
```

### What the Demo Does

The `basket:demo` command runs 4 test cases that demonstrate:

1. **Product Calculations** - Adds products to the basket and calculates subtotals
2. **Delivery Charges** - Applies delivery rules based on order value:
   - Orders $90+ get **free delivery**
   - Orders $50-$89.99 get **$2.95 delivery**
   - Orders under $50 get **$4.95 delivery**
3. **Special Offers** - Applies promotional discounts (e.g., Buy One Get Second Half Price on R01)
4. **Total Calculation** - Computes the final total including all discounts and delivery

### Expected Output

When you run the command, you'll see output similar to:

```
=== Running Tests ===

Test Case 1: B01, G01
--------------------------------------------------

  + Blue Widget (B01): $7.95
  + Green Widget (G01): $24.95

  Sub Total: $32.90

  Delivery: $4.95

  Expected Result : $37.85

  Actual Result:   $37.85

  ✓ PASS

Test Case 2: R01, R01
--------------------------------------------------

  + Red Widget (R01): $32.95
  + Red Widget (R01): $32.95

  Sub Total: $65.90

  Discount: -$16.48

  Delivery: $4.95

  Expected Result : $54.37

  Actual Result:   $54.37

  ✓ PASS

Test Case 3: R01, G01
--------------------------------------------------

  + Red Widget (R01): $32.95
  + Green Widget (G01): $24.95

  Sub Total: $57.90

  Delivery: $2.95

  Expected Result : $60.85

  Actual Result:   $60.85

  ✓ PASS

Test Case 4: B01, B01, R01, R01, R01
--------------------------------------------------

  + Blue Widget (B01): $7.95
  + Blue Widget (B01): $7.95
  + Red Widget (R01): $32.95
  + Red Widget (R01): $32.95
  + Red Widget (R01): $32.95

  Sub Total: $114.75

  Discount: -$16.48

  Delivery: $0.00

  Expected Result : $98.27

  Actual Result:   $98.27

  ✓ PASS
```

### Understanding the Test Cases

| Test Case | Items | Subtotal | Discount | Delivery | Total |
|-----------|-------|----------|----------|----------|-------|
| 1 | B01, G01 | $32.90 | $0.00 | $4.95 | $37.85 |
| 2 | R01, R01 | $65.90 | -$16.48 | $4.95 | $54.37 |
| 3 | R01, G01 | $57.90 | $0.00 | $2.95 | $60.85 |
| 4 | B01, B01, R01, R01, R01 | $114.75 | -$16.48 | $0.00 | $98.27 |

**Note:** Test Case 2 and 4 demonstrate the "Buy One Get Second Half Price" offer on Red Widgets (R01).

## Product Catalog

The system includes three sample products:

- **B01** - Blue Widget - $7.95
- **G01** - Green Widget - $24.95
- **R01** - Red Widget - $32.95

## Development

To start the development server:

```bash
php artisan serve
```

To compile frontend assets:

```bash
npm run dev
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
