<?php
/**
 * bookings.php — GET /api/bookings, POST /api/bookings/{id}/status (admin protected)
 */

function handleListBookings(): void {
    requireAdmin();
    jsonResponse(listBookings());
}

function handleUpdateBookingStatus(string $id): void {
    requireAdmin();
    $body = getJsonBody();
    $status = $body['status'] ?? '';
    $allowed = ['pending', 'confirmed', 'cancelled'];
    if (!in_array($status, $allowed, true)) {
        jsonResponse(['error' => 'Invalid status value'], 400);
    }
    updateBookingStatus($id, $status);
    jsonResponse(['success' => true]);
}
