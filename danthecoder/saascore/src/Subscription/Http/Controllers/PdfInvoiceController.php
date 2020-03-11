<?php

namespace DanTheCoder\SaaSCore\Subscription\Http\Controllers;

use PDF;
use SaaSCoreHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DanTheCoder\SaaSCore\Subscription\Models\Invoice;

class PdfInvoiceController extends Controller
{

    /**
     * Generate and download an invoice as PDF
     *
     * @param  Invoice ID  $id
     * @return \Illuminate\Http\Response
     */
    public function __invoke($id)
    {

        // If it's an admin allow them to download any invoice
        // Else, Restrict download only to the user it belong to
        if ( auth()->user()->hasPermissionTo('access admin') )
            $invoice = Invoice::where('id', $id)->with(['lines', 'user'])->first();
        else
            $invoice = Invoice::whereUser()->where('id', $id)->with(['lines', 'user'])->first();
        
        // Redirect if the invoice doesn't exist
        if ( $invoice === null )
            abort(404);


        $pdf = PDF::loadView('saascore::pdf.invoice', compact('invoice'));
        return $pdf->download( config('app.name') .'_'. $invoice->invoice_number .'.pdf' );
    }

}
