# Mail Setting Stripe Price Update Implementation

## Summary

Implemented automatic Stripe price updates when admin edits mail settings, following the PlanObserver pattern.

## Changes Made

### 1. Updated `MailSettingObserver@updated()` (app/Observers/MailSettingObserver.php)

**Previous behavior:**
- Attempted to update existing Stripe price via `prices->update()`
- Did not handle interval changes
- Did not repoint existing subscriber subscription items

**New behavior:**
- When `price` or `interval` is dirty:
  - Deactivates old Stripe price
  - Creates new metered Stripe price with updated amount/interval
  - Saves new `stripe_price_id` via `withoutEvents()`
  - Repoints existing subscriber metered subscription items to new price
- Follows the same pattern as `PlanObserver` for consistency

### 2. Added `repointSubscriptionItems()` method

- Finds all active subscriptions with items using the old price
- Updates subscription items to point to the new price
- Logs errors without failing the entire update

### 3. Created test suite (tests/Feature/MailSettingObserverTest.php)

Tests cover:
- Price change creates new Stripe price
- Interval change creates new Stripe price  
- Name change does not affect price
- Observer is queued (ShouldQueue)
- Admin controller integration

**Note:** Tests require valid Stripe test keys or proper mocking infrastructure. The observer is queued and creates its own StripeClient instance, making standard mocking difficult.

## How It Works

1. Admin edits mail price at `/admin/mail-settings/{id}/edit`
2. `MailSettingController@update()` saves the changes
3. Eloquent fires the `updated` event
4. `MailSettingObserver@updated()` is queued (due to `ShouldQueue`)
5. Queue worker processes the job:
   - Deactivates old Stripe price
   - Creates new metered Stripe price
   - Updates local `stripe_price_id`
   - Repoints active subscription items

## Manual Testing Steps

1. Start queue worker: `php artisan queue:work`
2. Edit a mail setting price in admin panel
3. Check Stripe dashboard:
   - Old price should be marked inactive
   - New price should be created with new amount
   - Active subscriptions should reference new price
4. Verify database: `mail_settings.stripe_price_id` should have new value

## Production Considerations

- Ensure queue worker is running (`queue:work` or scheduled `queue:restart`)
- Monitor failed jobs queue for any Stripe API errors
- Subscription item repointing may take time for large subscriber bases (limit: 100 per batch)
- Old Stripe prices are deactivated but not deleted (Stripe best practice)

## Related Files

- `app/Observers/MailSettingObserver.php` - Core implementation
- `app/Observers/PlanObserver.php` - Reference pattern
- `app/Http/Controllers/Admin/MailSettingController.php` - Admin update endpoint
- `tests/Feature/MailSettingObserverTest.php` - Test suite (requires Stripe mocking)

## Known Limitations

- Subscription item repointing is limited to 100 subscriptions per update (Stripe API pagination)
- For large-scale price changes affecting thousands of subscribers, consider batched processing
- The observer is queued, so updates are asynchronous (not truly "immediate")
