<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
use App\Models\SupportTicket;
use App\Models\Conversation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Admin dashboard
     */
    public function index()
    {
        $userCount = User::count();
        $landlordCount = User::where('role', 'landlord')->count();
        $tenantCount = User::where('role', 'tenant')->count();
        $propertyCount = Property::count();
        $activePropertyCount = Property::where('status', 'active')->count();
        $pendingPropertyCount = Property::where('status', 'pending')->count();
        $ticketCount = SupportTicket::where('status', 'open')->count();
        $messageCount = Conversation::count();
        $recentUsers = User::latest()->take(5)->get();
        $recentProperties = Property::with('user')->latest()->take(5)->get();
        
        return view('admin.dashboard', compact(
            'userCount',
            'landlordCount',
            'tenantCount',
            'propertyCount',
            'activePropertyCount',
            'pendingPropertyCount',
            'ticketCount',
            'messageCount',
            'recentUsers',
            'recentProperties'
        ));
    }

    /**
     * Get statistics for charts
     */
    public function stats()
    {
        $usersByMonth = User::selectRaw('COUNT(*) as count, MONTH(created_at) as month')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->get();
            
        $propertiesByMonth = Property::selectRaw('COUNT(*) as count, MONTH(created_at) as month')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->get();

        return response()->json([
            'users' => $usersByMonth,
            'properties' => $propertiesByMonth,
        ]);
    }

    /**
     * User report
     */
    public function userReport(Request $request)
    {
        $users = User::query();
        
        if ($request->filled('role')) {
            $users->where('role', $request->role);
        }
        
        if ($request->filled('date_from')) {
            $users->whereDate('created_at', '>=', $request->date_from);
        }
        
        $users = $users->latest()->paginate(50);
        
        return view('admin.reports.users', compact('users'));
    }

    /**
     * Property report
     */
    public function propertyReport(Request $request)
    {
        $properties = Property::with('user');
        
        if ($request->filled('status')) {
            $properties->where('status', $request->status);
        }
        
        $properties = $properties->latest()->paginate(50);
        
        return view('admin.reports.properties', compact('properties'));
    }

    /**
     * Payment report
     */
    public function paymentReport()
    {
        $payments = \App\Models\Subscription::with('user')
            ->latest()
            ->paginate(50);
            
        $totalRevenue = \App\Models\Subscription::where('is_active', true)->sum('amount');
        
        return view('admin.reports.payments', compact('payments', 'totalRevenue'));
    }
}