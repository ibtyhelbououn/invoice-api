<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    // POST /api/invoices
    public function store(Request $request)
    {
        $request->validate([
            'client_name'         => 'required|string',
            'client_email'        => 'required|email',
            'client_phone'        => 'required|string',
            'due_date'            => 'required|date',
            'tax'                 => 'nullable|numeric|min:0|max:100',
            'discount'            => 'nullable|numeric|min:0|max:100',
            'items'               => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        $invoice = Invoice::create([
            'client_name'  => $request->client_name,
            'client_email' => $request->client_email,
            'client_phone' => $request->client_phone,
            'due_date'     => $request->due_date,
            'status'       => 'unpaid',
            'tax'          => $request->tax ?? 0,
            'discount'     => $request->discount ?? 0,
        ]);

        foreach ($request->items as $item) {
            $invoice->items()->create($item);
        }

        return response()->json([
            'message' => 'Invoice created successfully',
            'invoice' => $invoice->load('items'),
            'total'   => $invoice->calculateTotal(),
        ], 201);
    }

    // GET /api/invoices
    public function index()
    {
        $invoices = Invoice::with('items')->latest()->get();

        $invoices = $invoices->map(function ($invoice) {
            $invoice->total = $invoice->calculateTotal();
            return $invoice;
        });

        return response()->json($invoices);
    }

    // GET /api/invoices/{id}
    public function show($id)
    {
        $invoice = Invoice::with('items')->find($id);

        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        $invoice->total = $invoice->calculateTotal();

        return response()->json($invoice);
    }

    // PUT /api/invoices/{id}/status
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:paid,unpaid',
        ]);

        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        $invoice->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Status updated successfully',
            'invoice' => $invoice,
        ]);
    }

    // DELETE /api/invoices/{id}
    public function destroy($id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        $invoice->delete();

        return response()->json(['message' => 'Invoice deleted successfully']);
    }

    // GET /api/dashboard
    public function dashboard()
    {
        $invoices = Invoice::with('items')->get();

        $totalInvoices = $invoices->count();
        $paidCount     = $invoices->where('status', 'paid')->count();
        $unpaidCount   = $invoices->where('status', 'unpaid')->count();

        $totalRevenue = $invoices->where('status', 'paid')
            ->sum(fn($i) => $i->calculateTotal());

        $recentInvoices = Invoice::with('items')->latest()->take(5)->get()
            ->map(function ($invoice) {
                $invoice->total = $invoice->calculateTotal();
                return $invoice;
            });

        $revenueByMonth = Invoice::with('items')
            ->where('status', 'paid')
            ->get()
            ->groupBy(fn($i) => $i->created_at->format('Y-m'))
            ->map(fn($group) => round($group->sum(fn($i) => $i->calculateTotal()), 2))
            ->sortKeys();

        return response()->json([
            'total_invoices'  => $totalInvoices,
            'paid_count'      => $paidCount,
            'unpaid_count'    => $unpaidCount,
            'total_revenue'   => round($totalRevenue, 2),
            'recent_invoices' => $recentInvoices,
            'revenue_by_month' => $revenueByMonth,
        ]);
    }

    // GET /api/invoices/{id}/pdf
    public function downloadPdf($id)
    {
        $invoice = Invoice::with('items')->find($id);

        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        $invoice->total = $invoice->calculateTotal();

        $subtotal = $invoice->items->sum(fn($item) => $item->quantity * $item->unit_price);
        $discountAmount = $subtotal * $invoice->discount / 100;
        $taxAmount = ($subtotal - $discountAmount) * $invoice->tax / 100;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoice-pdf', [
            'invoice'        => $invoice,
            'subtotal'       => round($subtotal, 2),
            'discountAmount' => round($discountAmount, 2),
            'taxAmount'      => round($taxAmount, 2),
        ]);

        return $pdf->download('invoice-' . $invoice->id . '.pdf');
    }
}
