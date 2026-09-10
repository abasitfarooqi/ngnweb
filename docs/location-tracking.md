# Dormant location tracking

This branch adds a shared tracking domain for `RentingBooking` and `FinanceApplication`.
Rental vehicle context is resolved through `renting_booking_items.motorbike_id`; finance context is resolved through `application_items.motorbike_id`.

Tracking is disabled by default with `LOCATION_TRACKING_ENABLED=false`. While disabled, the API rejects collection, the mobile adapter does not request OS permission or start a native task, and the admin page reports the dormant state.

## Tables

`tracking_sources` identifies `customer_phone` or future `vehicle_gps` sources. `tracking_locations` stores source-labelled UTC points and polymorphic agreement context. `tracking_alerts`, `tracking_recovery_modes`, and `location_sharing_events` are prepared for the health-check, recovery, and consent/audit stages.

## API

- `GET /api/v1/customer/tracking/config`
- `GET /api/v1/customer/tracking/agreements`
- `PUT /api/v1/customer/tracking/agreements/{rental|finance}/{id}/sharing`
- `POST /api/v1/customer/tracking/agreements/{rental|finance}/{id}/locations`

The customer is derived from the authenticated customer token. Customer/vehicle IDs are not trusted from the mobile payload. Only active agreements accepted by the existing model scopes are eligible.

## Admin and launch

Authorized portal administrators can inspect `/flux-admin/tracking` and open a source for Leaflet history. The health-check scheduler is registered but is a no-op while disabled. Recovery data structures are present for the next admin action stage. Enable only in staging after privacy, permission, native-location, retention, Apple, and Android reviews. Set `TRACKING_LOCATION_RETENTION_DAYS` only after the business approves a retention period. This branch does not launch tracking in production.
