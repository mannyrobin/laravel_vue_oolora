@include('layouts.header')


<div style="width: 200px;" class="mr-3">
    <img class="img-fluid" src="{{ config('settings.logo_dark') }}" alt="{{ config('app.name') }}">
</div>


<table class="table table-borderless table-xs">
    <tbody>
        <tr>
            <td class="text-right"><h2 class="text-uppercase font-weight-bold mb-0">Invoice</h2></td>
        </tr>
        <tr>
            <td class="text-right py-0">Number #: {{ $invoice->invoice_number }}</td>
        </tr>
        <tr>
            <td class="text-right py-0">Date: {{ $invoice->created_at }}</td>
        </tr>
        <tr>
            <td align="right" class="pt-0"><span class="badge badge-success">Paid via {{ ($invoice->payment_gateway === 'Stripe') ? "Credit Card" : $invoice->payment_gateway }}</span></td>
        </tr>

    </tbody>
</table>


<table class="table table-borderless table-xs my-4">
    <thead>
        <tr>
            <th scope="col" class="text-uppercase border-0 font-weight-normal pl-2">Invoice From</th>
            <th scope="col" class="text-uppercase border-0 font-weight-normal">Invoice To</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="pl-2 pb-0"><strong>{{ config('app.name') }}</strong></td>
            <td class="pb-0"><strong>{{ $invoice->user->name }}</strong></td>
        </tr>
        <tr>
            <td class="pl-2 pt-0">{{ config('app.url') }}</td>
            <td class="pt-0">{{ $invoice->user->email }}</td>
        </tr>
    </tbody>
</table>


<table class="table">
    <thead>
        <tr>
            <th scope="col">Description</th>
            <th scope="col">Period</th>
            <th scope="col">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->lines as $line)
        <tr v-for="line in invoice.lines">
            <td>{{ $line->description }}</td>
            <td>{{ $line->period_start }} – {{ $line->period_end }}</td>
            <td>{{ SaaSCoreHelper::guessCurrencySymbol($invoice->currency) }}{{ $line->amount }}</td>
        </tr>
        @endforeach
    </tbody>
</table>


<table class="table ml-auto" style="width: 35%;">
    <tbody>
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">{{ SaaSCoreHelper::guessCurrencySymbol($invoice->currency) }}{{ $invoice->subtotal }}</td>
        </tr>
        <tr>
            <td class="font-weight-bold">Total:</td>
            <td class="font-weight-bold text-right">{{ SaaSCoreHelper::guessCurrencySymbol($invoice->currency) }}{{ $invoice->total }}</td>
        </tr>
    </tbody>
</table>

</body>
</html>