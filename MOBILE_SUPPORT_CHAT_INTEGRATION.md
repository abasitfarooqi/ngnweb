# Mobile Support Chat Integration Reference

This document explains the support chat system in this codebase and how to integrate the same behaviour in iOS and Android apps for both customer and staff users.

## 1) What exists now

The support chat is already live in web portal/admin and now exposed for mobile APIs for:

- customer inbox + thread + send message + attachments
- staff inbox + thread + send message + attachments
- staff conversation status/assignment updates
- lightweight latest-message polling endpoint for near realtime updates

Core files:

- `app/Models/SupportConversation.php`
- `app/Models/SupportMessage.php`
- `app/Models/SupportAttachment.php`
- `app/Events/SupportMessageSent.php`
- `routes/channels.php`
- `app/Http/Controllers/Api/SupportConversationController.php`
- `app/Http/Controllers/Api/SupportMessageController.php`
- `app/Http/Controllers/Api/StaffSupportConversationController.php`
- `routes/api.php`

## 2) Database model and table relationships

### Main chat tables

1. `support_conversations`
   - One row per chat thread
   - Key fields:
     - `id`, `uuid`
     - `customer_auth_id` -> owner customer login
     - `service_booking_id` -> optional link to enquiry
     - `assigned_backpack_user_id` -> optional staff owner
     - `title`, `topic`, `status`
     - `last_message_at`, `first_customer_message_at`
     - `external_ai_session_id` (reserved bridge field)

2. `support_messages`
   - One row per chat message
   - Key fields:
     - `id`, `conversation_id`
     - `sender_type` (`customer`, `staff`, `system`, `ai`)
     - `sender_customer_auth_id` (if customer message)
     - `sender_user_id` (if staff message)
     - `body`, `meta`
     - `read_at_customer`, `read_at_staff`

3. `support_attachments`
   - Zero-to-many files linked to a message
   - Key fields:
     - `id`, `message_id`
     - `disk`, `path`, `original_name`, `mime`, `size`
     - `uploaded_by_customer_auth_id` or `uploaded_by_user_id`

### Related business table

4. `service_bookings`
   - Optional link into chat using `conversation_id`
   - Enables enquiry-origin chats to stay linked to service requests

## 3) Auth model for mobile

Two login paths already exist:

- Staff login:
  - `POST /api/login`
  - Returns Sanctum token for `users` table actors

- Customer login:
  - `POST /api/v1/customer/login`
  - Returns Sanctum token for `customer_auths` actors

Use `Authorization: Bearer <token>` in all mobile chat API requests.

## 4) Customer support API contract

Base prefix: `/api/v1/customer/support`
Auth: `auth:customer,sanctum`

1. List conversations
   - `GET /conversations`
   - Filters: `type`, `status`, `enquiry_id`, `search`

2. Create conversation
   - `POST /conversations`
   - Body:
     - `title` (optional)
     - `topic` (optional)
     - `service_booking_id` (optional)

3. Get one conversation
   - `GET /conversations/{uuid}`

4. List messages for thread
   - `GET /conversations/{uuid}/messages`
   - Paginated response

5. Check latest message id (poll endpoint)
   - `GET /conversations/{uuid}/latest-message`
   - Response: `{ "latest_message_id": 123 }`

6. Send message (text and/or files in one request)
   - `POST /conversations/{uuid}/messages`
   - `multipart/form-data` supported
   - Fields:
     - `body` (optional string)
     - `files[]` (optional up to 5, max 10MB each)
   - Validation: at least one of `body` or `files[]` must be present

7. Add files to existing message (legacy helper endpoint)
   - `POST /messages/{messageId}/attachments`
   - Field: `files[]`

8. Download attachment
   - `GET /attachments/{attachmentId}`

## 5) Staff support API contract

Base prefix: `/api/v1/staff/support`
Auth: `auth:sanctum` + user type check (staff only)

1. List conversations
   - `GET /conversations`
   - Filters:
     - `status`
     - `assigned_to_me` (`true`/`false`)
     - `customer_auth_id`
     - `search`
     - `per_page` (max 100)

