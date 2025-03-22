to setup project

1. composer install
2. cp .env.example .env
3. php artisan key:generate
4. php artisan migrate

run server
php artisan serve



View Order Details

    Endpoint: GET /api/orders/{order}
    Notes: Returns order information along with items and history.

Approve Order

    Endpoint: POST /api/orders/{order}/approve
    Notes: Only orders with a status of pending_approval can be approved.

View Order History

    Endpoint: GET /api/orders/{order}/history
    Notes: Shows all status changes for the order.
