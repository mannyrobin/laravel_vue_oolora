@include('layouts.javascript-vars')

@if ( config('services.stripe.enable') == '1' && config('services.stripe.key') && config('services.stripe.secret') )
<script src="https://js.stripe.com/v3/"></script>
@endif
<script src="{{ asset('assets/js/app.js') }}"></script>

</body>
</html>