<?php

namespace App\Services;

use App\Models\Assessments;
use App\Models\FeeStructure;
use App\Models\InvoiceLine;
use App\Models\Invoices;
use Illuminate\Support\Collection;

class BillingService
{
    /**
     * Calculate and generate an invoice from an assessment.
     *
     * This service handles the complex billing logic:
     * 1. Retrieves the student's enrolled subjects for the assessment term
     * 2. Loads applicable fee structure rules
     * 3. Computes tuition (subjects × rate) + additional fees
     * 4. Creates an Invoice record with all line items
     * 5. Returns the generated invoice
     */
    public function generateInvoiceFromAssessment(Assessments $assessment): Invoices
    {
        $student = $assessment->student;
        $program = $student->program;

        // Get invoice number (unique format: INV-YYYYMMDD-{count})
        $invoiceNumber = $this->generateInvoiceNumber();

        // Create the invoice record
        $invoice = Invoices::create([
            'student_id' => $student->id,
            'assessment_id' => $assessment->id,
            'invoice_number' => $invoiceNumber,
            'total_amount' => 0, // Will be calculated from line items
            'balance' => 0,
            'due_date' => now()->addDays(15),
            'status' => 'draft',
        ]);

        // Get enrolled subjects for this term
        $enrollments = $student->enrollments()
            ->where('academic_term_id', $assessment->academic_term_id)
            ->with('subject')
            ->get();

        $totalAmount = 0;

        // Create invoice lines for tuition (per subject)
        foreach ($enrollments as $enrollment) {
            $subject = $enrollment->subject;
            $units = $subject->units;
            $tuitionRate = $program->tuition_per_unit;
            $amount = $units * $tuitionRate;

            InvoiceLine::create([
                'invoice_id' => $invoice->id,
                'line_type' => 'tuition',
                'subject_id' => $subject->id,
                'description' => "Tuition: {$subject->code} ({$units} units)",
                'quantity' => $units,
                'unit_price' => $tuitionRate,
                'amount' => $amount,
            ]);

            $totalAmount += $amount;
        }

        // Apply additional fees from fee_structure
        $fees = $this->getApplicableFees($program);
        foreach ($fees as $fee) {
            $amount = $fee->per_unit
                ? $assessment->total_units * $fee->amount
                : $fee->amount;

            InvoiceLine::create([
                'invoice_id' => $invoice->id,
                'line_type' => $this->normalizeFeeType($fee->fee_type),
                'description' => $fee->fee_type,
                'quantity' => $fee->per_unit ? $assessment->total_units : null,
                'unit_price' => $fee->amount,
                'amount' => $amount,
            ]);

            $totalAmount += $amount;
        }

        // Update invoice with calculated total
        $invoice->update([
            'total_amount' => $totalAmount,
            'balance' => $totalAmount,
            'status' => 'issued',
        ]);

        return $invoice->refresh();
    }

    /**
     * Get all applicable fees for a program.
     *
     * @param  Program  $program
     * @return Collection
     */
    private function getApplicableFees($program)
    {
        return FeeStructure::where('program_id', $program->id)
            ->where('fee_type', '!=', 'tuition') // Tuition handled separately
            ->get();
    }

    /**
     * Normalize fee_type string to match invoice_line enum values.
     */
    private function normalizeFeeType(string $feeType): string
    {
        $mapping = [
            'lab_fee' => 'lab_fee',
            'lab fee' => 'lab_fee',
            'miscellaneous' => 'misc_fee',
            'misc_fee' => 'misc_fee',
            'misc fee' => 'misc_fee',
            'student activities' => 'other',
            'registration' => 'other',
        ];

        return $mapping[strtolower($feeType)] ?? 'other';
    }

    /**
     * Generate a unique invoice number.
     */
    private function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $count = Invoices::whereDate('created_at', today())->count() + 1;

        return sprintf('INV-%s-%03d', $date, $count);
    }

    /**
     * Recalculate invoice total from line items (useful for manual adjustments).
     */
    public function recalculateInvoiceTotal(Invoices $invoice): decimal
    {
        $total = $invoice->invoiceLines()
            ->sum('amount');

        $invoice->update([
            'total_amount' => $total,
            'balance' => $total - $invoice->payments()->sum('amount_paid'),
        ]);

        return $total;
    }

    /**
     * Calculate remaining balance for an invoice.
     */
    public function calculateRemainingBalance(Invoices $invoice): decimal
    {
        $paid = $invoice->payments()->sum('amount_paid');
        $balance = $invoice->total_amount - $paid;

        // Update status based on payment
        $status = match (true) {
            $balance <= 0 => 'paid',
            $balance < $invoice->total_amount => 'partial',
            default => 'unpaid',
        };

        $invoice->update([
            'balance' => max(0, $balance),
            'status' => $status,
        ]);

        return max(0, $balance);
    }

    /**
     * Check if invoice is overdue (past due date with remaining balance).
     */
    public function isInvoiceOverdue(Invoices $invoice): bool
    {
        return $invoice->due_date < today()
            && $invoice->balance > 0
            && $invoice->status !== 'paid';
    }

    /**
     * Mark invoice as overdue if applicable.
     */
    public function markOverdueIfNeeded(Invoices $invoice): void
    {
        if ($this->isInvoiceOverdue($invoice) && $invoice->status !== 'overdue') {
            $invoice->update(['status' => 'overdue']);
        }
    }
}
