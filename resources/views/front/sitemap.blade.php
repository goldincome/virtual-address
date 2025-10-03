<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{route('home.index')}}/</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
    </url>
    <url>
        <loc>{{route('contact-us.index')}}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
    </url>
    <url>
        <loc>{{route('about-us.index')}}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
    </url>
    <url>
        <loc>{{route('terms-of-service.index')}}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
    </url>
    <url>
        <loc>{{route('privacy-policy.index')}}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>  
    </url>
    <url>
        <loc>{{route('virtual-address.index')}}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
    </url>
    <url>
        <loc>{{route('meeting-rooms.index')}}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>  
    </url>
    <url>
        <loc>{{route('conference-rooms.index')}}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
    </url>
    @if(!empty($plans) && $plans->count() > 0)
        @foreach($plans as $plan)
            <url>
                <loc>{{route('virtual-address.show', $plan->slug)}}</loc>
                <lastmod>{{ $plan->updated_at->toAtomString() }}</lastmod>
            </url>
        @endforeach
    @endif
    @if(!empty($meetingRooms) && $meetingRooms->count() > 0)
        @foreach($meetingRooms as $meetingRoom)
            <url>
                <loc>{{route('meeting-rooms.show', $meetingRoom->slug)}}</loc>
                <lastmod>{{ $meetingRoom->updated_at->toAtomString() }}</lastmod>
            </url>
        @endforeach
    @endif
    @if(!empty($conferenceRooms) && $conferenceRooms->count() > 0)
        @foreach($conferenceRooms as $conferenceRoom)
             <url>
                <loc>{{route('conference-rooms.show', $conferenceRoom->slug)}}</loc>
                <lastmod>{{ $conferenceRoom->updated_at->toAtomString() }}</lastmod>
            </url>
        @endforeach
    @endif

</urlset>