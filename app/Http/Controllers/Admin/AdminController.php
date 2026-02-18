<?php
// app/Http/Controllers/Admin/AdminController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Identification;
use App\Models\Species;
use App\Models\Category;
use App\Models\EmailVerification;
use App\Helpers\MailHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Dompdf\Dompdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdminController extends Controller
{
    public function __construct()
    {
        // This ensures admin check even if route middleware fails
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            if (!Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access. Admin only.');
            }

            return $next($request);
        });
    }

    /**
     * Send OTP for email verification
     */
    public function sendVerificationOtp(Request $request, User $user)
    {
        try {
            // Generate OTP
            $verification = EmailVerification::generateForUser($user->id);

            // Prepare email
            $subject = 'Email Verification OTP - ' . env('APP_NAME');
            $body = MailHelper::getOtpEmailBody($user->name, $verification->otp);

            // Send email
            $result = MailHelper::sendEmail(
                $user->email,
                $user->name,
                $subject,
                $body
            );

            if ($result['success']) {
                // Store the OTP in session for verification
                session(['verification_user_id' => $user->id]);

                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully to ' . $user->email
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send OTP: ' . $result['message']
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify OTP and mark email as verified
     */
    public function verifyOtp(Request $request, User $user)
    {
        $request->validate([
            'otp' => 'required|string|size:6'
        ]);

        // Verify OTP
        if (EmailVerification::verify($user->id, $request->otp)) {
            // Mark email as verified
            $user->email_verified_at = now();
            $user->save();

            // Send welcome email
            $welcomeBody = MailHelper::getWelcomeEmailBody($user->name);
            MailHelper::sendEmail(
                $user->email,
                $user->name,
                'Welcome to ' . env('APP_NAME'),
                $welcomeBody
            );

            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid or expired OTP'
        ], 400);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request, User $user)
    {
        return $this->sendVerificationOtp($request, $user);
    }

    /**
     * Display admin dashboard
     */
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalIdentifications = Identification::count();
        $totalSpecies = Species::count();
        $totalCategories = Category::count();

        $recentUsers = User::latest()->take(5)->get();
        $recentIdentifications = Identification::with('user', 'species')
            ->latest()
            ->take(10)
            ->get();

        $topSpecies = Species::withCount('identifications')
            ->orderBy('identifications_count', 'desc')
            ->take(5)
            ->get();

        $userGrowth = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalIdentifications',
            'totalSpecies',
            'totalCategories',
            'recentUsers',
            'recentIdentifications',
            'topSpecies',
            'userGrowth'
        ));
    }

    /**
     * Display users list with filters
     */
    public function users(Request $request)
    {
        $query = User::withCount('identifications');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Apply role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Apply date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Apply sorting
        if ($request->filled('sort')) {
            switch($request->sort) {
                case 'latest':
                    $query->latest();
                    break;
                case 'oldest':
                    $query->oldest();
                    break;
                case 'name':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'identifications':
                    $query->orderBy('identifications_count', 'desc');
                    break;
                case 'identifications_desc':
                    $query->orderBy('identifications_count', 'asc');
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest(); // Default sorting
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users', compact('users'));
    }

    /**
     * Display statistics
     */
    public function statistics()
    {
        $stats = [
            'daily_identifications' => Identification::whereDate('created_at', today())->count(),
            'weekly_identifications' => Identification::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'monthly_identifications' => Identification::whereMonth('created_at', now()->month)->count(),
            'most_identified_species' => Species::withCount('identifications')
                ->orderBy('identifications_count', 'desc')
                ->first(),
            'most_active_user' => User::withCount('identifications')
                ->orderBy('identifications_count', 'desc')
                ->first(),
        ];

        return view('admin.statistics', compact('stats'));
    }

    /**
     * Show user details
     */
    public function showUser(User $user)
    {
        $user->load('identifications.species');
        $recentIdentifications = $user->identifications()->with('species')->latest()->take(10)->get();

        return view('admin.users.show', compact('user', 'recentIdentifications'));
    }

    /**
     * Show create user form
     */
    public function createUser()
    {
        return view('admin.users.create');
    }

    /**
     * Verify user email (admin action)
     */
    public function verifyEmail(Request $request, User $user)
    {
        // Mark email as verified
        $user->email_verified_at = now();
        $user->save();

        return back()->with('success', 'Email verified successfully for ' . $user->name);
    }

    /**
     * Reset user password (admin action)
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password reset successfully for ' . $user->name);
    }

    /**
     * Show edit user form
     */
    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:user,admin',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->bio = $request->bio;

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    /**
     * Delete user
     */
    public function destroyUser(User $user)
    {
        // Delete user's avatar
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Delete user's identifications (cascade will handle this if set in migration)
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

    /**
     * Store new user
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,admin',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->bio = $request->bio;

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

    /**
     * Export users based on current filters
     */
    public function exportUsers(Request $request)
    {
        // Build query with same filters as the users() method
        $query = User::withCount('identifications');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Apply role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Apply date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Apply sorting
        if ($request->filled('sort')) {
            switch($request->sort) {
                case 'latest':
                    $query->latest();
                    break;
                case 'oldest':
                    $query->oldest();
                    break;
                case 'name':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'identifications':
                    $query->orderBy('identifications_count', 'desc');
                    break;
                case 'identifications_desc':
                    $query->orderBy('identifications_count', 'asc');
                    break;
                default:
                    $query->latest();
            }
        }

        $users = $query->get();
        $format = $request->get('format', 'csv');

        switch($format) {
            case 'csv':
                return $this->exportAsCsv($users);
            case 'excel':
                return $this->exportAsExcel($users);
            case 'pdf':
                return $this->exportAsPdf($users);
            case 'docx':
                return $this->exportAsDocx($users);
            default:
                return $this->exportAsCsv($users);
        }
    }

    /**
     * Export as CSV
     */
    private function exportAsCsv($users)
    {
        $filename = 'users_export_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w+');

        // Add UTF-8 BOM for Excel compatibility
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

        // Headers
        fputcsv($handle, [
            'ID',
            'Name',
            'Email',
            'Role',
            'Status',
            'Identifications',
            'Email Verified',
            'Joined Date',
            'Bio'
        ]);

        // Data
        foreach ($users as $user) {
            fputcsv($handle, [
                $user->id,
                $user->name,
                $user->email,
                ucfirst($user->role),
                isset($user->is_active) ? ($user->is_active ? 'Active' : 'Inactive') : 'Active',
                $user->identifications_count ?? 0,
                $user->email_verified_at ? 'Yes' : 'No',
                $user->created_at->format('Y-m-d H:i:s'),
                $user->bio ?? ''
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Export as Excel using PhpSpreadsheet
     */
    private function exportAsExcel($users)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Role');
        $sheet->setCellValue('E1', 'Status');
        $sheet->setCellValue('F1', 'Identifications');
        $sheet->setCellValue('G1', 'Email Verified');
        $sheet->setCellValue('H1', 'Joined Date');
        $sheet->setCellValue('I1', 'Bio');

        // Style headers
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet->getStyle('A1:I1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF28A745');
        $sheet->getStyle('A1:I1')->getFont()->getColor()->setARGB('FFFFFFFF');

        // Add data
        $row = 2;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $user->id);
            $sheet->setCellValue('B' . $row, $user->name);
            $sheet->setCellValue('C' . $row, $user->email);
            $sheet->setCellValue('D' . $row, ucfirst($user->role));
            $sheet->setCellValue('E' . $row, isset($user->is_active) ? ($user->is_active ? 'Active' : 'Inactive') : 'Active');
            $sheet->setCellValue('F' . $row, $user->identifications_count ?? 0);
            $sheet->setCellValue('G' . $row, $user->email_verified_at ? 'Yes' : 'No');
            $sheet->setCellValue('H' . $row, $user->created_at->format('Y-m-d H:i:s'));
            $sheet->setCellValue('I' . $row, $user->bio ?? '');
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'users_export_' . date('Y-m-d_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Export as PDF using Dompdf with Blade view
     */
    private function exportAsPdf($users)
    {
        $html = view('admin.exports.users-pdf', compact('users'))->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'users_export_' . date('Y-m-d_His') . '.pdf';

        return $dompdf->stream($filename);
    }

    /**
     * Export as DOCX using PhpWord
     */
    private function exportAsDocx($users)
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Header
        $section->addTitle('Users Export Report', 1);
        $section->addText('Generated on ' . date('F j, Y \a\t g:i A'));
        $section->addTextBreak(1);

        // Summary
        $section->addText('Total Users: ' . $users->count());
        $section->addText('Admins: ' . $users->where('role', 'admin')->count());
        $section->addText('Regular Users: ' . $users->where('role', 'user')->count());

        if (request('search')) {
            $section->addText('Search Filter: "' . request('search') . '"');
        }
        if (request('role')) {
            $section->addText('Role Filter: ' . ucfirst(request('role')));
        }

        $section->addTextBreak(1);

        // Table
        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '28a745',
            'cellMargin' => 80
        ]);

        // Table headers
        $table->addRow();
        $table->addCell(500)->addText('ID', ['bold' => true]);
        $table->addCell(2000)->addText('Name', ['bold' => true]);
        $table->addCell(2500)->addText('Email', ['bold' => true]);
        $table->addCell(1000)->addText('Role', ['bold' => true]);
        $table->addCell(1000)->addText('Status', ['bold' => true]);
        $table->addCell(1200)->addText('Identifications', ['bold' => true]);
        $table->addCell(1000)->addText('Verified', ['bold' => true]);
        $table->addCell(1500)->addText('Joined', ['bold' => true]);

        // Table data
        foreach ($users as $user) {
            $table->addRow();
            $table->addCell()->addText($user->id);
            $table->addCell()->addText($user->name);
            $table->addCell()->addText($user->email);
            $table->addCell()->addText(ucfirst($user->role));
            $table->addCell()->addText(isset($user->is_active) ? ($user->is_active ? 'Active' : 'Inactive') : 'Active');
            $table->addCell()->addText($user->identifications_count ?? 0);
            $table->addCell()->addText($user->email_verified_at ? 'Yes' : 'No');
            $table->addCell()->addText($user->created_at->format('Y-m-d'));
        }

        // Footer
        $section->addTextBreak(1);
        $section->addText('Generated by ' . config('app.name'), ['size' => 8]);

        $filename = 'users_export_' . date('Y-m-d_His') . '.docx';
        $writer = IOFactory::createWriter($phpWord, 'Word2007');

        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
