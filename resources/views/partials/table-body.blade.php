@foreach($rundown->segments->sortBy('order_index') as $segment)
    @include('partials.segment-row', ['segment' => $segment])
@endforeach