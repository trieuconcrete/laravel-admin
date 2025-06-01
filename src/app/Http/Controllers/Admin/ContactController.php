<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Mail\ContactReplyMail;
use App\Notifications\ContactRepliedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Display a listing of contacts.
     */
    public function index(Request $request)
    {
        $query = Contact::query();
        
        // Search functionality
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        
        // Date filter
        if ($dateFilter = $request->get('date_filter')) {
            switch($dateFilter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;
                case 'week':
                    $query->where('created_at', '>=', now()->subDays(7));
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->subDays(30));
                    break;
            }
        }
        
        // Get statistics
        $stats = [
            'new' => Contact::where('status', 'new')->count(),
            'read' => Contact::where('status', 'read')->count(),
            'replied' => Contact::where('status', 'replied')->count(),
            'spam' => Contact::where('status', 'spam')->count(),
        ];
        
        // Paginate results
        $contacts = $query->latest()->paginate(10)->withQueryString();
        
        // If it's an AJAX request, return JSON
        if ($request->ajax()) {
            return response()->json([
                'contacts' => $contacts,
                'stats' => $stats
            ]);
        }
        
        // Return view for normal requests
        return view('admin.contacts.index', compact('contacts', 'stats'));
    }
    
    /**
     * Display the specified contact.
     */
    public function show(Request $request, Contact $contact)
    {
        // Mark as read when viewing
        $contact->markAsRead();
        
        if ($request->ajax()) {
            return response()->json($contact);
        }
        
        return view('admin.contacts.show', compact('contact'));
    }
    
    /**
     * Update contact status.
     */
    public function updateStatus(Request $request, Contact $contact)
    {
        $request->validate([
            'status' => 'required|in:new,read,replied,spam'
        ]);
        
        switch($request->status) {
            case 'read':
                $contact->markAsRead();
                break;
            case 'replied':
                $contact->markAsReplied();
                break;
            case 'spam':
                $contact->markAsSpam();
                break;
            default:
                $contact->update(['status' => $request->status]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Trạng thái đã được cập nhật'
        ]);
    }
    
    /**
     * Reply to contact.
     */
    public function reply(Request $request, Contact $contact)
    {
        $request->validate([
            'message' => 'required|string|min:10|max:5000'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Send email reply
            Mail::to($contact->email)->send(new ContactReplyMail(
                $contact, 
                $request->message,
                auth()->user()
            ));
            
            // Log the reply in admin notes
            $adminNote = sprintf(
                "[%s] %s replied:\n%s\n%s",
                now()->format('Y-m-d H:i:s'),
                auth()->user()->name,
                str_repeat('-', 40),
                $request->message
            );
            
            $contact->update([
                'admin_notes' => $contact->admin_notes 
                    ? $contact->admin_notes . "\n\n" . $adminNote 
                    : $adminNote
            ]);
            
            // Mark as replied
            $contact->markAsReplied();
            
            // Send notification to admin (optional - for tracking)
            auth()->user()->notify(new ContactRepliedNotification($contact, $request->message));
            
            // Log the activity
            Log::info('Contact reply sent', [
                'contact_id' => $contact->id,
                'admin_id' => auth()->id(),
                'recipient' => $contact->email
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Phản hồi đã được gửi thành công'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to send contact reply', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi gửi email. Vui lòng thử lại.'
            ], 500);
        }
    }
    
    /**
     * Send test reply email (for testing purposes).
     */
    public function testReply(Contact $contact)
    {
        try {
            Mail::to(auth()->user()->email)->send(new ContactReplyMail(
                $contact,
                'This is a test reply message to verify email configuration.',
                auth()->user()
            ));
            
            return response()->json([
                'success' => true,
                'message' => 'Test email sent to ' . auth()->user()->email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Bulk update contacts.
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:contacts,id',
            'action' => 'required|in:read,spam,delete'
        ]);
        
        $contacts = Contact::whereIn('id', $request->ids);
        
        switch($request->action) {
            case 'read':
                $contacts->each(function($contact) {
                    $contact->markAsRead();
                });
                $message = 'Đã đánh dấu là đã đọc';
                break;
            case 'spam':
                $contacts->update(['status' => 'spam']);
                $message = 'Đã đánh dấu là spam';
                break;
            case 'delete':
                $contacts->delete();
                $message = 'Đã xóa các mục đã chọn';
                break;
        }
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
    
    /**
     * Remove the specified contact.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Liên hệ đã được xóa'
        ]);
    }
    
    /**
     * Export contacts to CSV.
     */
    public function export(Request $request)
    {
        $contacts = Contact::query();
        
        // Apply filters if any
        if ($status = $request->get('status')) {
            $contacts->where('status', $status);
        }
        
        $contacts = $contacts->get();
        
        $csvData = "ID,Họ tên,Email,Chủ đề,Tin nhắn,Trạng thái,IP,Thời gian\n";
        
        foreach ($contacts as $contact) {
            $csvData .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s\n",
                $contact->id,
                $contact->name,
                $contact->email,
                $contact->subject,
                str_replace(["\r", "\n", ","], [' ', ' ', ';'], $contact->message),
                $contact->status,
                $contact->ip_address,
                $contact->created_at->format('Y-m-d H:i:s')
            );
        }
        
        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="contacts_' . date('YmdHis') . '.csv"');
    }
}