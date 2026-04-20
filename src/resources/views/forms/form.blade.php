<div class="cord-form space-y-6">
    @foreach ($fields as $field)
        {!! $field->render() !!}
    @endforeach
</div>