2. Get one conversation
   - `GET /conversations/{conversationId}`

3. List messages in conversation
   - `GET /conversations/{conversationId}/messages`

4. Check latest message id (poll endpoint)
   - `GET /conversations/{conversationId}/latest-message`

5. Send message (text and/or files)
   - `POST /conversations/{conversationId}/messages`
   - `multipart/form-data`
   - Fields:
     - `body` (optional)
     - `files[]` (optional up to 5, max 10MB each)
   - Validation: at least one of `body` or `files[]` must be present

6. Update thread metadata
   - `PATCH /conversations/{conversationId}`
   - Fields:
     - `status` in `open|waiting_for_staff|waiting_for_customer|resolved|closed`
     - `assigned_backpack_user_id` (nullable)

7. Download attachment
   - `GET /attachments/{attachmentId}`

## 6) Response shape (important for app team)

### Conversation object (`SupportConversationResource`)

- `id`
- `uuid`
- `service_booking_id`
- `title`
- `topic`
- `status`
- `customer_auth_id`
- `customer_name`
- `customer_email`
- `assigned_staff_name`
- `assigned_backpack_user_id`
- `last_message_at`
- `created_at`
- `latest_message` (when loaded)

### Message object (`SupportMessageResource`)

- `id`
- `conversation_uuid`
- `sender_type`
- `sender_name`
- `body`
- `attachments[]` with:
  - `id`, `name`, `mime`, `size`
  - `api_url` (customer route for backwards compatibility)
  - `api_url_customer`
  - `api_url_staff`
  - `url` (web route)
- `created_at`
- `delivery_state` (`delivered`)

## 7) Realtime behaviour for mobile

Current reliable path is polling:

1. Keep local `last_message_id` per open thread.
2. Every 500ms to 1s (choose by battery/performance profile):
   - call latest-message endpoint
   - if id increased, call messages endpoint (or fetch next page if using cursor strategy)
3. Append/render new items.

Websocket support exists in backend (`SupportMessageSent` + private channels), but mobile should start with polling for fast and predictable rollout.

Broadcast channel names:

- `support.conversation.{uuid}`
- `support.customer.{customerAuthId}`
- `support.staff`

## 8) Message lifecycle and system behaviour

When a message is created (`SupportMessage` model event):

1. conversation timestamps and status are updated
2. assignment may auto-fill when first staff response is posted
3. broadcast event is emitted (`SupportMessageSent`)
4. first customer message can trigger staff notification

This means mobile only needs to post messages and refresh from API; server handles state transitions.

## 9) Mobile implementation checklist

### Customer app

1. Login via `/api/v1/customer/login`
2. Build inbox from `GET /api/v1/customer/support/conversations`
3. Open thread by `uuid`
4. Poll latest id + refresh messages list
5. Send text/files via multipart `POST /messages`
6. Open attachment by `api_url_customer`

### Staff app

1. Login via `/api/login`
2. Build inbox from `GET /api/v1/staff/support/conversations`
3. Filter with `assigned_to_me=true` and status tabs
4. Open thread by numeric `conversationId`
5. Poll latest id + refresh messages list
6. Send text/files via multipart `POST /messages`
7. Update status/assignment via `PATCH /conversations/{id}`
8. Open attachment by `api_url_staff`

## 10) Suggested rollout plan

1. Ship customer mobile chat first using polling.
2. Ship staff mobile inbox/thread next with assignment/status controls.
3. Add read receipts from `read_at_customer` / `read_at_staff` in a later API pass.
4. Add websocket transport only after polling telemetry is stable.

## 11) Quick request examples

### Customer send message with image

`POST /api/v1/customer/support/conversations/{uuid}/messages`

Form fields:

- `body=Hi, please check this`
- `files[]=@photo.jpg`

### Staff mark thread resolved

`PATCH /api/v1/staff/support/conversations/{id}`

JSON:

```json
{
  "status": "resolved"
}
```

### Staff reply with attachment

`POST /api/v1/staff/support/conversations/{id}/messages`

Form fields:

- `body=Thanks, issue is fixed`
- `files[]=@report.pdf`

