{{-- Hidden defaults for the quick-add CTA forms so they submit valid subscription/mail/PSC data --}}
@csrf
<input type="hidden" name="subscription_type" value="{{ $subscriptionTypes::MONTHLY->value }}">
@if($plan->mailSettings->isNotEmpty())
    <input type="hidden" name="mail_type" value="{{ $plan->mailSettings->first()->mail_type->value }}">
@endif
@if($plan->allowsCompanyPsc())
    @foreach($pscTypes as $pscType)
        <input type="hidden" name="psc[{{ $pscType->id }}]" value="0">
    @endforeach
@endif