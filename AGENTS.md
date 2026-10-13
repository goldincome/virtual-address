# AGENTS.md

## Project

Laravel app (see `composer.json`) for virtual-address / business services, using
Laravel Cashier + Stripe for subscriptions, MySQL (`virtual_business`), and
database-backed queues. Runs on XAMPP locally.

## Local dev processes

Long-running processes cache PHP classes in memory. **Restart them after any code
change**, otherwise jobs/requests keep running the old code (a classic cause of
"undefined method" job failures):

- Web: `php artisan serve` (http://127.0.0.1:8000) or Apache (XAMPP).
- Queue worker: `php artisan queue:work`
  - After a deploy/code change: `php artisan queue:restart`, then restart the
    `queue:work` process (a plain process does not respawn itself).

## Common commands

- Run migrations: `php artisan migrate --force`
- Tests: `php artisan test`
- Sync a plan's Stripe product/prices: `php artisan plans:sync-stripe {plan?} {--all} {--check}`
  - Do NOT run `--force` twice for the same plan (it creates duplicate Stripe prices).
- Mark an order paid + (re)send the confirmation email (recovers orders stranded by
  the payment webhook/redirect race):
  - `php artisan orders:confirm-payment {order_no|id} [--invoice=in_...] [--force]`
- Report users with duplicate live subscriptions (caused by the old upgrade bug);
  report-only, makes no changes:
  - `php artisan subscriptions:find-duplicates [--user=ID] [--all]`

## Payment flow notes

- Order rows are created **after** payment, on the browser redirect
  (`PaymentController::stripeSuccess`). Stripe's `invoice.payment_succeeded`
  webhook can therefore fire **before** the order exists.
- To avoid losing the payment confirmation, both paths call
  `OrderService::confirmPayment($order, $invoiceId)`, which is idempotent per
  Stripe **invoice id** via the `payment_receipts` table (unique
  `stripe_invoice_id`). It marks the order paid and dispatches
  `SendPaymentReceiptEmail` exactly once. Renewals use a new invoice id, so they
  still each get a receipt.
- `PaymentWebhookController::findOrderFromInvoice` matches by explicit `order_no`
  metadata (invoice, invoice `subscription_details`, or the Stripe subscription's
  metadata) and only falls back to the user's latest VA order for upgrade invoices
  that carry no metadata.

## Subscription lifecycle (single "default" subscription)

All plan changes/cancellations go through `App\Services\SubscriptionChangeService`.
Never create a second subscription for an existing subscriber.

- **Upgrade** (higher `plans.level`): applied in place immediately. Only the plan
  item is swapped (`subscriptions->update` with `proration_behavior=always_invoice`);
  PSC and metered mail items are preserved. A credit-prorated invoice is generated;
  if it can't be paid automatically the user is sent to the Stripe hosted invoice
  (Stripe handles card details — the app stays PCI compliant). A local order row is
  created via `OrderService::createPlanChangeOrder`.
- **Downgrade** (lower level): scheduled with a Stripe **subscription schedule**
  (`end_behavior=release`) so Stripe swaps the plan exactly at period end. Local
  pending state lives on `subscriptions`: `subscription_schedule_id`,
  `scheduled_plan_id`, `scheduled_change_at`, `scheduled_change_type`.
  `PlanSubscription::hasScheduledChange()` / `scheduledPlan()` expose it.
- **Cancel**: at period end (`cancel()`), emails on request
  (`SendSubscriptionCancelledEmail`, stage `requested`).
- **Resume**: within grace period via `SubscriptionChangeService::resume()`.
- **Webhooks** (`PaymentWebhookController`): `customer.subscription.updated`
  reconciles a completed downgrade — clears the pending columns and, when the new
  plan no longer `allowsCompanyPsc()`, suspends the user's companies (which stops
  PSC billing). `customer.subscription.deleted` sends the second cancellation email
  (stage `ended`). `subscription_schedule.*` events are subscribed in
  `config/cashier.php` for visibility.
- **Duplicate guard**: `VirtualAddressController::store` routes any submission from a
  user with a live subscription through `SubscriptionChangeService` instead of the
  cart, so the old "buy another plan → second subscription" path is closed.
- **Direct Debit** is still a manual/stub gateway (no Stripe subscription). Such
  users are not blocked: `SubscriptionController::change` sends them to the normal
  purchase flow when they have no active Stripe subscription.
- After changing any of this, restart `queue:work` (see above) and re-run
  `php artisan test` (new tests: `SubscriptionChangeTest`, `SubscriptionWebhookTest`).

## Email templates

`resources/views/emails/CustomerOrderEmail.blade.php` is shared by the order
confirmation (`NewOrderEmail`) and the payment receipt (`PaymentReceiptEmail`,
which also attaches `pdf.invoice` and `pdf.service-agreement`). Keep it null-safe:
a brand-new subscriber has no local subscription row yet.

## Known pre-existing test failures (not related to features under change)

- `Tests\Feature\ExampleTest`
- `Tests\Feature\ProfileTest` (2 tests)
